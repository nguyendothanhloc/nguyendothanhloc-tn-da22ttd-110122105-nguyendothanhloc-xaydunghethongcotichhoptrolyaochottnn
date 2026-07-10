# Implementation Plan: Admin FAQ Management System

## Overview

This implementation plan builds the Admin FAQ Management System as Layer 2 (Knowledge Base) in the three-layer chatbot architecture. The system consists of database schema, Eloquent model with validation, admin CRUD interface with Blade views, and chatbot service integration for FAQ search.

Implementation follows this order:
1. Database foundation (migration, model, seeding)
2. Core FAQ search logic in chatbot service
3. Admin interface (routes, controller, views)
4. Integration and testing

## Tasks

- [x] 1. Create database migration and model
  - Create migration for `chatbot_knowledge` table with all fields (id, category, question, answer, keywords, priority, is_active, timestamps)
  - Add indexes: FULLTEXT on question/keywords, standard on is_active/category/priority
  - Add composite index on (category, is_active, priority)
  - Create `ChatbotKnowledge` Eloquent model with fillable, casts, and validation rules
  - _Requirements: 2.1, 2.3, 2.4, 2.5, 9.1_

- [ ]* 1.1 Write property test for default values
  - **Property 6: Default Values Applied**
  - Generate random FAQ entries without is_active/priority, verify defaults (true, 50)
  - **Validates: Requirements 2.4**

- [ ]* 1.2 Write property test for priority range validation
  - **Property 5: Priority Range Validation**
  - Generate random priorities (including <1 and >100), verify only 1-100 accepted
  - **Validates: Requirements 2.3, 10.3**

- [x] 2. Implement model accessors and scopes
  - Add `getNormalizedQuestionAttribute()` accessor using `removeVietnameseAccents()`
  - Add `getNormalizedKeywordsAttribute()` accessor using `removeVietnameseAccents()`
  - Add `scopeActive()` for filtering is_active = true
  - Add `scopeByCategory()` for filtering by category
  - Add `scopeSearchText()` for text matching on normalized question/keywords
  - _Requirements: 4.1, 4.2, 4.3_

- [ ]* 2.1 Write property test for Vietnamese accent normalization
  - **Property 7: Vietnamese Accent Normalization**
  - Generate random Vietnamese text with various diacritics, verify all removed and lowercase
  - **Validates: Requirements 4.1, 4.2, 4.3**

- [ ]* 2.2 Write property test for text round-trip preservation
  - **Property 10: Text Round-Trip Preservation**
  - Create random FAQ entries with accents/line breaks, save/reload, verify original preserved
  - **Validates: Requirements 4.5, 7.4, 7.5**

- [x] 3. Add FAQ search method to RuleBasedChatbotService
  - Implement `searchFAQ(string $message): ?array` method
  - Normalize user message using existing `removeVietnameseAccents()`
  - Query active FAQ entries with normalized text matching
  - Return highest priority match with response structure: ['response' => string, 'type' => 'faq', 'data' => entry]
  - Format response with prefix "📚 Từ cơ sở tri thức:" and include question + answer
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 6.2, 6.3, 7.1, 7.2, 7.3_

- [ ]* 3.1 Write property test for highest priority match
  - **Property 8: Highest Priority Match Returned**
  - Create multiple matching FAQ entries with different priorities, verify highest returned
  - **Validates: Requirements 3.3, 6.4, 9.3**

- [ ]* 3.2 Write property test for FAQ response structure
  - **Property 9: FAQ Response Structure**
  - Generate random FAQ entries, search for them, verify response type/prefix/structure
  - **Validates: Requirements 3.4, 7.1, 7.2, 7.3**

- [ ]* 3.3 Write property test for keyword substring matching
  - **Property 11: Keyword Substring Matching**
  - Create FAQ entries with keywords, search with substrings, verify matches found
  - **Validates: Requirements 6.2, 6.3**

- [ ]* 3.4 Write property test for active status controls visibility
  - **Property 3: Active Status Controls Visibility**
  - Create active/inactive entries, search, verify only active returned
  - **Validates: Requirements 1.5, 9.2**

- [x] 4. Integrate FAQ layer into processMessage workflow
  - Modify `RuleBasedChatbotService::processMessage()` to call `searchFAQ()` after pattern match fails
  - Add logging for FAQ matches (similar to existing pattern match logging)
  - Ensure AI fallback only called if both pattern match and FAQ search return null
  - _Requirements: 3.1, 3.5_

- [ ]* 4.1 Write integration test for layer order
  - Verify processMessage calls pattern → FAQ → AI in correct order
  - Mock AI service to test fallback behavior
  - _Requirements: 3.1, 3.5_

