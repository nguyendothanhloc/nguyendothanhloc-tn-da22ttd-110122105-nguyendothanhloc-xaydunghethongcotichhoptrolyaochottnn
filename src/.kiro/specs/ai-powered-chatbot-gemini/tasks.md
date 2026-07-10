# Implementation Plan: AI-Powered Chatbot with Gemini Integration

## Overview

This implementation plan converts the hybrid chatbot design into actionable coding tasks. The approach follows a layered strategy: first establish configuration and dependencies, then implement the Gemini service layer, refactor the existing rule-based service for fallback mechanism, and finally add comprehensive testing. The implementation maintains backward compatibility—the existing rule-based chatbot continues to work while AI capabilities are added as an optional fallback layer.

## Tasks

- [x] 1. Set up dependencies and configuration
  - Install google/generative-ai-php package via Composer
  - Create config/gemini.php configuration file with API settings (api_key, model, timeout, temperature, max_tokens, top_p, top_k)
  - Update .env.example with GEMINI_API_KEY and related configuration variables
  - Add GEMINI_API_KEY to .env file (placeholder for manual setup)
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 7.2, 7.3, 7.6_

- [x] 2. Implement GeminiChatbotService
  - [x] 2.1 Create GeminiChatbotService class with constructor and configuration loading
    - Create app/Services/GeminiChatbotService.php file
    - Implement constructor that loads API key from config and throws exception if missing
    - Load configuration values (model, timeout, temperature, max_tokens, top_p, top_k)
    - _Requirements: 2.1, 1.5_

  - [x] 2.2 Implement buildStudentContext() method
    - Query Student model with eager loading for enrollments, classes, courses, schedules, attendance, assessment scores, and payments
    - Build structured array with student info, enrollments, schedules, attendance summary, recent assessments, and payment status
    - Filter out sensitive data (passwords, API keys, full addresses, payment card details)
    - Limit queries to most recent/relevant data (e.g., last 5 assessments, upcoming 10 schedules)
    - _Requirements: 2.6, 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8_

  - [x] 2.3 Implement formatPrompt() method
    - Create system instructions for Vietnamese-speaking language center assistant
    - Format student context data into clear, structured text
    - Append user message to prompt
    - Return complete formatted prompt string
    - _Requirements: 2.7_

  - [x] 2.4 Implement callGeminiAPI() method
    - Build HTTP request to Gemini API endpoint with formatted prompt
    - Set request timeout from configuration
    - Include generationConfig (temperature, maxOutputTokens, topP, topK)
    - Parse API response and extract generated text
    - Implement single retry with exponential backoff for transient network errors
    - _Requirements: 2.3_

  - [x] 2.5 Implement handleAPIError() method
    - Detect 8 error types: missing API key, invalid API key (401), rate limit (429), timeout, network error, service unavailable (503), malformed response, student not found
    - Log each error type with context (student_id, message, api_response, timestamp)
    - Return user-friendly Vietnamese error messages for each error type
    - _Requirements: 2.5, 2.8, 5.1, 5.2, 5.3, 5.4, 5.6_

  - [x] 2.6 Implement generateResponse() method
    - Accept message and context parameters
    - Call buildStudentContext() to get student data
    - Call formatPrompt() to create API prompt
    - Call callGeminiAPI() to get AI response
    - Catch exceptions and call handleAPIError()
    - Return AI-generated response or error message
    - _Requirements: 2.2, 2.3, 2.4, 2.5_

- [x] 3. Refactor RuleBasedChatbotService for hybrid system
  - [x] 3.1 Extract existing pattern matching into tryRuleBasedMatch() method
    - Move current pattern matching logic from processMessage() to new tryRuleBasedMatch() method
    - Return response array if pattern matches, null if no match
    - Keep existing functionality intact (removeVietnameseAccents, matchesPattern)
    - _Requirements: 3.1, 3.3_

  - [x] 3.2 Implement askAI() method as fallback
    - Retrieve student record from authenticated user
    - Call GeminiChatbotService->generateResponse() with message and student context
    - Return response array with AI-generated content
    - Handle case where student record is not found
    - _Requirements: 3.2, 3.6, 3.7_

  - [x] 3.3 Refactor processMessage() for fallback mechanism
    - Call tryRuleBasedMatch() first
    - If rule-based returns response, log "rule-based match" and return immediately
    - If no pattern match (null), retrieve student and call askAI()
    - Log "AI fallback" when using AI response
    - Maintain conversation continuity for both response types
    - _Requirements: 3.3, 3.4, 3.5, 4.1, 4.2, 4.5_

