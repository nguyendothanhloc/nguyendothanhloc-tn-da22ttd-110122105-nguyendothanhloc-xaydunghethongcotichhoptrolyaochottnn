# Requirements Document

## Introduction

Nâng cấp hệ thống chatbot từ rule-based pattern matching lên hybrid system kết hợp rule-based với AI-powered fallback sử dụng Google Gemini API. Hệ thống hybrid giữ lại rule-based cho câu hỏi đơn giản (xử lý 99% trường hợp, nhanh và miễn phí) và sử dụng Gemini AI làm fallback cho câu hỏi phức tạp (xử lý 1% trường hợp còn lại).

## Glossary

- **Rule_Based_Service**: Service hiện tại xử lý chatbot dựa trên pattern matching với 35 câu hỏi định sẵn
- **Gemini_Service**: Service mới sử dụng Google Gemini API để xử lý câu hỏi phức tạp
- **Hybrid_Chatbot**: Hệ thống chatbot kết hợp rule-based và AI-powered
- **Student_Context**: Thông tin ngữ cảnh của student bao gồm enrollment, classes, schedules, attendance, assessments, payments
- **Fallback_Mechanism**: Cơ chế chuyển từ rule-based sang AI khi không tìm thấy pattern match
- **API_Key**: GEMINI_API_KEY được lấy từ Google AI Studio
- **Configuration**: File config chứa thông số API và settings

## Requirements

### Requirement 1: Gemini API Integration

**User Story:** As a system administrator, I want to integrate Google Gemini API into the chatbot system, so that the chatbot can handle complex questions beyond predefined patterns.

#### Acceptance Criteria

1. THE System SHALL install the google/generative-ai-php package via Composer
2. THE Configuration SHALL store the GEMINI_API_KEY in the .env file
3. THE Configuration SHALL provide the GEMINI_API_KEY template in .env.example file
4. THE System SHALL create a config/gemini.php configuration file with API settings
5. WHEN the GEMINI_API_KEY is missing from .env, THE Gemini_Service SHALL throw a configuration exception

### Requirement 2: Gemini Service Implementation

**User Story:** As a developer, I want a dedicated Gemini service to handle AI-powered responses, so that AI functionality is separated from rule-based logic.

#### Acceptance Criteria

1. THE System SHALL create app/Services/GeminiChatbotService.php file
2. THE Gemini_Service SHALL implement a generateResponse($message, $context) method
3. WHEN generateResponse is called with a message and context, THE Gemini_Service SHALL send a request to Google Gemini API
4. WHEN the API request succeeds, THE Gemini_Service SHALL return the AI-generated response
5. IF the API request fails, THEN THE Gemini_Service SHALL log the error and return a user-friendly error message
6. THE Gemini_Service SHALL build Student_Context from student data including enrollment, classes, schedules, attendance, assessments, and payments
7. THE Gemini_Service SHALL format the context data into a prompt for Gemini API
8. WHEN the API rate limit is exceeded (15 requests/min), THE Gemini_Service SHALL return a rate limit message

### Requirement 3: Rule-Based Service Refactoring

**User Story:** As a developer, I want to refactor the existing rule-based service to support fallback mechanism, so that complex questions can be routed to AI.

#### Acceptance Criteria

1. THE Rule_Based_Service SHALL wrap existing pattern matching logic in a tryRuleBasedMatch($message) method
2. THE Rule_Based_Service SHALL implement an askAI($message, $studentId) method as fallback
3. THE Rule_Based_Service SHALL modify processMessage() to try rule-based matching first
4. WHEN tryRuleBasedMatch returns null (no pattern match), THE Rule_Based_Service SHALL call askAI method
5. WHEN tryRuleBasedMatch returns a response, THE Rule_Based_Service SHALL return that response immediately without calling AI
6. THE askAI method SHALL retrieve Student_Context based on studentId
7. THE askAI method SHALL delegate to Gemini_Service for AI response generation

### Requirement 4: Hybrid System Integration

**User Story:** As a student, I want the chatbot to answer both simple and complex questions, so that I can get help with any question about the language center.

#### Acceptance Criteria

