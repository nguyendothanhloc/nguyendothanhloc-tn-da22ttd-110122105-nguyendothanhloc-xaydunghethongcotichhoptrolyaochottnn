<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\ChatbotKnowledge;
use App\Services\RuleBasedChatbotService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FaqSearchIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private RuleBasedChatbotService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RuleBasedChatbotService();
    }

    /**
     * Test that searchFAQ method exists and works correctly
     */
    public function test_search_faq_method_integration(): void
    {
        // Create test FAQ entries
        ChatbotKnowledge::create([
            'category' => 'Chính sách hoàn tiền',
            'question' => 'Chính sách hoàn tiền như thế nào?',
            'answer' => 'Chúng tôi hoàn tiền trong vòng 30 ngày nếu khách hàng không hài lòng.',
            'keywords' => 'hoàn tiền, refund, policy',
            'priority' => 80,
            'is_active' => true
        ]);

        ChatbotKnowledge::create([
            'category' => 'Quy định chuyển lớp',
            'question' => 'Tôi có thể chuyển lớp không?',
            'answer' => 'Bạn có thể chuyển lớp miễn phí trong vòng 7 ngày đầu tiên.',
            'keywords' => 'chuyển lớp, transfer class',
            'priority' => 60,
            'is_active' => true
        ]);

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('searchFAQ');
        $method->setAccessible(true);

        // Test 1: Search for refund policy (should find first entry)
        $result = $method->invoke($this->service, 'hoan tien');
        
        $this->assertNotNull($result, 'searchFAQ should find a match for "hoan tien"');
        $this->assertIsArray($result);
        $this->assertEquals('faq', $result['type']);
        $this->assertArrayHasKey('response', $result);
        $this->assertArrayHasKey('data', $result);
        
        // Check response format
        $this->assertStringContainsString('📚 Từ cơ sở tri thức:', $result['response']);
        $this->assertStringContainsString('Chính sách hoàn tiền như thế nào?', $result['response']);
        $this->assertStringContainsString('hoàn tiền trong vòng 30 ngày', $result['response']);
        
        // Check data is ChatbotKnowledge instance
        $this->assertInstanceOf(ChatbotKnowledge::class, $result['data']);
        $this->assertEquals('Chính sách hoàn tiền', $result['data']->category);

        // Test 2: Search for class transfer (should find second entry)
        $result2 = $method->invoke($this->service, 'chuyen lop');
        
        $this->assertNotNull($result2);
        $this->assertEquals('faq', $result2['type']);
        $this->assertStringContainsString('chuyển lớp', $result2['response']);

        // Test 3: Search with Vietnamese accents should also work
        $result3 = $method->invoke($this->service, 'hoàn tiền');
        
        $this->assertNotNull($result3);
        $this->assertEquals('faq', $result3['type']);

        // Test 4: Search for non-existent topic should return null
        $result4 = $method->invoke($this->service, 'something that does not exist');
        
        $this->assertNull($result4, 'searchFAQ should return null when no match found');
    }

    /**
     * Test that highest priority FAQ is returned when multiple matches exist
     */
    public function test_search_faq_returns_highest_priority(): void
    {
        // Create multiple FAQ entries with same keywords but different priorities
        ChatbotKnowledge::create([
            'category' => 'Khác',
            'question' => 'Câu hỏi ưu tiên thấp về học phí',
            'answer' => 'Đây là câu trả lời ưu tiên thấp.',
            'keywords' => 'học phí, tuition',
            'priority' => 30,
            'is_active' => true
        ]);

        ChatbotKnowledge::create([
            'category' => 'Khác',
            'question' => 'Câu hỏi ưu tiên cao về học phí',
            'answer' => 'Đây là câu trả lời ưu tiên cao.',
            'keywords' => 'học phí, tuition, fee',
            'priority' => 90,
            'is_active' => true
        ]);

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('searchFAQ');
        $method->setAccessible(true);

        // Search should return the higher priority entry
        $result = $method->invoke($this->service, 'hoc phi');
        
        $this->assertNotNull($result);
        $this->assertStringContainsString('ưu tiên cao', $result['response']);
        $this->assertEquals(90, $result['data']->priority);
    }

    /**
     * Test that inactive FAQs are not returned
     */
    public function test_search_faq_ignores_inactive_entries(): void
    {
        // Create an inactive FAQ entry
        ChatbotKnowledge::create([
            'category' => 'Khác',
            'question' => 'Câu hỏi không hoạt động',
            'answer' => 'Đây không nên được trả về.',
            'keywords' => 'inactive test',
            'priority' => 100,
            'is_active' => false
        ]);

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('searchFAQ');
        $method->setAccessible(true);

        // Search should return null since the only match is inactive
        $result = $method->invoke($this->service, 'inactive test');
        
        $this->assertNull($result, 'searchFAQ should not return inactive FAQ entries');
    }
}