- [x] 4. Checkpoint - Test basic functionality manually
  - Ensure all tests pass (if any exist), verify API key is configured
  - Test with simple rule-based question ("Xin chào", "Lịch học hôm nay")
  - Test with complex AI fallback question ("How can I improve my speaking based on my recent scores?")
  - Verify conversation persistence works for both response types
  - Ask the user if questions arise

- [x] 5. Implement unit tests for GeminiChatbotService
  - [x]* 5.1 Write unit tests for GeminiChatbotService
    - testGenerateResponseSuccess: Mock API success, verify response returned
    - testBuildStudentContextComplete: Verify context includes all required data (student, enrollments, schedules, attendance, assessments, payments)
    - testBuildStudentContextWithNoEnrollments: Handle students with no enrollments gracefully
    - testFormatPromptStructure: Verify prompt includes system instructions, context, and user message
    - testHandleAPIErrorInvalidKey: Mock 401 error, verify authentication error message
    - testHandleAPIErrorRateLimit: Mock 429 error, verify rate limit message
    - testHandleAPIErrorTimeout: Mock timeout, verify timeout error message
    - testHandleAPIErrorNetworkFailure: Mock network error, verify network error message
    - testMissingAPIKeyThrowsException: Verify exception when GEMINI_API_KEY not configured
    - _Requirements: 8.1, 8.2_

- [x] 6. Implement unit tests for RuleBasedChatbotService
  - [x]* 6.1 Write unit tests for refactored RuleBasedChatbotService
    - testTryRuleBasedMatchSuccess: Send pattern-matching message, verify response returned
    - testTryRuleBasedMatchNoMatch: Send non-matching message, verify null returned
    - testAskAICalledOnNoMatch: Verify AI service is called when no rule matches
    - testRuleBasedPreferredOverAI: Send matching message, verify rule-based used (AI not called)
    - testProcessMessageLogsDecision: Verify logging for both rule-based and AI fallback decisions
    - _Requirements: 8.3_

- [x] 7. Implement integration tests for hybrid chatbot
  - [x]* 7.1 Write integration tests for end-to-end hybrid chatbot flow
    - testSimpleQuestionUsesRuleBased: Send "lịch học hôm nay", verify rule-based response used
    - testComplexQuestionUsesAI: Send complex question, verify AI response generated
    - testConversationPersistence: Verify both rule-based and AI responses saved to database via ConversationService
    - testStudentContextBuilding: Verify correct student data (enrollments, schedules, etc.) retrieved for AI context
    - testAPIFailureFallback: Mock API failure, verify graceful error message returned
    - testRateLimitHandling: Mock 429 rate limit error, verify appropriate Vietnamese message
    - testResponseTimePerformance: Verify AI responses complete within 5 seconds
    - testEmptyPatternFallsBackToAI: Send message with no pattern match, verify AI fallback triggered
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.6, 5.1, 5.5, 8.4, 8.5, 8.6_

- [x] 8. Final checkpoint - Complete testing and validation
  - Run all unit and integration tests, ensure they pass
  - Verify error handling for all 8 error types
  - Test with sample complex questions from design document
  - Verify logging works for rule-based vs AI decision tracking
  - Ask the user if questions arise