- [ ] 5. Checkpoint - Ensure chatbot integration tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Create ChatbotKnowledgeController with CRUD methods
  - Add authentication and role:admin middleware
  - Implement index() method with category filtering and pagination
  - Implement create() method returning create view
  - Implement store() method with validation (required fields, lengths, priority range, uniqueness)
  - Implement edit() method returning edit view
  - Implement update() method with validation
  - Implement destroy() method
  - Add success/error flash messages
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 8.1, 8.5, 10.1, 10.2, 10.3, 10.4_

- [ ]* 6.1 Write property test for required field validation
  - **Property 1: Required Field Validation**
  - Generate random FAQ entries with missing required fields, verify rejection
  - **Validates: Requirements 1.2, 1.3**

- [ ]* 6.2 Write property test for FAQ deletion
  - **Property 2: FAQ Deletion Removes Entry**
  - Create random entries, delete them, verify no longer in database
  - **Validates: Requirements 1.4**

- [ ]* 6.3 Write property test for unique normalized question
  - **Property 4: Unique Normalized Question Per Category**
  - Create entry, attempt duplicate with same normalized question in same category, verify rejection
  - **Validates: Requirements 2.2, 10.4**

- [ ]* 6.4 Write property test for field length validation
  - **Property 13: Field Length Validation**
  - Generate entries with various field lengths (question <10, answer <20, keywords >500), verify rejection
  - **Validates: Requirements 9.4, 10.1, 10.2**

- [ ]* 6.5 Write property test for whitespace trimming
  - **Property 15: Whitespace Trimming**
  - Create entries with whitespace padding, verify trimmed after save
  - **Validates: Requirements 10.5**

- [x] 7. Create admin views with Tailwind CSS styling
  - Create `resources/views/admin/chatbot-knowledge/index.blade.php` with FAQ list table
  - Add category filter dropdown, search box, and pagination
  - Display columns: category, question preview (100 chars), priority, active status, actions (Edit, Delete)
  - Create `resources/views/admin/chatbot-knowledge/create.blade.php` with form
  - Form fields: category (dropdown with predefined categories), question (textarea), answer (textarea), keywords (text), priority (number 1-100), is_active (checkbox)
  - Create `resources/views/admin/chatbot-knowledge/edit.blade.php` with pre-filled form
  - Match styling of existing admin pages (admin dashboard, classes, courses)
  - _Requirements: 5.1, 5.2, 5.3, 8.2, 8.3, 8.4_

- [ ]* 7.1 Write integration test for admin interface
  - Test index page renders with FAQ data
  - Test create form submission
  - Test edit form submission
  - Test delete functionality
  - _Requirements: 8.1, 8.2, 8.3, 8.5_

- [x] 8. Add routes and navigation
  - Register resource routes for `admin/chatbot-knowledge` in `routes/web.php`
  - Apply `auth` and `role:admin` middleware
  - Add "FAQ Management" link to admin sidebar navigation
  - _Requirements: 8.1_

- [x] 9. Create database seeder with example FAQs
  - Create `ChatbotKnowledgeSeeder` with 5-10 example FAQ entries
  - Cover all predefined categories
  - Include Vietnamese text with accents and typical policy questions
  - Set varying priorities for testing
  - _Requirements: 5.1_

- [ ]* 9.1 Write property test for category entry count
  - **Property 12: Category Entry Count Accuracy**
  - Create random entries in various categories, verify counts accurate
  - **Validates: Requirements 5.4**

- [ ]* 9.2 Write property test for maximum entry limit
  - **Property 14: Maximum Entry Limit Enforcement**
  - Create 500 active entries, attempt 501st, verify rejection
  - **Validates: Requirements 9.5**

- [ ] 10. Final checkpoint - End-to-end testing
  - Run all property tests (minimum 100 iterations each)
  - Run integration tests
  - Manually test admin UI flow: create, edit, delete FAQ
  - Manually test chatbot flow: ask question that matches FAQ, verify Layer 2 response
  - Check that FAQ search occurs between pattern matching and AI fallback
  - Verify Vietnamese accent matching works correctly
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Property tests are marked with `*` and can be skipped for faster MVP delivery
- Each property test should be tagged with `Feature: admin-faq-management-system, Property {number}: {property_text}`
- Use Eris library for PHP property-based testing (install via composer: `giorgiosironi/eris`)
- All property tests must run minimum 100 iterations to ensure coverage
- Reuse existing `removeVietnameseAccents()` method from RuleBasedChatbotService
- Database indexes are critical for search performance - verify they are created correctly
- Admin interface should match styling of existing admin pages (check `resources/views/admin/dashboard.blade.php` for reference)

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1"] },
    { "id": 1, "tasks": ["2"] },
    { "id": 2, "tasks": ["3"] },
    { "id": 3, "tasks": ["4"] },
    { "id": 4, "tasks": ["6", "7", "9"] },
    { "id": 5, "tasks": ["8"] }
  ]
}
```
