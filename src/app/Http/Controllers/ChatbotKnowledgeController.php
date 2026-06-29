<?php

namespace App\Http\Controllers;

use App\Models\ChatbotKnowledge;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChatbotKnowledgeController extends Controller
{
    /**
     * Display a listing of FAQ entries with filters and pagination
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = ChatbotKnowledge::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'LIKE', "%{$search}%")
                  ->orWhere('answer', 'LIKE', "%{$search}%")
                  ->orWhere('keywords', 'LIKE', "%{$search}%");
            });
        }

        // Order by priority and get paginated results
        $faqs = $query->orderBy('priority', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->paginate(20)
                      ->withQueryString();

        $categories = ChatbotKnowledge::categories();

        return view('admin.chatbot-knowledge.index', compact('faqs', 'categories'));
    }

    /**
     * Show the form for creating a new FAQ entry
     *
     * @return View
     */
    public function create(): View
    {
        $categories = ChatbotKnowledge::categories();
        return view('admin.chatbot-knowledge.create', compact('categories'));
    }

    /**
     * Store a newly created FAQ entry in storage
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // DEBUG: Log all incoming request data
        \Log::info('FAQ Store Request Received', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? 'N/A'
        ]);

        try {
            $validated = $request->validate(ChatbotKnowledge::validationRules(), [
                'category.required' => 'Danh mục là bắt buộc',
                'question.required' => 'Câu hỏi là bắt buộc',
                'question.min' => 'Câu hỏi phải có ít nhất 10 ký tự',
                'answer.required' => 'Câu trả lời là bắt buộc',
                'answer.min' => 'Câu trả lời phải có ít nhất 20 ký tự',
                'priority.required' => 'Độ ưu tiên là bắt buộc',
                'priority.min' => 'Độ ưu tiên phải từ 1 đến 100',
                'priority.max' => 'Độ ưu tiên phải từ 1 đến 100',
            ]);

            \Log::info('FAQ Validation Passed', ['validated_data' => $validated]);

            // Set is_active to true if not provided (checkbox not checked)
            $validated['is_active'] = $request->has('is_active');

            $faq = ChatbotKnowledge::create($validated);
            
            \Log::info('FAQ Created Successfully', ['faq_id' => $faq->id]);

            return redirect()->route('admin.faq.index')
                ->with('success', 'Câu hỏi FAQ đã được tạo thành công');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('FAQ Validation Failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('FAQ Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified FAQ entry
     *
     * @param ChatbotKnowledge $chatbotKnowledge
     * @return View
     */
    public function edit(ChatbotKnowledge $chatbotKnowledge): View
    {
        $categories = ChatbotKnowledge::categories();
        return view('admin.chatbot-knowledge.edit', compact('chatbotKnowledge', 'categories'));
    }

    /**
     * Update the specified FAQ entry in storage
     *
     * @param Request $request
     * @param ChatbotKnowledge $chatbotKnowledge
     * @return RedirectResponse
     */
    public function update(Request $request, ChatbotKnowledge $chatbotKnowledge): RedirectResponse
    {
        $validated = $request->validate(ChatbotKnowledge::validationRules(), [
            'category.required' => 'Danh mục là bắt buộc',
            'question.required' => 'Câu hỏi là bắt buộc',
            'question.min' => 'Câu hỏi phải có ít nhất 10 ký tự',
            'answer.required' => 'Câu trả lời là bắt buộc',
            'answer.min' => 'Câu trả lời phải có ít nhất 20 ký tự',
            'priority.required' => 'Độ ưu tiên là bắt buộc',
            'priority.min' => 'Độ ưu tiên phải từ 1 đến 100',
            'priority.max' => 'Độ ưu tiên phải từ 1 đến 100',
        ]);

        // Set is_active based on checkbox
        $validated['is_active'] = $request->has('is_active');

        $chatbotKnowledge->update($validated);

        return redirect()->route('admin.faq.index')
            ->with('success', 'Câu hỏi FAQ đã được cập nhật thành công');
    }

    /**
     * Remove the specified FAQ entry from storage (hard delete)
     *
     * @param ChatbotKnowledge $chatbotKnowledge
     * @return RedirectResponse
     */
    public function destroy(ChatbotKnowledge $chatbotKnowledge): RedirectResponse
    {
        $chatbotKnowledge->delete();

        return redirect()->route('admin.faq.index')
            ->with('success', 'Câu hỏi FAQ đã được xóa thành công');
    }

    /**
     * Toggle the is_active status of FAQ entry via AJAX
     *
     * @param Request $request
     * @param ChatbotKnowledge $chatbotKnowledge
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request, ChatbotKnowledge $chatbotKnowledge)
    {
        try {
            $chatbotKnowledge->is_active = !$chatbotKnowledge->is_active;
            $chatbotKnowledge->save();

            return response()->json([
                'success' => true,
                'is_active' => $chatbotKnowledge->is_active,
                'message' => $chatbotKnowledge->is_active 
                    ? 'Đã kích hoạt FAQ' 
                    : 'Đã vô hiệu hóa FAQ'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thay đổi trạng thái'
            ], 500);
        }
    }
}
