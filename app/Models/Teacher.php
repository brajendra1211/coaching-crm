<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'qualification',
        'experience',
        'specialization',
        'joining_date',
        'address',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
    ];
    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_teachers')
            ->withPivot(['subject_id', 'role', 'status', 'assigned_at'])
            ->withTimestamps();
    }
}