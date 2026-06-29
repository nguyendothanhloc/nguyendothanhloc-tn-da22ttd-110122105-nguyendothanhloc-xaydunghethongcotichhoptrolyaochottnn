<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Enrollment;
use App\Jobs\SendEnrollmentConfirmationEmail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send enrollment confirmation notification
     *
     * @param Enrollment $enrollment
     * @return void
     */
    public function sendEnrollmentConfirmation(Enrollment $enrollment): void
    {
        try {
            // Load relationships if not already loaded
            $enrollment->load(['student.user', 'class.course']);
            
            $user = $enrollment->student->user;
            $courseName = $enrollment->class->course->name;
            $className = $enrollment->class->name;

            // Create in-app notification
            $this->createInAppNotification(
                $user,
                'enrollment_confirmation',
                'Enrollment Confirmation',
                "You have successfully enrolled in {$courseName} - {$className}. Please complete your payment to confirm your enrollment."
            );

            // Queue email job
            SendEnrollmentConfirmationEmail::dispatch($enrollment);

            Log::info("Enrollment confirmation sent", [
                'user_id' => $user->id,
                'enrollment_id' => $enrollment->id
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send enrollment confirmation", [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create an in-app notification
     *
     * @param User $user
     * @param string $type
     * @param string $title
     * @param string $message
     * @return Notification
     */
    public function createInAppNotification(User $user, string $type, string $title, string $message): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
            'sent_at' => now(),
        ]);
    }

    /**
     * Send a generic notification
     *
     * @param User $user
     * @param string $type
     * @param string $title
     * @param string $message
     * @return Notification
     */
    public function send(User $user, string $type, string $title, string $message): Notification
    {
        return $this->createInAppNotification($user, $type, $title, $message);
    }

    /**
     * Mark notification as read
     *
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);
        
        if ($notification) {
            $notification->update(['is_read' => true]);
            return true;
        }

        return false;
    }

    /**
     * Get unread notifications for a user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnreadNotifications(int $userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all notifications for a user
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserNotifications(int $userId, int $limit = 50)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
