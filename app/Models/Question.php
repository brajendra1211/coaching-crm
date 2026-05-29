<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'subject_id',
        'teacher_id',
        'class_level',
        'label',
        'topic',
        'source',
        'tags',
        'question_type',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'marks',
        'difficulty',
        'explanation',
        'status',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class)
            ->withPivot(['marks', 'sort_order'])
            ->withTimestamps();
    }

    public function isCorrectAnswer(?string $answer): bool
    {
        $answer = trim((string) $answer);
        $correctAnswer = trim((string) $this->correct_answer);

        if ($this->question_type === 'mcq') {
            return $answer !== '' && $answer === $correctAnswer;
        }

        if ($this->question_type === 'numeric') {
            return is_numeric($answer)
                && is_numeric($correctAnswer)
                && (float) $answer == (float) $correctAnswer;
        }

        return $answer !== ''
            && mb_strtolower($answer) === mb_strtolower($correctAnswer);
    }

    public function correctOptionText(): ?string
    {
        if ($this->question_type !== 'mcq' || !$this->correct_answer) {
            return null;
        }

        return $this->{$this->correct_answer} ?? null;
    }
}