1. WHEN a student asks a question matching existing patterns, THE Hybrid_Chatbot SHALL respond using Rule_Based_Service
2. WHEN a student asks a complex question not matching any pattern, THE Hybrid_Chatbot SHALL respond using Gemini_Service
3. THE Hybrid_Chatbot SHALL maintain conversation continuity across rule-based and AI responses
4. THE Hybrid_Chatbot SHALL use the existing ConversationService for message storage
5. WHEN switching from rule-based to AI, THE Hybrid_Chatbot SHALL not notify the user about the switch
6. THE Hybrid_Chatbot SHALL respond to complex questions within a reasonable time (under 5 seconds when API is available)

### Requirement 5: Error Handling and Resilience

**User Story:** As a student, I want the chatbot to work reliably even when AI service has issues, so that I can always get some response to my questions.

#### Acceptance Criteria

1. IF the Gemini API is unavailable, THEN THE Hybrid_Chatbot SHALL return a graceful error message
2. IF the API key is invalid, THEN THE Gemini_Service SHALL log the error and return an authentication error message
3. IF the API request times out, THEN THE Gemini_Service SHALL return a timeout error message within 10 seconds
4. WHEN an API error occurs, THE System SHALL log the full error details for debugging
5. THE Hybrid_Chatbot SHALL continue to work with rule-based responses even when Gemini_Service fails
6. IF network connectivity is lost, THEN THE Gemini_Service SHALL detect the failure and return an appropriate message

### Requirement 6: Context Building and Privacy

**User Story:** As a student, I want the AI to understand my personal context, so that it can provide relevant and personalized answers to my questions.

#### Acceptance Criteria

1. WHEN building Student_Context, THE Gemini_Service SHALL retrieve the authenticated student's data only
2. THE Student_Context SHALL include current enrollment information
3. THE Student_Context SHALL include current and upcoming class schedules
4. THE Student_Context SHALL include recent attendance records
5. THE Student_Context SHALL include assessment scores and feedback
6. THE Student_Context SHALL include payment status and history
7. THE Gemini_Service SHALL not include sensitive data like passwords or API keys in the context
8. THE Gemini_Service SHALL format the context in a clear, structured format for the AI model

### Requirement 7: Configuration and Deployment

**User Story:** As a system administrator, I want clear configuration options, so that I can easily deploy and manage the AI-powered chatbot.

#### Acceptance Criteria

1. THE System SHALL provide documentation for obtaining a GEMINI_API_KEY from https://aistudio.google.com/app/apikey
2. THE config/gemini.php file SHALL include settings for API endpoint, model name, and timeout
3. THE config/gemini.php file SHALL allow configuration of temperature and max tokens for AI responses
4. THE System SHALL provide clear error messages when configuration is incomplete
5. WHEN deploying to production, THE System SHALL validate that GEMINI_API_KEY is set before accepting traffic
6. THE .env.example file SHALL document the required GEMINI_API_KEY variable with a placeholder

### Requirement 8: Testing and Validation

**User Story:** As a developer, I want to test the hybrid chatbot with complex questions, so that I can verify it handles cases beyond the 35 existing patterns.

#### Acceptance Criteria

1. THE System SHALL allow testing with sample complex questions that rule-based cannot handle
2. WHEN testing with "How can I improve my speaking skills based on my recent assessment scores?", THE Hybrid_Chatbot SHALL generate a relevant AI response
3. WHEN testing with "What is the schedule for next week?", THE Hybrid_Chatbot SHALL use rule-based response if pattern exists
4. THE System SHALL log whether each response came from rule-based or AI for monitoring
5. THE System SHALL track the percentage of questions handled by rule-based vs AI
6. WHEN a question triggers AI fallback, THE System SHALL record the question for pattern analysis

## Notes

- Hệ thống hybrid được thiết kế để tối ưu chi phí: 99% câu hỏi đơn giản dùng rule-based (miễn phí), chỉ 1% câu hỏi phức tạp dùng AI
- Google Gemini API free tier cung cấp 15 requests/minute, đủ cho trường hợp fallback hiếm
- Existing chatbot widget và conversation service không cần thay đổi, chỉ mở rộng backend logic
- Laravel 11 project với MySQL database `language_center`