- [x] 9. Create Admin FAQ Management System (Knowledge Base - Layer 3)
  - [x] 9.1 Create database migration for chatbot_knowledge table
    - Create migration file with table structure: id, category (varchar 50), question (text), answer (text), keywords (text nullable), priority (integer default 0), is_active (boolean default true), timestamps
    - Add indexes on category, is_active, and priority columns for query performance
    - _Requirements: Database schema for FAQ storage_

  - [x] 9.2 Create ChatbotKnowledge model
    - Create app/Models/ChatbotKnowledge.php with fillable fields (category, question, answer, keywords, priority, is_active)
    - Add casts for is_active (boolean) and priority (integer)
    - Add scope for active records: scopeActive()
    - Add scope for search by keywords: scopeSearchKeywords()
    - _Requirements: Eloquent model for FAQ management_

  - [x] 9.3 Create ChatbotKnowledgeController for admin CRUD
    - Create app/Http/Controllers/ChatbotKnowledgeController.php
    - Implement index() method: list all FAQs with pagination (20 per page), filter by category and is_active
    - Implement create() method: show create form
    - Implement store() method: validate and save new FAQ (required: category, question, answer; optional: keywords, priority)
    - Implement edit() method: show edit form with existing data
    - Implement update() method: validate and update FAQ
    - Implement destroy() method: soft delete or hard delete FAQ
    - Implement toggleStatus() method: toggle is_active status via AJAX
    - _Requirements: Admin CRUD operations for FAQ_

  - [x] 9.4 Create admin views for FAQ management
    - Create resources/views/admin/chatbot-knowledge/index.blade.php: table with columns (category, question preview, answer preview, priority, status, actions), filter dropdown by category, search box
    - Create resources/views/admin/chatbot-knowledge/create.blade.php: form with fields (category dropdown, question textarea, answer textarea, keywords input, priority number, is_active checkbox)
    - Create resources/views/admin/chatbot-knowledge/edit.blade.php: similar to create form with existing data
    - Use Tailwind CSS consistent with existing admin pages
    - Add toast notifications for success/error messages
    - _Requirements: Admin UI for FAQ management_

  - [x] 9.5 Add admin routes for FAQ management
    - Add routes in routes/web.php under admin middleware group:
      - GET /admin/chatbot-knowledge -> index
      - GET /admin/chatbot-knowledge/create -> create
      - POST /admin/chatbot-knowledge -> store
      - GET /admin/chatbot-knowledge/{id}/edit -> edit
      - PUT /admin/chatbot-knowledge/{id} -> update
      - DELETE /admin/chatbot-knowledge/{id} -> destroy
      - PATCH /admin/chatbot-knowledge/{id}/toggle-status -> toggleStatus
    - _Requirements: Admin routes for FAQ CRUD_

  - [x] 9.6 Add searchKnowledgeBase() method to RuleBasedChatbotService
    - Add searchKnowledgeBase() method after tryRuleBasedMatch() and before askAI() in the fallback chain
    - Query ChatbotKnowledge::active() for matching records
    - Search logic: normalize message (removeVietnameseAccents), match against question field (LIKE %normalized%), match against keywords field (split by comma, check each keyword)
    - Order results by priority DESC
    - Return first match with highest priority
    - If match found, return response array with type 'knowledge_base'
    - If no match, return null (proceed to askAI)
    - Log "Knowledge Base match" when FAQ found
    - _Requirements: Knowledge Base search as Layer 3 fallback_

  - [x] 9.7 Update processMessage() to include Knowledge Base layer
    - Modify processMessage() to call searchKnowledgeBase() after tryRuleBasedMatch() returns null and before askAI()
    - Flow: tryRuleBasedMatch() -> searchKnowledgeBase() -> askAI()
    - Log each layer decision for debugging
    - Maintain backward compatibility with existing pattern matching
    - _Requirements: 3-layer hybrid system (Rule-based -> Knowledge Base -> AI)_

  - [x] 9.8 Add navigation menu item for admin
    - Update resources/views/layouts/navigation.blade.php to add "Quản lý FAQ Chatbot" menu item in admin section
    - Icon: question mark or knowledge base icon
    - Place after "Quản lý lớp học" menu item
    - _Requirements: Admin navigation for FAQ management_

  - [x] 9.9 Checkpoint - Test Knowledge Base functionality
    - Run migration and verify table created
    - Test admin CRUD: create, edit, delete, toggle status
    - Add sample FAQ entries (e.g., "Chính sách hoàn tiền", "Quy định chuyển lớp", "Thủ tục nghỉ học")
    - Test chatbot with FAQ questions, verify Knowledge Base layer returns correct answers
    - Verify 3-layer fallback works: Rule-based -> Knowledge Base -> AI
    - Ask the user if questions arise

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- The implementation maintains backward compatibility—existing rule-based chatbot continues to work
- AI fallback is seamless and transparent to users (no notification of switch)
- Configuration must be completed manually: obtain API key from https://aistudio.google.com/app/apikey
- Testing uses mocking for external API calls—no actual API requests in unit tests
- Integration tests validate the hybrid mechanism and conversation persistence
- Manual testing checklist in design document should be followed after implementation
- Laravel 11 project structure with MySQL database `language_center`
- Existing components (ChatbotController, ConversationService, chatbot widget) require no changes

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1"] },
    { "id": 1, "tasks": ["2.1"] },
    { "id": 2, "tasks": ["2.2", "2.3", "2.4", "2.5"] },
    { "id": 3, "tasks": ["2.6", "3.1"] },
    { "id": 4, "tasks": ["3.2"] },
    { "id": 5, "tasks": ["3.3"] },
    { "id": 6, "tasks": ["5.1", "6.1"] },
    { "id": 7, "tasks": ["7.1"] },
    { "id": 8, "tasks": ["9.1", "9.2"] },
    { "id": 9, "tasks": ["9.3", "9.4", "9.5"] },
    { "id": 10, "tasks": ["9.6"] },
    { "id": 11, "tasks": ["9.7", "9.8"] }
  ]
}
```
