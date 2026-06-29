<?php

namespace Tests\Feature;

use App\Models\ChatbotKnowledge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotKnowledgeControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com'
        ]);
    }

    /** @test */
    public function admin_can_view_faq_index_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.faq.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.chatbot-knowledge.index');
        $response->assertViewHas(['faqs', 'categories', 'categoryCounts']);
    }

    /** @test */
    public function non_admin_cannot_access_faq_management()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('admin.faq.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_create_form()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.faq.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.chatbot-knowledge.create');
        $response->assertViewHas('categories');
    }

    /** @test */
    public function admin_can_create_faq_entry_with_valid_data()
    {
        $data = [
            'category' => 'Chính sách hoàn tiền',
            'question' => 'Chính sách hoàn tiền như thế nào?',
            'answer' => 'Học viên có thể yêu cầu hoàn tiền trong vòng 7 ngày đầu tiên.',
            'keywords' => 'hoàn tiền, refund, chính sách',
            'priority' => 80,
            'is_active' => true
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.faq.store'), $data);

        $response->assertRedirect(route('admin.faq.index'));
        $response->assertSessionHas('success', 'Câu hỏi FAQ đã được tạo thành công');
        
        $this->assertDatabaseHas('chatbot_knowledge', [
            'category' => 'Chính sách hoàn tiền',
            'question' => 'Chính sách hoàn tiền như thế nào?',
            'priority' => 80,
            'is_active' => true
        ]);
    }

    /** @test */
    public function store_trims_whitespace_from_fields()
    {
        $data = [
            'category' => 'Khác',
            'question' => '  Câu hỏi có khoảng trắng  ',
            'answer' => '  Câu trả lời có khoảng trắng  ',
            'keywords' => '  từ khóa, test  ',
            'priority' => 50,
            'is_active' => true
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.faq.store'), $data);

        $response->assertRedirect(route('admin.faq.index'));
        
        $faq = ChatbotKnowledge::first();
        $this->assertEquals('Câu hỏi có khoảng trắng', $faq->question);
        $this->assertEquals('Câu trả lời có khoảng trắng', $faq->answer);
        $this->assertEquals('từ khóa, test', $faq->keywords);
    }

    /** @test */
    public function store_rejects_question_shorter_than_10_characters()
    {
        $data = [
            'category' => 'Khác',
            'question' => 'Ngắn quá',
            'answer' => 'Câu trả lời đủ dài cho validation',
            'priority' => 50
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.faq.store'), $data);

        $response->assertSessionHasErrors('question');
        $this->assertDatabaseCount('chatbot_knowledge', 0);
    }

    /** @test */
    public function store_rejects_answer_shorter_than_20_characters()
    {
        $data = [
            'category' => 'Khác',
            'question' => 'Câu hỏi đủ dài',
            'answer' => 'Ngắn',
            'priority' => 50
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.faq.store'), $data);

        $response->assertSessionHasErrors('answer');
        $this->assertDatabaseCount('chatbot_knowledge', 0);
    }

    /** @test */
    public function store_rejects_priority_out_of_range()
    {
        $data = [
            'category' => 'Khác',
            'question' => 'Câu hỏi hợp lệ',
            'answer' => 'Câu trả lời hợp lệ và đủ dài',
            'priority' => 150, // Out of range
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.faq.store'), $data);

        $response->assertSessionHasErrors('priority');
        $this->assertDatabaseCount('chatbot_knowledge', 0);
    }

    /** @test */
    public function store_rejects_duplicate_normalized_question_in_same_category()
    {
        // Create first FAQ
        ChatbotKnowledge::create([
            'category' => 'Chính sách hoàn tiền',
            'question' => 'Chính sách hoàn tiền như thế nào?',
            'answer' => 'Câu trả lời cho chính sách hoàn tiền',
            'priority' => 50
        ]);

        // Try to create duplicate with different accents/casing
        $data = [
            'category' => 'Chính sách hoàn tiền',
            'question' => 'CHINH SACH HOAN TIEN NHU THE NAO?', // Same when normalized
            'answer' => 'Câu trả lời khác nhưng câu hỏi trùng',
            'priority' => 60
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.faq.store'), $data);

        $response->assertSessionHasErrors('question');
        $this->assertDatabaseCount('chatbot_knowledge', 1);
    }

    /** @test */
    public function admin_can_view_edit_form()
    {
        $faq = ChatbotKnowledge::create([
            'category' => 'Khác',
            'question' => 'Câu hỏi test',
            'answer' => 'Câu trả lời test đủ dài',
            'priority' => 50
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.faq.edit', $faq));

        $response->assertStatus(200);
        $response->assertViewIs('admin.chatbot-knowledge.edit');
        $response->assertViewHas(['chatbotKnowledge', 'categories']);
    }

    /** @test */
    public function admin_can_update_faq_entry()
    {
        $faq = ChatbotKnowledge::create([
            'category' => 'Khác',
            'question' => 'Câu hỏi cũ',
            'answer' => 'Câu trả lời cũ đủ dài',
            'priority' => 50
        ]);

        $data = [
            'category' => 'Quy định chuyển lớp',
            'question' => 'Câu hỏi mới được cập nhật',
            'answer' => 'Câu trả lời mới được cập nhật',
            'priority' => 70,
            'is_active' => false
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.faq.update', $faq), $data);

        $response->assertRedirect(route('admin.faq.index'));
        $response->assertSessionHas('success', 'Câu hỏi FAQ đã được cập nhật thành công');
        
        $faq->refresh();
        $this->assertEquals('Quy định chuyển lớp', $faq->category);
        $this->assertEquals('Câu hỏi mới được cập nhật', $faq->question);
        $this->assertEquals(70, $faq->priority);
        $this->assertFalse($faq->is_active);
    }

    /** @test */
    public function update_allows_same_question_for_same_entry()
    {
        $faq = ChatbotKnowledge::create([
            'category' => 'Khác',
            'question' => 'Câu hỏi không đổi',
            'answer' => 'Câu trả lời cũ đủ dài',
            'priority' => 50
        ]);

        $data = [
            'category' => 'Khác',
            'question' => 'Câu hỏi không đổi', // Same question
            'answer' => 'Câu trả lời mới được cập nhật',
            'priority' => 60,
            'is_active' => true
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.faq.update', $faq), $data);

        $response->assertRedirect(route('admin.faq.index'));
        $this->assertDatabaseCount('chatbot_knowledge', 1);
    }

    /** @test */
    public function admin_can_delete_faq_entry()
    {
        $faq = ChatbotKnowledge::create([
            'category' => 'Khác',
            'question' => 'Câu hỏi sẽ bị xóa',
            'answer' => 'Câu trả lời sẽ bị xóa luôn',
            'priority' => 50
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.faq.destroy', $faq));

        $response->assertRedirect(route('admin.faq.index'));
        $response->assertSessionHas('success', 'Câu hỏi FAQ đã được xóa thành công');
        
        $this->assertDatabaseCount('chatbot_knowledge', 0);
    }

    /** @test */
    public function index_filters_by_category()
    {
        ChatbotKnowledge::create([
            'category' => 'Chính sách hoàn tiền',
            'question' => 'Câu hỏi về hoàn tiền',
            'answer' => 'Câu trả lời về hoàn tiền',
            'priority' => 50
        ]);

        ChatbotKnowledge::create([
            'category' => 'Quy định chuyển lớp',
            'question' => 'Câu hỏi về chuyển lớp',
            'answer' => 'Câu trả lời về chuyển lớp',
            'priority' => 50
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.faq.index', ['category' => 'Chính sách hoàn tiền']));

        $response->assertStatus(200);
        $faqs = $response->viewData('faqs');
        $this->assertCount(1, $faqs);
        $this->assertEquals('Chính sách hoàn tiền', $faqs->first()->category);
    }

    /** @test */
    public function index_paginates_results_with_20_per_page()
    {
        // Create 25 FAQs
        for ($i = 1; $i <= 25; $i++) {
            ChatbotKnowledge::create([
                'category' => 'Khác',
                'question' => "Câu hỏi số $i đủ dài",
                'answer' => "Câu trả lời số $i đủ dài cho validation",
                'priority' => 50
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.faq.index'));

        $response->assertStatus(200);
        $faqs = $response->viewData('faqs');
        $this->assertCount(20, $faqs); // First page has 20 items
        $this->assertEquals(25, $faqs->total()); // Total is 25
    }

    /** @test */
    public function index_orders_by_priority_desc_then_created_at_desc()
    {
        $faq1 = ChatbotKnowledge::create([
            'category' => 'Khác',
            'question' => 'Câu hỏi ưu tiên thấp',
            'answer' => 'Câu trả lời ưu tiên thấp',
            'priority' => 30,
            'created_at' => now()->subDays(2)
        ]);

        $faq2 = ChatbotKnowledge::create([
            'category' => 'Khác',
            'question' => 'Câu hỏi ưu tiên cao',
            'answer' => 'Câu trả lời ưu tiên cao',
            'priority' => 80,
            'created_at' => now()->subDays(1)
        ]);

        $faq3 = ChatbotKnowledge::create([
            'category' => 'Khác',
            'question' => 'Câu hỏi ưu tiên trung bình',
            'answer' => 'Câu trả lời ưu tiên trung bình',
            'priority' => 50,
            'created_at' => now()
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.faq.index'));

        $faqs = $response->viewData('faqs');
        $this->assertEquals($faq2->id, $faqs[0]->id); // Highest priority first
        $this->assertEquals($faq3->id, $faqs[1]->id); // Medium priority second
        $this->assertEquals($faq1->id, $faqs[2]->id); // Lowest priority last
    }
}
