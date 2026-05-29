<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'batch_id',
        'teacher_id',
        'subject_id',
        'title',
        'exam_code',
        'exam_type',
        'label',
        'difficulty',
        'access_type',
        'price',
        'exam_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'total_marks',
        'passing_marks',
        'negative_marks',
        'attempt_limit',
        'show_result_immediately',
        'instructions',
        'status',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'total_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'negative_marks' => 'decimal:2',
        'price' => 'decimal:2',
        'show_result_immediately' => 'boolean',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class)
            ->withPivot(['marks', 'sort_order'])
            ->withTimestamps();
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function series()
    {
        return $this->belongsToMany(ExamSeries::class, 'exam_series_exam')
            ->withPivot(['sort_order', 'unlock_rule'])
            ->withTimestamps();
    }
}
