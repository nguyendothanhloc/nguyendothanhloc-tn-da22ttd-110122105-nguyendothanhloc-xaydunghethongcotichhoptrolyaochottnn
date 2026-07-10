# Design Document: Admin FAQ Management System

## Overview

The Admin FAQ Management System introduces Layer 2 (Knowledge Base) into the existing three-layer chatbot architecture. This layer sits between the rule-based pattern matching (Layer 1) and AI fallback (Layer 3), providing fast, accurate responses to policy and regulation questions through a searchable knowledge base.

**Architecture Flow:**
```
User Question
    ↓
Layer 1: Pattern Matching (RuleBasedChatbotService::tryRuleBasedMatch)
    ↓ (if no match)
Layer 2: FAQ Knowledge Base (NEW - RuleBasedChatbotService::searchFAQ)
    ↓ (if no match)
Layer 3: AI Fallback (GeminiChatbotService::generateResponse)
```

The system consists of two main components:
1. **Admin Interface**: Laravel Blade views and controller for CRUD operations on FAQ entries
2. **Chatbot Integration**: Modified RuleBasedChatbotService with FAQ search capability

## Architecture

### Database Layer

**Table: `chatbot_knowledge`**
- Primary storage for FAQ entries
- Indexed fields for efficient text search
- Schema designed for normalized text matching

### Application Layer

**Models:**
- `ChatbotKnowledge` - Eloquent model representing FAQ entries
  - Accessors for normalized text
  - Validation rules
  - Scopes for active entries

**Services:**
- `RuleBasedChatbotService` (modified) - Adds FAQ search between pattern matching and AI fallback
  - Reuses existing `removeVietnameseAccents()` method
  - New `searchFAQ()` method for knowledge base queries
  - Integration with existing `processMessage()` workflow

**Controllers:**
- `ChatbotKnowledgeController` - CRUD operations for FAQ management
  - Index: List all FAQs with filtering
  - Create/Store: Add new FAQ entries
  - Edit/Update: Modify existing entries
  - Destroy: Delete entries
  - Middleware: `auth`, `role:admin`

### Presentation Layer

**Admin Views:**
- `admin/chatbot-knowledge/index.blade.php` - FAQ list with search/filter
- `admin/chatbot-knowledge/create.blade.php` - Create new FAQ form
- `admin/chatbot-knowledge/edit.blade.php` - Edit existing FAQ form
- Styled with Tailwind CSS matching existing admin interface

## Components and Interfaces

### ChatbotKnowledge Model

```php
class ChatbotKnowledge extends Model
{
    // Fields
    protected $table = 'chatbot_knowledge';
    protected $fillable = [
        'category',
        'question',
        'answer',
        'keywords',
        'priority',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer'
    ];
    
    // Accessors
    public function getNormalizedQuestionAttribute(): string
    public function getNormalizedKeywordsAttribute(): string
    
    // Scopes
    public function scopeActive($query)
    public function scopeByCategory($query, $category)
    public function scopeSearchText($query, $searchText)
    
    // Validation Rules
    public static function validationRules(): array
}
```

### ChatbotKnowledgeController

```php
class ChatbotKnowledgeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
    
    // Display FAQ list with optional category filter
    public function index(Request $request): View
    
    // Show create form
    public function create(): View
    
    // Store new FAQ entry (with validation)
    public function store(Request $request): RedirectResponse
    
    // Show edit form
    public function edit(ChatbotKnowledge $faq): View
    
    // Update existing FAQ entry (with validation)
    public function update(Request $request, ChatbotKnowledge $faq): RedirectResponse
    
    // Delete FAQ entry
    public function destroy(ChatbotKnowledge $faq): RedirectResponse
}
```

### Modified RuleBasedChatbotService

```php
class RuleBasedChatbotService
{
    // Existing method - reused for FAQ search
    private function removeVietnameseAccents(string $str): string
    
    // NEW: Search FAQ knowledge base
    private function searchFAQ(string $message): ?array
    {
        // 1. Normalize search message
        // 2. Query active FAQ entries
        // 3. Match against normalized question and keywords
        // 4. Return highest priority match or null
    }
    
    // MODIFIED: Updated to include FAQ layer
    public function processMessage(string $message): array
    {
        // Step 1: Try rule-based matching first
        $ruleResponse = $this->tryRuleBasedMatch($message);
        if ($ruleResponse !== null) {
            return $ruleResponse;
        }
        
        // Step 2: NEW - Try FAQ knowledge base
        $faqResponse = $this->searchFAQ($message);
        if ($faqResponse !== null) {
            return $faqResponse;
        }
        
        // Step 3: Fall back to AI
        return $this->askAI($message, $student->id);
    }
}
```

