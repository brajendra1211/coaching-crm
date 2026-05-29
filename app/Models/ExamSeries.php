<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSeries extends Model
{
    protected $table = 'exam_series';

    protected $fillable = [
        'batch_id',
        'teacher_id',
        'title',
        'series_code',
        'label',
        'difficulty',
        'access_type',
        'price',
        'start_date',
        'end_date',
        'description',
        'instructions',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_series_exam')
            ->withPivot(['sort_order', 'unlock_rule'])
            ->withTimestamps()
            ->orderBy('exam_series_exam.sort_order');
    }
}
