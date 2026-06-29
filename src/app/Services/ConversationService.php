<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class ConversationService
{
    /**
     * Get or create conversation for current user
     */
    public function getOrCreateConversation(): Conversation
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            throw new \Exception('Student not found');
        }
        
        // Get latest conversation or create new one
        $conversation = Conversation::where('student_id', $student->id)
            ->latest('last_message_at')
            ->first();
        
        if (!$conversation) {
            $conversation = Conversation::create([
                'student_id' => $student->id,
                'started_at' => now(),
                'last_message_at' => now(),
                'message_count' => 0
            ]);
        }
        
        return $conversation;
    }
    
    /**
     * Save user message
     */
    public function saveUserMessage(Conversation $conversation, string $message): Message
    {
        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'student',
            'content' => $message
        ]);
        
        // Update conversation
        $conversation->update([
            'last_message_at' => now(),
            'message_count' => $conversation->message_count + 1
        ]);
        
        return $msg;
    }
    
    /**
     * Save assistant response
     */
    public function saveAssistantMessage(Conversation $conversation, string $message): Message
    {
        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'assistant',
            'content' => $message
        ]);
        
        // Update conversation
        $conversation->update([
            'last_message_at' => now(),
            'message_count' => $conversation->message_count + 1
        ]);
        
        return $msg;
    }
    
    /**
     * Get conversation context (last N messages)
     */
    public function getConversationContext(Conversation $conversation, int $limit = 10): array
    {
        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('sent_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
        
        return $messages->map(function ($message) {
            return [
                'sender' => $message->sender,
                'message' => $message->message,
                'sent_at' => $message->sent_at->format('d/m/Y H:i:s')
            ];
        })->toArray();
    }
    
    /**
     * Get all messages for a conversation
     */
    public function getMessages(Conversation $conversation): array
    {
        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('sent_at', 'asc')
            ->get();
        
        return $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'sender' => $message->sender,
                'message' => $message->message,
                'sent_at' => $message->sent_at->format('d/m/Y H:i:s')
            ];
        })->toArray();
    }
}
