<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    protected $fillable = [
        'batch_id',
        'teacher_id',
        'subject_id',
        'class_level',
        'title',
        'type',
        'file_path',
        'external_link',
        'description',
        'status',
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
}
