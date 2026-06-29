<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;
    protected $table = 'classes';

    protected $fillable = [
        'course_id',
        'teacher_id',
        'name',
        'start_date',
        'end_date',
        'max_capacity',
        'current_enrollment',
        'status',
        'shift',
        'weekdays',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the course that owns the class.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the teacher assigned to the class.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the enrollments for the class.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    /**
     * Get the schedules for the class.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    /**
     * Get the assessments for the class.
     */
    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'class_id');
    }

    /**
     * Get the students enrolled in the class.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments', 'class_id', 'student_id');
    }

    /**
     * Get the feedbacks for the class.
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'class_id');
    }
}
