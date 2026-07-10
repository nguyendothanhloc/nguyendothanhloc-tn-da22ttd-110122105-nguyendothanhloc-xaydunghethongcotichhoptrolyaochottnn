# Requirements Document

## Introduction

This document specifies the requirements for an Admin FAQ Management System that will serve as Layer 2 (Knowledge Base) in the three-layer chatbot architecture for the Language Center Management System. The system enables administrators to create, manage, and maintain a FAQ knowledge base for frequently asked questions about policies, regulations, and procedures that are not handled by dynamic pattern matching (Layer 1) and do not require AI processing (Layer 3).

The FAQ system will integrate seamlessly with the existing chatbot infrastructure (RuleBasedChatbotService, GeminiChatbotService, ChatbotController) by intercepting user queries after pattern matching fails and before AI fallback, providing fast, accurate, and cost-effective responses to policy-related questions.

## Glossary

- **FAQ_System**: The complete Admin FAQ Management System including admin UI and chatbot integration
- **Admin**: User with admin role who can create, read, update, and delete FAQ entries
- **FAQ_Entry**: A single knowledge base entry containing category, question, answer, keywords, priority, and active status
- **Knowledge_Base**: The collection of all active FAQ entries in the database
- **Chatbot_Service**: The RuleBasedChatbotService that will be modified to query the FAQ layer
- **Normalized_Text**: Text with Vietnamese accents removed and converted to lowercase for matching
- **Pattern_Matching**: Layer 1 - Rule-based responses for dynamic data queries (schedules, grades, etc.)
- **FAQ_Layer**: Layer 2 - Knowledge base responses for policy and regulation questions
- **AI_Fallback**: Layer 3 - Gemini AI service for complex questions not handled by Layer 1 or 2

## Requirements

### Requirement 1: FAQ Entry Management

**User Story:** As an admin, I want to create and manage FAQ entries, so that students can receive accurate answers to policy questions without requiring AI processing.

#### Acceptance Criteria

1. WHEN an admin accesses the FAQ management interface, THE FAQ_System SHALL display all existing FAQ entries with their category, question preview, active status, and priority
2. WHEN an admin creates a new FAQ entry, THE FAQ_System SHALL require category, question, answer, and keywords fields before saving
3. WHEN an admin updates an FAQ entry, THE FAQ_System SHALL validate that all required fields are non-empty and save the changes
4. WHEN an admin deletes an FAQ entry, THE FAQ_System SHALL permanently remove it from the Knowledge_Base
5. WHEN an admin toggles the active status of an FAQ entry, THE FAQ_System SHALL immediately update the entry's visibility to the chatbot

### Requirement 2: FAQ Entry Data Model

**User Story:** As a developer, I want a well-structured FAQ data model, so that entries can be efficiently stored, retrieved, and searched.

#### Acceptance Criteria

1. THE FAQ_System SHALL store each FAQ_Entry with fields: id, category, question, answer, keywords, priority (integer), is_active (boolean), created_at, updated_at
2. THE FAQ_System SHALL enforce unique constraints on the combination of normalized question text within the same category
3. THE FAQ_System SHALL allow priority values from 1 to 100, with higher numbers indicating higher priority
4. THE FAQ_System SHALL default new entries to is_active = true and priority = 50
5. THE FAQ_System SHALL index the question and keywords fields for efficient text searching

### Requirement 3: FAQ Search Integration

**User Story:** As a student, I want the chatbot to automatically search the FAQ knowledge base when my question doesn't match a pattern, so that I get accurate policy information without waiting for AI processing.

#### Acceptance Criteria

1. WHEN Pattern_Matching returns null (no match), THE Chatbot_Service SHALL query the Knowledge_Base before invoking AI_Fallback
2. WHEN searching the Knowledge_Base, THE Chatbot_Service SHALL normalize both the user message and stored questions/keywords by removing Vietnamese accents and converting to lowercase
3. WHEN a search query matches multiple FAQ entries, THE Chatbot_Service SHALL return the entry with the highest priority value
4. WHEN a search finds a matching FAQ entry, THE Chatbot_Service SHALL return the answer with response type 'faq' and include the matched entry data
5. WHEN no FAQ entry matches the search query, THE Chatbot_Service SHALL proceed to AI_Fallback

### Requirement 4: Text Normalization for Matching

**User Story:** As a student, I want my questions to match FAQ entries regardless of Vietnamese accent marks or letter casing, so that I can ask questions naturally without worrying about exact text formatting.

#### Acceptance Criteria

1. THE FAQ_System SHALL provide a text normalization function that removes all Vietnamese diacritical marks from text
2. WHEN normalizing text, THE FAQ_System SHALL convert Vietnamese characters (à, á, ạ, ả, ã, â, ầ, ấ, ậ, ẩ, ẫ, ă, ằ, ắ, ặ, ẳ, ẵ, è, é, ẹ, ẻ, ẽ, ê, ề, ế, ệ, ể, ễ, ì, í, ị, ỉ, ĩ, ò, ó, ọ, ỏ, õ, ô, ồ, ố, ộ, ổ, ỗ, ơ, ờ, ớ, ợ, ở, ỡ, ù, ú, ụ, ủ, ũ, ư, ừ, ứ, ự, ử, ữ, ỳ, ý, ỵ, ỷ, ỹ, đ) to their base equivalents (a, e, i, o, u, d)
3. WHEN normalizing text, THE FAQ_System SHALL convert all characters to lowercase
4. THE FAQ_System SHALL apply text normalization to both user queries and stored question/keyword fields during search
5. THE FAQ_System SHALL preserve the original text with accents in the database for display purposes