## Data Models

### ChatbotKnowledge Model Fields

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint unsigned | Primary Key, Auto Increment | Unique identifier |
| category | varchar(100) | NOT NULL | FAQ category for organization |
| question | text | NOT NULL, min:10 chars | The FAQ question |
| answer | text | NOT NULL, min:20 chars | The FAQ answer |
| keywords | varchar(500) | Nullable, max:500 chars | Comma-separated search keywords |
| priority | integer | NOT NULL, default:50, range:1-100 | Priority for ranking matches (higher = more important) |
| is_active | boolean | NOT NULL, default:true | Whether FAQ is visible to chatbot |
| created_at | timestamp | Nullable | Record creation time |
| updated_at | timestamp | Nullable | Record last update time |

### Indexes

- Primary: `id`
- Index: `question` (FULLTEXT for efficient search)
- Index: `keywords` (FULLTEXT for efficient search)
- Index: `is_active` (for filtering active entries)
- Index: `category` (for filtering by category)
- Composite Index: `(category, is_active, priority)` (for optimized queries)

### Predefined Categories

1. "Chính sách hoàn tiền" (Refund policy)
2. "Quy định chuyển lớp" (Class transfer regulations)
3. "Thủ tục nghỉ học / bảo lưu" (Leave/suspension procedures)
4. "Điều kiện nhận ưu đãi / giảm giá" (Discount eligibility)
5. "Khác" (Other)

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Required Field Validation

*For any* FAQ entry creation or update attempt, if any required field (category, question, answer) is empty or missing, the system SHALL reject the operation and return a validation error.

**Validates: Requirements 1.2, 1.3**

### Property 2: FAQ Deletion Removes Entry

*For any* FAQ entry in the knowledge base, when deleted by an admin, the entry SHALL no longer exist in the database after the operation completes.

**Validates: Requirements 1.4**

### Property 3: Active Status Controls Visibility

*For any* FAQ entry, when is_active is false, the entry SHALL NOT appear in chatbot search results; when is_active is true, the entry SHALL be searchable by the chatbot.

**Validates: Requirements 1.5, 9.2**

### Property 4: Unique Normalized Question Per Category

*For any* two FAQ entries within the same category, if their normalized question text is identical, the system SHALL reject the creation or update of the second entry.

**Validates: Requirements 2.2, 10.4**

### Property 5: Priority Range Validation

*For any* FAQ entry, if the priority value is less than 1 or greater than 100, the system SHALL reject the save operation.

**Validates: Requirements 2.3, 10.3**

### Property 6: Default Values Applied

*For any* new FAQ entry created without explicit values for is_active or priority, the system SHALL set is_active to true and priority to 50.

**Validates: Requirements 2.4**

### Property 7: Vietnamese Accent Normalization

*For any* text string containing Vietnamese diacritical marks, the removeVietnameseAccents() function SHALL return text with all diacritics removed and all characters converted to lowercase.

**Validates: Requirements 4.1, 4.2, 4.3**

### Property 8: Highest Priority Match Returned

*For any* search query that matches multiple FAQ entries, the searchFAQ() method SHALL return the entry with the highest priority value.

**Validates: Requirements 3.3, 6.4, 9.3**

### Property 9: FAQ Response Structure

*For any* FAQ entry returned by searchFAQ(), the response SHALL have type 'faq', include the knowledge base prefix "📚 Từ cơ sở tri thức:", contain both the question and answer, and include the matched entry data.

**Validates: Requirements 3.4, 7.1, 7.2, 7.3**

### Property 10: Text Round-Trip Preservation

*For any* FAQ entry with Vietnamese accents, special characters, or line breaks in the question or answer, after saving to database and reloading, the original text SHALL be preserved exactly.

**Validates: Requirements 4.5, 7.4, 7.5**

### Property 11: Keyword Substring Matching

*For any* FAQ entry with keywords and any search query, if the normalized search query is a substring of any normalized keyword or the normalized question, the entry SHALL be included in search results.

**Validates: Requirements 6.2, 6.3**

### Property 12: Category Entry Count Accuracy

*For any* category filter, the count of active FAQ entries returned SHALL equal the actual number of active entries with that category in the database.

**Validates: Requirements 5.4**

### Property 13: Field Length Validation

*For any* FAQ entry, if the question is less than 10 characters, the answer is less than 20 characters, or keywords exceed 500 characters, the system SHALL reject the save operation.

