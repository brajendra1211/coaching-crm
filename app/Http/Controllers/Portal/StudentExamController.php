<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentExamController extends Controller
{
    public function show(Exam $exam)
    {
        $student = $this->student();
        abort_unless($student && $this->canAccessExam($student, $exam), 403);

        $exam->load(['questions' => fn ($query) => $query->orderBy('exam_question.sort_order'), 'subject', 'batch']);
        $attemptsCount = ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->count();

        return view('portal.student.exams.show', compact('student', 'exam', 'attemptsCount'));
    }

    public function submit(Request $request, Exam $exam)
    {
        $student = $this->student();
        abort_unless($student && $this->canAccessExam($student, $exam), 403);

        $exam->load(['questions' => fn ($query) => $query->orderBy('exam_question.sort_order')]);

        $attemptsCount = ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->count();
        if ($attemptsCount >= (int) ($exam->attempt_limit ?: 1)) {
            throw ValidationException::withMessages([
                'exam' => 'Attempt limit reached for this exam.',
            ]);
        }

        $answers = $request->input('answers', []);

        $attempt = DB::transaction(function () use ($exam, $student, $answers) {
            $totalMarks = 0;
            $marksObtained = 0;

            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'started_at' => now(),
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);

            foreach ($exam->questions as $question) {
                $questionMarks = (float) ($question->pivot->marks ?? $question->marks ?? 0);
                $totalMarks += $questionMarks;
                $answer = $answers[$question->id] ?? null;
                $isCorrect = $question->isCorrectAnswer($answer);
                $marks = $isCorrect ? $questionMarks : 0;
                $marksObtained += $marks;

                $attempt->answers()->create([
                    'question_id' => $question->id,
                    'answer' => is_array($answer) ? implode(',', $answer) : $answer,
                    'marks' => $marks,
                    'is_correct' => $isCorrect,
                ]);
            }

            $percentage = $totalMarks > 0 ? round(($marksObtained / $totalMarks) * 100, 2) : 0;

            $attempt->update([
                'marks_obtained' => $marksObtained,
                'total_marks' => $totalMarks,
                'percentage' => $percentage,
            ]);

            ExamResult::updateOrCreate(
                ['exam_id' => $exam->id, 'student_id' => $student->id],
                [
                    'marks_obtained' => $marksObtained,
                    'total_marks' => $totalMarks,
                    'percentage' => $percentage,
                    'grade' => $this->gradeFor($percentage),
                    'result_status' => $marksObtained >= (float) ($exam->passing_marks ?? 0) ? 'pass' : 'fail',
                    'published_at' => now()->toDateString(),
                    'remarks' => 'Auto generated from student exam attempt.',
                ]
            );

            return $attempt;
        });

        return redirect()->route('student.exams.result', $attempt);
    }

    public function result(ExamAttempt $attempt)
    {
        $student = $this->student();
        abort_unless($student && (int) $attempt->student_id === (int) $student->id, 403);

        $attempt->load(['exam.subject', 'answers.question']);

        return view('portal.student.exams.result', compact('attempt'));
    }

    private function student(): ?Student
    {
        $user = Auth::user();

        if ($user->student_id) {
            return Student::with('batches')->find($user->student_id);
        }

        return Student::with('batches')
            ->where(function ($query) use ($user) {
                $query->where('email', $user->email);
                if ($user->phone) {
                    $query->orWhere('phone', $user->phone);
                }
            })
            ->first();
    }

    private function canAccessExam(Student $student, Exam $exam): bool
    {
        if (!$exam->batch_id) {
            return true;
        }

        return $student->batches()->where('batches.id', $exam->batch_id)->exists();
    }

    private function gradeFor(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            default => 'D',
        };
    }
}
