<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'class_id',
        'enrollment_date',
        'status',
        'completion_percentage',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'completion_percentage' => 'decimal:2',
    ];

    /**
     * Get the student that owns the enrollment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the class that owns the enrollment.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the payment for the enrollment.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
