<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    protected $fillable = [
        'student_id',
        'admission_id',
        'name',
        'relation',
        'phone',
        'alternate_phone',
        'email',
        'occupation',
        'address',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }
}