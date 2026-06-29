<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'class_id',
        'date',
        'start_time',
        'end_time',
        'location',
        'topic',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the class that owns the schedule.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the attendances for the schedule.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