### Requirement 5: FAQ Category Organization

**User Story:** As an admin, I want to organize FAQ entries by category, so that I can logically group related questions and maintain the knowledge base efficiently.

#### Acceptance Criteria

1. THE FAQ_System SHALL support the following predefined categories: "Chính sách hoàn tiền", "Quy định chuyển lớp", "Thủ tục nghỉ học / bảo lưu", "Điều kiện nhận ưu đãi / giảm giá", "Khác"
2. WHEN displaying FAQ entries in the admin interface, THE FAQ_System SHALL allow filtering by category
3. WHEN creating or editing an FAQ entry, THE FAQ_System SHALL provide a dropdown select for choosing the category
4. THE FAQ_System SHALL allow admins to view the count of active entries per category
5. THE FAQ_System SHALL display categories with zero entries but allow admins to create entries for them

### Requirement 6: Keyword-Based Search Enhancement

**User Story:** As a student, I want my questions to match FAQ entries even when I use different wording, so that I can find answers using my own vocabulary.

#### Acceptance Criteria

1. WHEN an admin creates an FAQ entry, THE FAQ_System SHALL allow specifying multiple keywords separated by commas
2. WHEN searching for FAQ entries, THE Chatbot_Service SHALL check if any normalized keyword contains the normalized search query
3. WHEN searching for FAQ entries, THE Chatbot_Service SHALL check if the normalized question text contains the normalized search query
4. WHEN both question and keywords match, THE Chatbot_Service SHALL prioritize the match with higher priority value
5. THE FAQ_System SHALL display keywords in the admin interface for easy review and editing

### Requirement 7: FAQ Search Result Formatting

**User Story:** As a student, I want FAQ answers to be clearly formatted and informative, so that I understand the policy or procedure immediately.

#### Acceptance Criteria

1. WHEN the Chatbot_Service returns an FAQ answer, THE response SHALL include a prefix indicating it came from the knowledge base (e.g., "📚 Từ cơ sở tri thức:")
2. WHEN formatting the FAQ response, THE Chatbot_Service SHALL include the question as a header followed by the answer
3. WHEN returning the FAQ response, THE Chatbot_Service SHALL set response type to 'faq' to distinguish it from pattern matching and AI responses
4. THE FAQ_System SHALL preserve line breaks and formatting in the answer field when storing and displaying
5. WHEN an FAQ answer contains special characters or Vietnamese text, THE Chatbot_Service SHALL return it without encoding issues

### Requirement 8: Admin Interface Design

**User Story:** As an admin, I want a clean and intuitive FAQ management interface, so that I can efficiently maintain the knowledge base without technical expertise.

#### Acceptance Criteria

1. THE FAQ_System SHALL provide an admin page at route '/admin/faq' with authentication middleware requiring admin role
2. WHEN viewing the FAQ list, THE Admin SHALL see a table displaying category, question preview (first 100 characters), priority, active status, and action buttons (Edit, Delete)
3. WHEN creating or editing an FAQ, THE Admin SHALL see a form with labeled fields for category (dropdown), question (textarea), answer (textarea), keywords (text input), priority (number input), and is_active (checkbox)
4. THE FAQ management interface SHALL use Tailwind CSS consistent with existing admin pages (admin dashboard, class management, course management)
5. WHEN saving an FAQ entry, THE FAQ_System SHALL display success or error messages using the existing notification pattern

### Requirement 9: FAQ Search Performance

**User Story:** As a system operator, I want FAQ searches to be fast and efficient, so that chatbot response times remain under 200ms for FAQ queries.

#### Acceptance Criteria

1. THE FAQ_System SHALL create database indexes on the question and keywords columns in the chatbot_knowledge table
2. WHEN searching for FAQ entries, THE Chatbot_Service SHALL query only active entries (is_active = true)
3. WHEN multiple FAQ entries match the search query, THE Chatbot_Service SHALL order results by priority DESC and return only the top match
4. THE FAQ_System SHALL limit keyword field length to 500 characters to maintain search performance
5. THE FAQ_System SHALL limit the number of active FAQ entries to a maximum of 500 to ensure consistent search performance

### Requirement 10: FAQ Entry Validation

**User Story:** As an admin, I want the system to validate FAQ entries before saving, so that the knowledge base maintains high quality and searchability.

#### Acceptance Criteria

1. WHEN creating or updating an FAQ entry, THE FAQ_System SHALL validate that the question field is at least 10 characters long
2. WHEN creating or updating an FAQ entry, THE FAQ_System SHALL validate that the answer field is at least 20 characters long
3. WHEN creating or updating an FAQ entry, THE FAQ_System SHALL validate that priority is an integer between 1 and 100
4. WHEN creating or updating an FAQ entry with a duplicate normalized question in the same category, THE FAQ_System SHALL reject the save and display an error message
5. THE FAQ_System SHALL trim whitespace from question, answer, and keywords fields before saving
