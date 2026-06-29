<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'level',
        'interests',
    ];

    /**
     * Get the user that owns the student profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the enrollments for the student.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the classes the student is enrolled in.
     */
    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'enrollments', 'student_id', 'class_id');
    }

    /**
     * Get the attendances for the student.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the assessment scores for the student.
     */
    public function assessmentScores()
    {
        return $this->hasMany(AssessmentScore::class);
    }

    /**
     * Get the certificates for the student.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the conversations for the student.
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Get the feedbacks submitted by the student.
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
