<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'language',
        'level',
        'duration_weeks',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the classes for the course.
     */
    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }

    /**
     * Get the certificates issued for the course.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
