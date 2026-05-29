<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoachingSetting;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class IdCardController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('student_code', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('course_name', 'like', '%' . $search . '%')
                    ->orWhere('class_level', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->paginate(15)->withQueryString();

        return view('admin.id-cards.index', compact('students'));
    }

    public function show(Request $request, Student $student)
    {
        $this->loadStudentRelations($student);

        $setting = CoachingSetting::current();

        $template = $this->normalizeTemplate($request->get('template', 'premium'));

        $cardData = $this->makeCardData($student, $setting);

        return view('admin.id-cards.show', compact(
            'student',
            'setting',
            'template',
            'cardData'
        ));
    }

    public function downloadPdf(Request $request, Student $student)
    {
        $this->loadStudentRelations($student);

        $setting = CoachingSetting::current();

        $template = $this->normalizeTemplate($request->get('template', 'premium'));

        $cardData = $this->makeCardData($student, $setting);

        $logoDataUri = $this->makeFileDataUri($setting->logo ?? null);

        $photoPath = $student->photo
            ?? $student->profile_photo
            ?? $student->image
            ?? $student->avatar
            ?? null;

        $studentPhotoDataUri = $this->makeFileDataUri($photoPath);

        $pdf = Pdf::setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 120,
            ])
            ->loadView('admin.id-cards.pdf', [
                'student' => $student,
                'setting' => $setting,
                'template' => $template,
                'cardData' => $cardData,
                'logoDataUri' => $logoDataUri,
                'studentPhotoDataUri' => $studentPhotoDataUri,
            ])
            ->setPaper('A4', 'portrait');

        $fileName = 'ID-CARD-' . ($student->student_code ?: $student->id) . '.pdf';

        return $pdf->download($fileName);
    }

    private function loadStudentRelations(Student $student): void
    {
        $relations = [];

        if (method_exists($student, 'course')) {
            $relations[] = 'course';
        }

        if (method_exists($student, 'batches')) {
            $relations[] = 'batches';
        }

        if (method_exists($student, 'parent')) {
            $relations[] = 'parent';
        }

        if (!empty($relations)) {
            $student->loadMissing($relations);
        }
    }

    private function makeCardData(Student $student, CoachingSetting $setting): array
    {
        $courseName = $student->course_name ?? null;

        if (!$courseName && method_exists($student, 'course')) {
            $courseName = optional($student->course)->title;
        }

        $batchName = $student->batch_name ?? null;

        if (!$batchName && method_exists($student, 'batches')) {
            $batchName = optional($student->batches->first())->name;
        }

        $parentName = $student->parent_name ?? $student->guardian_name ?? null;

        if (!$parentName && method_exists($student, 'parent')) {
            $parentName = optional($student->parent)->name;
        }

        return [
            'institute_name' => $setting->institute_name ?? config('app.name'),
            'tagline' => $setting->tagline ?? 'Student Identity Card',
            'phone' => $setting->phone,
            'email' => $setting->email,
            'address' => $setting->address,

            'student_name' => $student->name ?? '-',
            'student_code' => $student->student_code ?? 'STU-' . $student->id,
            'phone_number' => $student->phone ?? '-',
            'email_address' => $student->email ?? '-',
            'course_name' => $courseName ?: '-',
            'class_level' => $student->class_level ?? '-',
            'batch_name' => $batchName ?: '-',
            'parent_name' => $parentName ?: '-',
            'blood_group' => $student->blood_group ?? '-',
            'address_line' => $student->address ?? '-',
            'status' => $student->status ?? 'active',
        ];
    }

    private function normalizeTemplate(?string $template): string
    {
        $allowed = [
            'premium',
            'classic',
            'minimal',
            'compact',
        ];

        return in_array($template, $allowed, true) ? $template : 'premium';
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