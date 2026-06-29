<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'course_id',
        'certificate_number',
        'issue_date',
        'pdf_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    /**
     * Get the student that owns the certificate.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course that owns the certificate.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
