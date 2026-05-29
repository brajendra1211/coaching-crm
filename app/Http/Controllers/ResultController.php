<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\WebsitePage;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $page = WebsitePage::where('status', 'active')
            ->where(function ($query) {
                $query->where('slug', 'results')
                    ->orWhere('page_type', 'results');
            })
            ->orderByRaw("CASE WHEN slug = 'results' THEN 0 ELSE 1 END")
            ->first();

        $exams = Exam::whereHas('results')
            ->orderByDesc('exam_date')
            ->orderBy('title')
            ->get();

        $query = ExamResult::with(['exam.subject', 'exam.batch', 'student'])
            ->where(function ($resultQuery) {
                $resultQuery->whereNull('published_at')
                    ->orWhereDate('published_at', '<=', today());
            })
            ->latest('published_at')
            ->latest();

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->integer('exam_id'));
        }

        if ($request->filled('q')) {
            $search = trim((string) $request->q);

            $query->whereHas('student', function ($studentQuery) use ($search) {
                $studentQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('student_code', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $results = $request->filled('q') || $request->filled('exam_id')
            ? $query->paginate(12)->withQueryString()
            : collect();

        $topResults = ExamResult::with(['exam', 'student'])
            ->where(function ($resultQuery) {
                $resultQuery->whereNull('published_at')
                    ->orWhereDate('published_at', '<=', today());
            })
            ->whereNotNull('rank')
            ->orderBy('rank')
            ->latest('percentage')
            ->take(8)
            ->get();

        $latestResults = ExamResult::with(['exam', 'student'])
            ->where(function ($resultQuery) {
                $resultQuery->whereNull('published_at')
                    ->orWhereDate('published_at', '<=', today());
            })
            ->latest('published_at')
            ->latest()
            ->take(6)
            ->get();

        return view('frontend.results', compact('page', 'exams', 'results', 'topResults', 'latestResults'));
    }
}
