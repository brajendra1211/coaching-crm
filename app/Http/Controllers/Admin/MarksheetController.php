<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\Request;

class MarksheetController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamResult::with(['exam.subject', 'exam.batch', 'student'])
            ->orderByDesc('published_at')
            ->latest();

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        $results = $query->paginate(15)->withQueryString();
        $exams = Exam::orderByDesc('exam_date')->orderBy('title')->get();

        return view('admin.exams.marksheets.index', compact('results', 'exams'));
    }
}
