<?php

namespace App\Http\Controllers;

use App\Services\RuleBasedChatbotService;
use App\Services\ConversationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    protected $chatbotService;
    protected $conversationService;
    
    public function __construct(
        RuleBasedChatbotService $chatbotService,
        ConversationService $conversationService
    ) {
        $this->chatbotService = $chatbotService;
        $this->conversationService = $conversationService;
    }
    
    /**
     * Process chat message
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);
        
        try {
            \Log::info('Chat request received', ['message' => $request->message]);
            
            // Get or create conversation
            $conversation = $this->conversationService->getOrCreateConversation();
            \Log::info('Conversation created', ['id' => $conversation->id]);
            
            // Save user message
            $this->conversationService->saveUserMessage($conversation, $request->message);
            \Log::info('User message saved');
            
            // Process message and get response
            $result = $this->chatbotService->processMessage($request->message);
            \Log::info('Message processed', ['result' => $result]);
            
            // Save assistant response
            $this->conversationService->saveAssistantMessage($conversation, $result['response']);
            \Log::info('Assistant message saved');
            
            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'type' => $result['type'],
                'data' => $result['data'] ?? null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Chat error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'response' => 'Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại sau.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get conversation history
     */
    public function history(): JsonResponse
    {
        try {
            $conversation = $this->conversationService->getOrCreateConversation();
            $messages = $this->conversationService->getMessages($conversation);
            
            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
