<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\CoachingSetting;
use App\Models\FeePayment;
use App\Models\InvoiceSetting;
use App\Models\StudentFeeAssignment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FeeCollectionController extends Controller
{
    public function index(Request $request)
    {
        $query = FeePayment::with(['student', 'batch', 'assignment.feePlan'])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('receipt_no', 'like', '%' . $request->search . '%')
                    ->orWhere('payment_mode', 'like', '%' . $request->search . '%')
                    ->orWhere('transaction_id', 'like', '%' . $request->search . '%')
                    ->orWhereHas('student', function ($studentQuery) use ($request) {
                        $studentQuery->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('student_code', 'like', '%' . $request->search . '%')
                            ->orWhere('phone', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('batch', function ($batchQuery) use ($request) {
                        $batchQuery->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('code', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->paginate(15)->withQueryString();

        $batches = Batch::orderBy('name')->get();

        $todayCollection = FeePayment::whereDate('payment_date', now()->format('Y-m-d'))
            ->where('status', 'paid')
            ->sum('amount');

        $totalCollection = FeePayment::where('status', 'paid')->sum('amount');

        $pendingAmount = StudentFeeAssignment::where('status', 'active')->sum('balance_amount');

        return view('admin.fees.collections.index', compact(
            'payments',
            'batches',
            'todayCollection',
            'totalCollection',
            'pendingAmount'
        ));
    }

    public function create(Request $request)
    {
        $assignments = StudentFeeAssignment::with(['student', 'batch', 'feePlan'])
            ->whereIn('status', ['active', 'paid'])
            ->orderByDesc('id')
            ->get();

        $selectedAssignment = null;

        if ($request->filled('assignment_id')) {
            $selectedAssignment = StudentFeeAssignment::with(['student', 'batch', 'feePlan'])
                ->find($request->assignment_id);
        }

        return view('admin.fees.collections.create', compact('assignments', 'selectedAssignment'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_fee_assignment_id' => ['required', 'integer', 'exists:student_fee_assignments,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'fine_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_mode' => ['required', 'in:cash,upi,bank_transfer,card,cheque,other'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment = DB::transaction(function () use ($data) {
            $assignment = StudentFeeAssignment::with(['student', 'batch'])
                ->lockForUpdate()
                ->findOrFail($data['student_fee_assignment_id']);

            $amount = (float) ($data['amount'] ?? 0);
            $discountAmount = (float) ($data['discount_amount'] ?? 0);
            $fineAmount = (float) ($data['fine_amount'] ?? 0);

            $balanceBefore = (float) $assignment->balance_amount;
            $totalBefore = (float) $assignment->total_amount;

            $newPaidAmount = (float) $assignment->paid_amount + $amount;
            $newDiscountAmount = (float) $assignment->discount_amount + $discountAmount;
            $newFineAmount = (float) $assignment->fine_amount + $fineAmount;

            $balanceAfter = max(
                0,
                ($totalBefore + $newFineAmount) - ($newPaidAmount + $newDiscountAmount)
            );

            $payment = FeePayment::create([
                'receipt_no' => $this->generateReceiptNo(),
                'student_fee_assignment_id' => $assignment->id,
                'student_id' => $assignment->student_id,
                'batch_id' => $assignment->batch_id,
                'payment_date' => $data['payment_date'],
                'amount' => $amount,
                'discount_amount' => $discountAmount,
                'fine_amount' => $fineAmount,
                'total_before_payment' => $totalBefore,
                'balance_before_payment' => $balanceBefore,
                'balance_after_payment' => $balanceAfter,
                'payment_mode' => $data['payment_mode'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'paid',
            ]);

            $assignment->update([
                'paid_amount' => $newPaidAmount,
                'discount_amount' => $newDiscountAmount,
                'fine_amount' => $newFineAmount,
                'balance_amount' => $balanceAfter,
                'status' => $balanceAfter <= 0 ? 'paid' : 'active',
            ]);

            return $payment;
        });

        return redirect()
            ->route('admin.fee-collections.receipt', $payment)
            ->with('success', 'Fee payment received successfully.');
    }

    public function show(FeePayment $feeCollection)
    {
        return redirect()->route('admin.fee-collections.receipt', $feeCollection);
    }

    public function receipt(FeePayment $feePayment)
    {
        $feePayment->load(['student', 'batch', 'assignment.feePlan']);

        $setting = CoachingSetting::current();
        $invoiceSetting = InvoiceSetting::current();

        return view('admin.fees.collections.receipt', [
            'payment' => $feePayment,
            'setting' => $setting,
            'invoiceSetting' => $invoiceSetting,
        ]);
    }

    public function downloadPdf(Request $request, FeePayment $feePayment)
    {
        $feePayment->load(['student', 'batch', 'assignment.feePlan']);

        $setting = CoachingSetting::current();
        $invoiceSetting = InvoiceSetting::current();

        $template = $this->normalizeTemplate(
            $request->get('template', $invoiceSetting->default_template ?: 'global')
        );

        $paper = strtolower($invoiceSetting->paper_size ?: 'A4');
        $logoDataUri = $this->makeLogoDataUri($setting->logo ?? null);

        $view = 'admin.fees.collections.pdf.' . $template;

        $pdf = Pdf::setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 120,
            ])
            ->loadView($view, [
                'payment' => $feePayment,
                'setting' => $setting,
                'invoiceSetting' => $invoiceSetting,
                'template' => $template,
                'logoDataUri' => $logoDataUri,
            ])
            ->setPaper($paper, 'portrait');

        $receiptNo = $feePayment->receipt_no;
        $prefix = $invoiceSetting->invoice_prefix ?: 'RCPT';

        $fileName = str_starts_with($receiptNo, $prefix)
            ? $receiptNo . '.pdf'
            : $prefix . '-' . $receiptNo . '.pdf';

        return $pdf->download($fileName);
    }

    public function destroy(FeePayment $feeCollection)
    {
        DB::transaction(function () use ($feeCollection) {
            if ($feeCollection->status === 'void') {
                return;
            }

            $assignment = StudentFeeAssignment::lockForUpdate()
                ->find($feeCollection->student_fee_assignment_id);

            if ($assignment) {
                $newPaidAmount = max(0, (float) $assignment->paid_amount - (float) $feeCollection->amount);
                $newDiscountAmount = max(0, (float) $assignment->discount_amount - (float) $feeCollection->discount_amount);
                $newFineAmount = max(0, (float) $assignment->fine_amount - (float) $feeCollection->fine_amount);

                $balanceAmount = max(
                    0,
                    ((float) $assignment->total_amount + $newFineAmount) - ($newPaidAmount + $newDiscountAmount)
                );

                $assignment->update([
                    'paid_amount' => $newPaidAmount,
                    'discount_amount' => $newDiscountAmount,
                    'fine_amount' => $newFineAmount,
                    'balance_amount' => $balanceAmount,
                    'status' => $balanceAmount <= 0 ? 'paid' : 'active',
                ]);
            }

            $feeCollection->update(['status' => 'void']);
        });

        return redirect()
            ->route('admin.fee-collections.index')
            ->with('success', 'Payment has been voided successfully.');
    }

    private function generateReceiptNo(): string
    {
        $prefix = 'RCPT-' . date('Y') . '-';

        $last = FeePayment::where('receipt_no', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $next = 1;

        if ($last) {
            $number = (int) str_replace($prefix, '', $last->receipt_no);
            $next = $number + 1;
        }

        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private function normalizeTemplate(?string $template): string
    {
        $map = [
            'modern' => 'global',
            'classic' => 'minimal',
            'compact' => 'compact-slip',
        ];

        $template = $map[$template] ?? $template;

        $allowed = [
            'global',
            'premium',
            'letterhead',
            'minimal',
            'gst',
            'compact-slip',
        ];

        return in_array($template, $allowed, true) ? $template : 'global';
    }

    private function makeLogoDataUri(?string $logoPath): ?string
    {
        if (!$logoPath) {
            return null;
        }

        $possiblePaths = [
            public_path('storage/' . $logoPath),
            storage_path('app/public/' . $logoPath),
            public_path($logoPath),
        ];

        foreach ($possiblePaths as $path) {
            if (File::exists($path)) {
                $mime = File::mimeType($path) ?: 'image/png';
                $data = base64_encode(File::get($path));

                return 'data:' . $mime . ';base64,' . $data;
            }
        }

        return null;
    }
}