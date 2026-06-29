<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'class_id',
        'course_rating',
        'teacher_rating',
        'comment',
        'is_anonymous',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    /**
     * Get the student that owns the feedback.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the class that owns the feedback.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}
