<?php

namespace App\Jobs;

use App\Models\Enrollment;
use App\Mail\EnrollmentConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEnrollmentConfirmationEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Enrollment $enrollment
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Load relationships
            $this->enrollment->load(['student.user', 'class.course', 'class.teacher.user']);
            
            $user = $this->enrollment->student->user;

            // Send email
            Mail::to($user->email)->send(new EnrollmentConfirmationMail($this->enrollment));

            Log::info("Enrollment confirmation email sent", [
                'user_id' => $user->id,
                'email' => $user->email,
                'enrollment_id' => $this->enrollment->id
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send enrollment confirmation email", [
                'enrollment_id' => $this->enrollment->id,
                'error' => $e->getMessage()
            ]);
            
            // Re-throw exception to trigger job retry
            throw $e;
        }
    }
}