**Validates: Requirements 9.4, 10.1, 10.2**

### Property 14: Maximum Entry Limit Enforcement

*For any* attempt to create a new active FAQ entry when 500 active entries already exist, the system SHALL reject the creation.

**Validates: Requirements 9.5**

### Property 15: Whitespace Trimming

*For any* FAQ entry saved with leading or trailing whitespace in the question, answer, or keywords fields, the saved version SHALL have whitespace trimmed from all three fields.

**Validates: Requirements 10.5**

## Error Handling

### Validation Errors

**Input Validation:**
- Required field missing: Return 422 with specific field error
- Field length violation: Return 422 with min/max length message
- Priority out of range: Return 422 with valid range message
- Duplicate question in category: Return 422 with uniqueness violation message
- Maximum entries exceeded: Return 422 with limit message

**Error Response Format:**
```php
return redirect()->back()
    ->withErrors(['field' => 'Error message'])
    ->withInput();
```

### Database Errors

**Connection Errors:**
- Log error with context
- Show user-friendly message: "Không thể kết nối cơ sở dữ liệu. Vui lòng thử lại sau."

**Query Errors:**
- Log full query and error
- Show user-friendly message based on error type
- For integrity violations: Show specific constraint message

### Search Errors

**FAQ Search Failures:**
- Log error but do not interrupt flow
- Fall through to AI fallback layer
- Return AI-generated response or default error message

### Authorization Errors

**Non-Admin Access:**
- Middleware intercepts
- Redirect to dashboard with message: "Bạn không có quyền truy cập trang này."

## Testing Strategy

The FAQ Management System will use a dual testing approach combining unit tests for specific examples and edge cases with property-based tests for universal correctness properties.

### Unit Testing

Unit tests will focus on:
- **Specific UI flows**: Admin creates FAQ, views list, edits entry, deletes entry
- **Integration points**: Controller → Model → Database interactions
- **Edge cases**: Empty category filter, no results found, duplicate detection
- **Error conditions**: Invalid input, missing required fields, authorization failures

Unit tests validate correct behavior for representative scenarios but do not exhaustively cover all input combinations.

### Property-Based Testing

Property-based tests will focus on:
- **Universal validation rules**: Required fields, field lengths, priority ranges
- **Text normalization**: Vietnamese accent removal, case conversion, preservation
- **Search logic**: Keyword matching, priority ordering, visibility filtering
- **Data integrity**: Uniqueness constraints, whitespace trimming, round-trip preservation

**Property Test Configuration:**
- Minimum 100 iterations per property test
- Each property test references its design document property number
- Tag format: `Feature: admin-faq-management-system, Property {number}: {property_text}`

**Property Test Library:**
- PHP Property Testing: Use **Eris** (https://github.com/giorgiosironi/eris) for PHP property-based testing
- Generator strategies:
  - Vietnamese text with random diacritics
  - Random priorities (including out-of-range)
  - Random field lengths (including boundary cases)
  - Random category combinations
  - Random keyword patterns

### Integration Testing

Integration tests will verify:
- Route registration and middleware application
- Database migrations and indexes
- Chatbot service layer integration (pattern match → FAQ → AI flow)
- View rendering with Blade components
- Session and flash message handling

### Test Coverage Goals

- Unit tests: 80%+ code coverage of controllers and models
- Property tests: 100% coverage of all 15 correctness properties
- Integration tests: All routes, middleware, and service integrations

### Testing Notes

- Use in-memory SQLite database for fast test execution
- Mock GeminiChatbotService to avoid external API calls during tests
- Use database transactions to roll back test data
- Property tests tagged with `@group property` for separate execution
- Integration tests tagged with `@group integration`

## Implementation Notes

1. **Reuse existing normalization**: The `removeVietnameseAccents()` method already exists in RuleBasedChatbotService and can be reused for FAQ search
2. **Indexing strategy**: Use FULLTEXT indexes on MySQL for efficient text search on question and keywords columns
3. **Performance**: Limit FAQ queries to 500 active entries maximum; consider caching if search latency exceeds 200ms
4. **Migration order**: Create chatbot_knowledge table migration, then seed with example FAQs for testing
5. **Admin navigation**: Add "FAQ Management" link to admin sidebar navigation menu
6. **Response format**: FAQ responses should match the existing response structure `['response' => string, 'type' => string, 'data' => array]` used by other chatbot layers
7. **Logging**: Add logging to searchFAQ() method to track FAQ match rates for analytics (similar to existing pattern match logging)
