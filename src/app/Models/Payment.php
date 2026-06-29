<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'enrollment_id',
        'amount',
        'payment_method',
        'status',
        'due_date',
        'paid_date',
        'proof_image',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    /**
     * Get the enrollment that owns the payment.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}
