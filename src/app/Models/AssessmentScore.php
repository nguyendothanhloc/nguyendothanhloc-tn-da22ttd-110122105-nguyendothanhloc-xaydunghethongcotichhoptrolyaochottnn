<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentScore extends Model
{
    use HasFactory;
    protected $fillable = [
        'assessment_id',
        'student_id',
        'score',
        'feedback',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    /**
     * Get the assessment that owns the score.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the student that owns the score.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
