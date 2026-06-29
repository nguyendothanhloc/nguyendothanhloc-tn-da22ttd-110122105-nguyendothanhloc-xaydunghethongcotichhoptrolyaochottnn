<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;
    protected $fillable = [
        'class_id',
        'name',
        'type',
        'max_score',
        'assessment_date',
        'description',
    ];

    protected $casts = [
        'max_score' => 'decimal:2',
        'assessment_date' => 'date',
    ];

    /**
     * Get the class that owns the assessment.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the scores for the assessment.
     */
    public function scores()
    {
        return $this->hasMany(AssessmentScore::class);
    }
}
