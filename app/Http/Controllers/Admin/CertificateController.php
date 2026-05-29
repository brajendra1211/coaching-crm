<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CoachingSetting;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::with('student')->latest();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('certificate_no', 'like', '%' . $search . '%')
                    ->orWhere('recipient_name', 'like', '%' . $search . '%')
                    ->orWhere('student_code', 'like', '%' . $search . '%')
                    ->orWhere('course_name', 'like', '%' . $search . '%')
                    ->orWhere('certificate_title', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('certificate_type')) {
            $query->where('certificate_type', $request->certificate_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->paginate(15)->withQueryString();

        return view('admin.certificates.index', compact('certificates'));
    }

    public function create()
    {
        $students = Student::orderBy('name')->get();

        return view('admin.certificates.create', [
            'certificate' => new Certificate([
                'certificate_title' => 'Certificate of Completion',
                'certificate_type' => 'completion',
                'issue_date' => now(),
                'template' => 'premium',
                'status' => 'active',
            ]),
            'students' => $students,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if (!empty($data['student_id'])) {
            $student = Student::find($data['student_id']);

            if ($student) {
                $data['recipient_name'] = $data['recipient_name'] ?: ($student->name ?? '');
                $data['student_code'] = $data['student_code'] ?: ($student->student_code ?? null);
                $data['course_name'] = $data['course_name'] ?: ($student->course_name ?? null);
                $data['class_level'] = $data['class_level'] ?: ($student->class_level ?? null);
            }
        }

        $data['certificate_no'] = $this->generateCertificateNo();

        $certificate = Certificate::create($data);

        return redirect()
            ->route('admin.certificates.show', $certificate)
            ->with('success', 'Certificate created successfully.');
    }

    public function show(Certificate $certificate)
    {
        $certificate->load('student');

        $setting = CoachingSetting::current();

        return view('admin.certificates.show', compact('certificate', 'setting'));
    }

    public function downloadPdf(Request $request, Certificate $certificate)
    {
        $certificate->load('student');

        $setting = CoachingSetting::current();

        $template = $request->get('template', $certificate->template ?: 'premium');

        if (!in_array($template, ['premium', 'classic', 'minimal'], true)) {
            $template = 'premium';
        }

        $logoDataUri = $this->makeFileDataUri($setting->logo ?? null);

        $pdf = Pdf::setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 120,
            ])
            ->loadView('admin.certificates.pdf', [
                'certificate' => $certificate,
                'setting' => $setting,
                'template' => $template,
                'logoDataUri' => $logoDataUri,
            ])
            ->setPaper('A4', 'landscape');

        return $pdf->download('CERTIFICATE-' . $certificate->certificate_no . '.pdf');
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->update([
            'status' => 'cancelled',
        ]);

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Certificate cancelled successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'student_id' => ['nullable', 'integer', 'exists:students,id'],

            'recipient_name' => ['required', 'string', 'max:255'],
            'student_code' => ['nullable', 'string', 'max:100'],

            'certificate_title' => ['required', 'string', 'max:255'],
            'certificate_type' => ['required', 'in:completion,participation,achievement,training,appreciation,other'],

            'course_name' => ['nullable', 'string', 'max:255'],
            'class_level' => ['nullable', 'string', 'max:255'],
            'batch_name' => ['nullable', 'string', 'max:255'],

            'issue_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date'],

            'grade' => ['nullable', 'string', 'max:100'],
            'duration' => ['nullable', 'string', 'max:100'],

            'description' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],

            'template' => ['required', 'in:premium,classic,minimal'],

            'signed_by' => ['nullable', 'string', 'max:255'],
            'signature_title' => ['nullable', 'string', 'max:255'],

            'status' => ['required', 'in:active,cancelled'],
        ]);
    }

    private function generateCertificateNo(): string
    {
        $prefix = 'CERT-' . date('Y') . '-';

        $last = Certificate::where('certificate_no', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $next = 1;

        if ($last) {
            $number = (int) str_replace($prefix, '', $last->certificate_no);
            $next = $number + 1;
        }

        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private function makeFileDataUri(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $possiblePaths = [
            public_path('storage/' . $path),
            storage_path('app/public/' . $path),
            public_path($path),
        ];

        foreach ($possiblePaths as $possiblePath) {
            if (File::exists($possiblePath)) {
                $mime = File::mimeType($possiblePath) ?: 'image/png';
                $data = base64_encode(File::get($possiblePath));

                return 'data:' . $mime . ';base64,' . $data;
            }
        }

        return null;
    }
}