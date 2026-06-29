<?php

namespace App\Mail;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Enrollment $enrollment
    ) {
        // Ensure relationships are loaded
        $this->enrollment->load(['student.user', 'class.course', 'class.teacher.user']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enrollment Confirmation - ' . $this->enrollment->class->course->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.enrollment-confirmation',
            with: [
                'studentName' => $this->enrollment->student->user->name,
                'courseName' => $this->enrollment->class->course->name,
                'className' => $this->enrollment->class->name,
                'startDate' => $this->enrollment->class->start_date,
                'endDate' => $this->enrollment->class->end_date,
                'teacherName' => $this->enrollment->class->teacher->user->name,
                'enrollmentDate' => $this->enrollment->enrollment_date,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
