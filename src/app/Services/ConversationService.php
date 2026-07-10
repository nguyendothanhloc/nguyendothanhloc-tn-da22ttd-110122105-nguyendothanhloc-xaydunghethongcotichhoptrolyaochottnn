<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class ConversationService
{
    /**
     * Get or create conversation for current user or specified user ID
     * 
     * @param int|null $userId Optional user ID. If null, uses Auth::user()
     * @param bool $returnObject If true, return Conversation object. If false, return conversation ID
     * @return Conversation|int
     */
    public function getOrCreateConversation($userId = null, bool $returnObject = true)
    {
        if ($userId === null) {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }
            $userId = $user->id;
        }
        
        $student = Student::where('user_id', $userId)->first();
        
        if (!$student) {
            throw new \Exception('Student not found for user ID: ' . $userId);
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
        
        return $returnObject ? $conversation : $conversation->id;
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
    public function getConversationContext($conversationIdOrObject, int $limit = 10): array
    {
        // Support both conversation ID and Conversation object
        if (is_numeric($conversationIdOrObject)) {
            $conversationId = $conversationIdOrObject;
        } else {
            $conversationId = $conversationIdOrObject->id;
        }
        
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
        
        return $messages->map(function ($message) {
            return [
                'sender' => $message->sender_type,
                'message' => $message->content,
                'sent_at' => $message->created_at ? $message->created_at->format('d/m/Y H:i:s') : 'N/A'
            ];
        })->toArray();
    }
    
    /**
     * Save message (generic method)
     */
    public function saveMessage($conversationIdOrObject, string $senderType, string $content): Message
    {
        // Support both conversation ID and Conversation object
        if (is_numeric($conversationIdOrObject)) {
            $conversationId = $conversationIdOrObject;
            $conversation = Conversation::find($conversationId);
        } else {
            $conversation = $conversationIdOrObject;
            $conversationId = $conversation->id;
        }
        
        if (!$conversation) {
            throw new \Exception('Conversation not found');
        }
        
        $msg = Message::create([
            'conversation_id' => $conversationId,
            'sender_type' => $senderType,
            'content' => $content
        ]);
        
        // Update conversation
        $conversation->update([
            'last_message_at' => now(),
            'message_count' => $conversation->message_count + 1
        ]);
        
        return $msg;
    }
    
    /**
     * Get all messages for a conversation
     */
    public function getMessages(Conversation $conversation): array
    {
        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();
        
        return $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'sender' => $message->sender_type,
                'message' => $message->content,
                'sent_at' => $message->created_at ? $message->created_at->format('d/m/Y H:i:s') : 'N/A'
            ];
        })->toArray();
    }
}
