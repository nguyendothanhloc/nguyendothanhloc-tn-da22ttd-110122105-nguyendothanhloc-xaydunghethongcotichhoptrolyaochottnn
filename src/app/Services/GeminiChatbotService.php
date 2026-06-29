<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatbotService
{
    /**
     * API key for Gemini API
     *
     * @var string
     */
    private string $apiKey;

    /**
     * Gemini model name
     *
     * @var string
     */
    private string $model;

    /**
     * API endpoint URL
     *
     * @var string
     */
    private string $apiEndpoint;

    /**
     * Request timeout in seconds
     *
     * @var int
     */
    private int $timeout;

    /**
     * Temperature for response generation (0.0 to 1.0)
     *
     * @var float
     */
    private float $temperature;

    /**
     * Maximum tokens in response
     *
     * @var int
     */
    private int $maxTokens;

    /**
     * Top-p sampling parameter
     *
     * @var float
     */
    private float $topP;

    /**
     * Top-k sampling parameter
     *
     * @var int
     */
    private int $topK;

    /**
     * Constructor - Load configuration and validate API key
     *
     * @throws Exception If GEMINI_API_KEY is not configured
     */
    public function __construct()
    {
        // Load API key from config
        $apiKey = config('gemini.api_key');

        // Throw exception if API key is missing
        if (empty($apiKey)) {
            throw new Exception('GEMINI_API_KEY is not configured. Please set it in your .env file.');
        }

        $this->apiKey = $apiKey;

        // Load configuration values
        $this->model = config('gemini.model', 'gemini-pro');
        $this->apiEndpoint = config('gemini.api_endpoint', 'https://generativelanguage.googleapis.com/v1beta');
        $this->timeout = config('gemini.timeout', 10);
        $this->temperature = config('gemini.temperature', 0.7);
        $this->maxTokens = config('gemini.max_tokens', 500);
        $this->topP = config('gemini.top_p', 0.95);
        $this->topK = config('gemini.top_k', 40);

        Log::info('GeminiChatbotService initialized', [
            'model' => $this->model,
            'timeout' => $this->timeout,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens
        ]);
    }

    /**
     * Format message and context into Gemini API prompt
     *
     * Creates a comprehensive prompt for the Gemini API that includes:
     * - System instructions for Vietnamese-speaking language center assistant
     * - Formatted student context data (enrollments, schedules, attendance, assessments, payments)
     * - User's question
     *
     * @param string $message User's question
     * @param array $context Student context data
     * @return string Formatted prompt ready for Gemini API
     */
    private function formatPrompt(string $message, array $context): string
    {
        // ===== OPTIMIZED SYSTEM INSTRUCTIONS FOR GEMINI 2.5 =====
        $systemInstructions = "# ROLE\n";
        $systemInstructions .= "Bạn là EduBot - trợ lý AI của Trung tâm Ngoại ngữ.\n\n";
        
        $systemInstructions .= "# CRITICAL RULES (MUST FOLLOW EVERY TIME)\n";
        $systemInstructions .= "1. ĐỌC [Student Context] bên dưới TRƯỚC KHI trả lời\n";
        $systemInstructions .= "2. CHỈ dùng data từ [Student Context] - KHÔNG đoán/tự nghĩ\n";
        $systemInstructions .= "3. TRẢ LỜI ĐẦY ĐỦ với:\n";
        $systemInstructions .= "   - Thông tin chính (📚📅⏰📍)\n";
        $systemInstructions .= "   - Chi tiết cụ thể (ngày, giờ, phòng, giáo viên)\n";
        $systemInstructions .= "   - 1 câu gợi ý/hỏi tiếp ở cuối\n";
        $systemInstructions .= "4. Format: Dùng emoji + bullet points\n";
        $systemInstructions .= "5. Độ dài: 50-150 từ (trừ khi cần chi tiết)\n\n";
        
        
        $systemInstructions .= "# VÍ DỤ TRẢ LỜI ĐÚNG\n";
        $systemInstructions .= "Q: 'Hôm nay tôi học gì?'\n";
        $systemInstructions .= "[Data: 17/06/2026, 18:00-20:00, Phòng 106, Tiếng Anh]\n";
        $systemInstructions .= "A: '📅 Lịch học hôm nay:\n";
        $systemInstructions .= "✅ Tiếng Anh - Buổi 6\n";
        $systemInstructions .= "⏰ 18:00-20:00\n";
        $systemInstructions .= "📍 Phòng 106\n";
        $systemInstructions .= "👨‍🏫 GV: Nguyễn Văn Giáo\n\n";
        $systemInstructions .= "Nhớ mang sách giáo trình nhé! Bạn muốn xem lịch tuần sau không?'\n\n";
        
        $systemInstructions .= "# LƯU Ý QUAN TRỌNG\n";
        $systemInstructions .= "- Nếu HỎI về LỊCH HỌC: Phải liệt kê ĐẦY ĐỦ ngày/giờ/phòng từ [📅 LỊCH HỌC]\n";
        $systemInstructions .= "- Nếu HỎI về ĐIỂM: Phải liệt kê ĐẦY ĐỦ từng điểm từ [📊 KẾT QUẢ]\n";
        $systemInstructions .= "- Nếu data KHÔNG CÓ: Nói 'Chưa có thông tin' + hướng dẫn liên hệ\n";
        $systemInstructions .= "- LUÔN kết thúc bằng 1 câu hỏi gợi ý\n\n";

        // ===== STUDENT CONTEXT DATA =====
        $contextText = "\n═══════════════════════════════════\n";
        $contextText .= "📋 [STUDENT CONTEXT - USE THIS DATA]\n";
        $contextText .= "═══════════════════════════════════\n\n";
        
        // Student basic information
        if (!empty($context['student'])) {
            $student = $context['student'];
            $contextText .= "👤 THÔNG TIN HỌC VIÊN:\n";
            $contextText .= "Tên: " . ($student['name'] ?? 'N/A') . "\n";
            $contextText .= "Trình độ: " . ($student['level'] ?? 'N/A') . "\n";
            if (!empty($student['interests'])) {
                $contextText .= "Sở thích: " . $student['interests'] . "\n";
            }
            $contextText .= "\n";
        }

        // Current enrollments/courses
        if (!empty($context['enrollments']) && count($context['enrollments']) > 0) {
            $contextText .= "📚 KHÓA HỌC HIỆN TẠI:\n";
            foreach ($context['enrollments'] as $idx => $enrollment) {
                $contextText .= ($idx + 1) . ". " . ($enrollment['course_name'] ?? 'N/A');
                $contextText .= " (" . ($enrollment['language'] ?? 'N/A') . " - " . ($enrollment['level'] ?? 'N/A') . ")\n";
                $contextText .= "   Lớp: " . ($enrollment['class_name'] ?? 'N/A') . "\n";
                $contextText .= "   Trạng thái: " . ($enrollment['status'] ?? 'N/A') . "\n";
                $contextText .= "   Giáo viên: " . ($enrollment['teacher_name'] ?? 'N/A') . "\n";
                if (isset($enrollment['completion_percentage'])) {
                    $contextText .= "   Tiến độ: " . $enrollment['completion_percentage'] . "%\n";
                }
                $contextText .= "\n";
            }
        } else {
            $contextText .= "📚 KHÓA HỌC: Chưa đăng ký khóa học nào\n\n";
        }

        // Upcoming classes/schedules
        if (!empty($context['schedules']) && count($context['schedules']) > 0) {
            $contextText .= "📅 LỊCH HỌC SẮP TỚI:\n";
            foreach ($context['schedules'] as $idx => $schedule) {
                $contextText .= ($idx + 1) . ". " . ($schedule['class_name'] ?? 'N/A') . "\n";
                $contextText .= "   Ngày: " . ($schedule['date'] ?? 'N/A') . " (" . ($schedule['day_of_week'] ?? 'N/A') . ")\n";
                $contextText .= "   Giờ: " . ($schedule['start_time'] ?? 'N/A') . " - " . ($schedule['end_time'] ?? 'N/A') . "\n";
                if (!empty($schedule['location'])) {
                    $contextText .= "   Phòng: " . $schedule['location'] . "\n";
                }
                if (!empty($schedule['topic'])) {
                    $contextText .= "   Chủ đề: " . $schedule['topic'] . "\n";
                }
                $contextText .= "\n";
            }
        } else {
            $contextText .= "📅 LỊCH HỌC: Chưa có lịch học được xếp\n\n";
        }

        // Attendance summary
        if (!empty($context['attendance'])) {
            $attendance = $context['attendance'];
            $contextText .= "✅ ĐIỂM DANH:\n";
            $contextText .= "Tổng buổi: " . ($attendance['total_sessions'] ?? 0) . " buổi\n";
            $contextText .= "Có mặt: " . ($attendance['present'] ?? 0) . " buổi\n";
            $contextText .= "Vắng: " . ($attendance['absent'] ?? 0) . " buổi\n";
            $contextText .= "Đi muộn: " . ($attendance['late'] ?? 0) . " lần\n";
            if (isset($attendance['attendance_rate'])) {
                $rate = $attendance['attendance_rate'];
                $status = $rate >= 80 ? '✅' : ($rate >= 60 ? '⚠️' : '❌');
                $contextText .= "Tỷ lệ tham gia: " . number_format($rate, 1) . "% " . $status . "\n";
            }
            $contextText .= "\n";
        }

        // Recent assessments
        if (!empty($context['assessments']) && count($context['assessments']) > 0) {
            $contextText .= "📊 KẾT QUẢ HỌC TẬP:\n";
            $totalScore = 0;
            $count = count($context['assessments']);
            foreach ($context['assessments'] as $idx => $assessment) {
                $score = $assessment['score'] ?? 0;
                $maxScore = $assessment['max_score'] ?? 10;
                $percentage = $maxScore > 0 ? round(($score / $maxScore) * 10, 1) : 0;
                $totalScore += $percentage;
                
                $contextText .= ($idx + 1) . ". " . ($assessment['title'] ?? 'N/A') . "\n";
                $contextText .= "   Điểm: " . $score . "/" . $maxScore . " (" . $percentage . "/10)\n";
                $contextText .= "   Ngày: " . ($assessment['date'] ?? 'N/A') . "\n";
                if (!empty($assessment['feedback'])) {
                    $contextText .= "   Nhận xét: " . $assessment['feedback'] . "\n";
                }
            }
            if ($count > 0) {
                $avgScore = round($totalScore / $count, 1);
                $contextText .= "\n💯 Điểm trung bình: " . $avgScore . "/10\n";
            }
            $contextText .= "\n";
        } else {
            $contextText .= "📊 KẾT QUẢ: Chưa có bài kiểm tra nào\n\n";
        }

        // Payment status
        if (!empty($context['payments']) && count($context['payments']) > 0) {
            $contextText .= "💰 THANH TOÁN:\n";
            foreach ($context['payments'] as $idx => $payment) {
                $status = $payment['status'] ?? 'N/A';
                $statusIcon = $status === 'completed' ? '✅' : '⏳';
                $contextText .= ($idx + 1) . ". Số tiền: " . number_format($payment['amount'] ?? 0) . " VNĐ\n";
                $contextText .= "   Trạng thái: " . $status . " " . $statusIcon . "\n";
                if (!empty($payment['due_date'])) {
                    $contextText .= "   Hạn thanh toán: " . $payment['due_date'] . "\n";
                }
                if (!empty($payment['paid_at'])) {
                    $contextText .= "   Đã thanh toán: " . $payment['paid_at'] . "\n";
                }
                $contextText .= "\n";
            }
        }

        // Append user message with clear separator
        $contextText .= "═══════════════════════════════════\n";
        $contextText .= "❓ CÂU HỎI HỌC VIÊN: " . $message . "\n";
        $contextText .= "═══════════════════════════════════\n\n";
        $contextText .= "💬 TRẢ LỜI ĐẦY ĐỦ (50-150 từ):\n";
        $contextText .= "- Bước 1: Dùng thông tin từ [Student Context] ở trên\n";
        $contextText .= "- Bước 2: Liệt kê chi tiết với emoji + bullet points\n";
        $contextText .= "- Bước 3: Kết thúc bằng 1 câu hỏi gợi ý\n\n";
        $contextText .= "BẮT ĐẦU TRẢ LỜI:\n";

        // Return complete formatted prompt
        return $systemInstructions . $contextText;
    }

    /**
     * Call Gemini API with formatted prompt and retry logic
     *
     * @param string $prompt Formatted prompt for the AI
     * @return string AI-generated response text
     * @throws Exception On API failure after retries
     */
    private function callGeminiAPI(string $prompt): string
    {
        $url = "{$this->apiEndpoint}/models/{$this->model}:generateContent";
        
        // Build request payload according to Gemini API format
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $this->temperature,
                'maxOutputTokens' => $this->maxTokens,
                'topP' => $this->topP,
                'topK' => $this->topK,
            ]
        ];

        $maxRetries = 1; // Single retry as per requirement
        $attempt = 0;

        while ($attempt <= $maxRetries) {
            try {
                $attempt++;
                
                Log::info('Gemini API Request', [
                    'attempt' => $attempt,
                    'url' => $url,
                    'prompt_length' => strlen($prompt)
                ]);

                // Make HTTP POST request with timeout and API key
                // Support both old (AIzaSy...) and new (AQ.Ab...) API key formats
                if (str_starts_with($this->apiKey, 'AIzaSy')) {
                    // Old format: use query parameter
                    $response = Http::timeout($this->timeout)
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                        ])
                        ->post($url . '?key=' . $this->apiKey, $payload);
                } else {
                    // New format (AQ.Ab...): use X-Goog-Api-Key header
                    $response = Http::timeout($this->timeout)
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                            'X-Goog-Api-Key' => $this->apiKey,
                        ])
                        ->post($url, $payload);
                }

                // Check if request was successful
                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Validate response structure
                    if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        throw new Exception('Malformed API response: missing expected fields');
                    }

                    $generatedText = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    Log::info('Gemini API Success', [
                        'attempt' => $attempt,
                        'response_length' => strlen($generatedText)
                    ]);

                    return $generatedText;
                }

                // Handle specific HTTP error codes
                $statusCode = $response->status();
                
                if ($statusCode === 401) {
                    throw new Exception('API authentication failed: Invalid API key', 401);
                }
                
                if ($statusCode === 429) {
                    throw new Exception('API rate limit exceeded', 429);
                }
                
                if ($statusCode === 503) {
                    throw new Exception('API service unavailable', 503);
                }

                // For other errors, check if we should retry
                if ($attempt <= $maxRetries && $this->isTransientError($statusCode)) {
                    // Exponential backoff: wait 2^(attempt-1) seconds
                    $waitTime = pow(2, $attempt - 1);
                    Log::warning("Gemini API transient error, retrying in {$waitTime}s", [
                        'status_code' => $statusCode,
                        'attempt' => $attempt
                    ]);
                    sleep($waitTime);
                    continue;
                }

                // Non-retryable error or max retries reached
                throw new Exception("API request failed with status {$statusCode}: {$response->body()}", $statusCode);

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                // Network/connection errors
                if ($attempt <= $maxRetries) {
                    $waitTime = pow(2, $attempt - 1);
                    Log::warning("Gemini API network error, retrying in {$waitTime}s", [
                        'attempt' => $attempt,
                        'error' => $e->getMessage()
                    ]);
                    sleep($waitTime);
                    continue;
                }
                throw new Exception('Network error: Could not connect to API', 0);
            } catch (\Illuminate\Http\Client\RequestException $e) {
                // Timeout errors
                if (str_contains($e->getMessage(), 'timeout') || str_contains($e->getMessage(), 'timed out')) {
                    if ($attempt <= $maxRetries) {
                        $waitTime = pow(2, $attempt - 1);
                        Log::warning("Gemini API timeout, retrying in {$waitTime}s", [
                            'attempt' => $attempt
                        ]);
                        sleep($waitTime);
                        continue;
                    }
                    throw new Exception('API request timeout', 408);
                }
                // For other request exceptions, re-throw
                throw $e;
            } catch (Exception $e) {
                // For other exceptions, check if it's worth retrying
                $errorMessage = $e->getMessage();
                
                // Check for timeout in error message
                if (str_contains($errorMessage, 'timeout') || str_contains($errorMessage, 'timed out')) {
                    if ($attempt <= $maxRetries) {
                        $waitTime = pow(2, $attempt - 1);
                        Log::warning("Gemini API timeout, retrying in {$waitTime}s", [
                            'attempt' => $attempt
                        ]);
                        sleep($waitTime);
                        continue;
                    }
                    throw new Exception('API request timeout', 408);
                }

                // For malformed response or other API errors, don't retry
                throw $e;
            }
        }

        // Should never reach here, but just in case
        throw new Exception('API request failed after maximum retries');
    }

    /**
     * Check if an HTTP status code represents a transient error worth retrying
     *
     * @param int $statusCode HTTP status code
     * @return bool True if error is transient
     */
    private function isTransientError(int $statusCode): bool
    {
        // Retry on: 408 (Timeout), 500 (Internal Server Error), 502 (Bad Gateway), 503 (Service Unavailable), 504 (Gateway Timeout)
        return in_array($statusCode, [408, 500, 502, 503, 504]);
    }

    /**
     * Get all FAQ from database
     * 
     * @return array FAQ data with questions and answers
     */
    private function getFAQContext(): array
    {
        try {
            $faqs = \App\Models\ChatbotKnowledge::where('is_active', true)
                ->orderBy('category')
                ->orderBy('priority', 'desc')
                ->get();
            
            $faqData = [];
            foreach ($faqs as $faq) {
                $faqData[] = [
                    'category' => $faq->category ?? 'General',
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'keywords' => $faq->keywords ?? ''
                ];
            }
            
            return $faqData;
        } catch (\Exception $e) {
            Log::warning('Could not load FAQ context', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Build comprehensive context for a student
     * 
     * @param int $studentId Student database ID
     * @return array Structured context including enrollments, schedules, attendance, assessments, payments
     */
    private function buildStudentContext(int $studentId): array
    {
        // Query Student model with eager loading for all related data
        $student = \App\Models\Student::with([
            'user',
            'enrollments' => function ($query) {
                $query->whereIn('status', ['paid', 'pending', 'completed', 'approved'])
                      ->whereHas('class', function($q) {
                          $q->where('status', '!=', 'cancelled');
                      })
                      ->limit(5);
            },
            'enrollments.class.course',
            'enrollments.class.teacher.user',
            'enrollments.payment',
            'attendances' => function ($query) {
                $query->latest()->limit(10);
            },
            'attendances.schedule.class',
            'assessmentScores' => function ($query) {
                $query->latest()->limit(5);
            },
            'assessmentScores.assessment'
        ])->find($studentId);

        // Handle case where student not found
        if (!$student) {
            Log::warning('Student not found for context building', ['student_id' => $studentId]);
            return [];
        }

        // Build student info (filter sensitive data)
        $studentInfo = [
            'id' => $student->id,
            'name' => $student->user->name ?? 'N/A',
            'level' => $student->level ?? 'N/A',
            'interests' => $student->interests ?? 'N/A'
        ];

        // Build enrollments data
        $enrollments = [];
        foreach ($student->enrollments as $enrollment) {
            $enrollments[] = [
                'class_name' => $enrollment->class->name ?? 'N/A',
                'course_name' => $enrollment->class->course->name ?? 'N/A',
                'language' => $enrollment->class->course->language ?? 'N/A',
                'level' => $enrollment->class->course->level ?? 'N/A',
                'status' => $enrollment->status,
                'enrollment_date' => $enrollment->enrollment_date ? $enrollment->enrollment_date->format('d/m/Y') : 'N/A',
                'completion_percentage' => $enrollment->completion_percentage ?? 0,
                'teacher_name' => $enrollment->class->teacher->user->name ?? 'N/A'
            ];
        }

        // Build schedules data - Get upcoming schedules for enrolled classes (exclude cancelled)
        $classIds = $student->enrollments->pluck('class_id')->toArray();
        $schedules = [];
        if (!empty($classIds)) {
            $upcomingSchedules = \App\Models\Schedule::whereIn('class_id', $classIds)
                ->whereHas('class', function($query) {
                    $query->where('status', '!=', 'cancelled');
                })
                ->where('date', '>=', now())
                ->orderBy('date')
                ->orderBy('start_time')
                ->limit(10)
                ->with('class')
                ->get();

            foreach ($upcomingSchedules as $schedule) {
                $schedules[] = [
                    'class_name' => $schedule->class->name ?? 'N/A',
                    'date' => $schedule->date ? $schedule->date->format('d/m/Y') : 'N/A',
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'day_of_week' => $schedule->date ? $schedule->date->format('l') : 'N/A',
                    'location' => $schedule->location ?? 'N/A',
                    'topic' => $schedule->topic ?? 'N/A'
                ];
            }
        }

        // Build attendance summary
        $totalAttendances = $student->attendances->count();
        $presentCount = $student->attendances->where('status', 'present')->count();
        $absentCount = $student->attendances->where('status', 'absent')->count();
        $lateCount = $student->attendances->where('status', 'late')->count();
        $attendanceRate = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 2) : 0;

        $attendance = [
            'total_sessions' => $totalAttendances,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'attendance_rate' => $attendanceRate
        ];

        // Build recent assessments data
        $assessments = [];
        foreach ($student->assessmentScores as $score) {
            $assessments[] = [
                'title' => $score->assessment->name ?? 'N/A',
                'type' => $score->assessment->type ?? 'N/A',
                'score' => $score->score,
                'max_score' => $score->assessment->max_score ?? 100,
                'date' => $score->assessment->assessment_date ? $score->assessment->assessment_date->format('d/m/Y') : 'N/A',
                'feedback' => $score->feedback ?? 'No feedback'
            ];
        }

        // Build payment status
        $payments = [];
        foreach ($student->enrollments as $enrollment) {
            if ($enrollment->payment) {
                $payments[] = [
                    'amount' => $enrollment->payment->amount,
                    'status' => $enrollment->payment->status,
                    'due_date' => $enrollment->payment->due_date ? $enrollment->payment->due_date->format('d/m/Y') : 'N/A',
                    'paid_at' => $enrollment->payment->paid_date ? $enrollment->payment->paid_date->format('d/m/Y') : null,
                    'payment_method' => $enrollment->payment->payment_method ?? 'N/A'
                ];
            }
        }

        // Return structured context array
        $context = [
            'student' => $studentInfo,
            'enrollments' => $enrollments,
            'schedules' => $schedules,
            'attendance' => $attendance,
            'assessments' => $assessments,
            'payments' => $payments
        ];

        Log::info('Student context built successfully', [
            'student_id' => $studentId,
            'enrollments_count' => count($enrollments),
            'schedules_count' => count($schedules),
            'assessments_count' => count($assessments),
            'payments_count' => count($payments)
        ]);

        return $context;
    }

    /**
     * Handle API errors and return user-friendly messages
     *
     * This method detects 8 error types and returns appropriate Vietnamese error messages:
     * 1. Missing API key
     * 2. Invalid API key (401)
     * 3. Rate limit exceeded (429)
     * 4. Request timeout
     * 5. Network error
     * 6. Service unavailable (503)
     * 7. Malformed response
     * 8. Student not found
     *
     * @param Exception $e The exception from API call
     * @param int|null $studentId Student ID for logging context
     * @param string|null $userMessage User's message for logging context
     * @param mixed $apiResponse API response for logging (if available)
     * @return string User-friendly Vietnamese error message
     */
    private function handleAPIError(Exception $e, ?int $studentId = null, ?string $userMessage = null, $apiResponse = null): string
    {
        $errorMessage = $e->getMessage();
        $errorType = 'unknown';
        $userFriendlyMessage = 'Xin lỗi, có lỗi xảy ra khi xử lý yêu cầu của bạn. Vui lòng thử lại sau.';

        // Detect error type and set appropriate message
        
        // 1. Missing API key (should be caught in constructor, but handle here too)
        if (stripos($errorMessage, 'API key') !== false && stripos($errorMessage, 'not configured') !== false) {
            $errorType = 'missing_api_key';
            $userFriendlyMessage = 'Xin lỗi, hệ thống chatbot AI chưa được cấu hình đúng. Vui lòng liên hệ quản trị viên.';
        }
        // 2. Invalid API key (401 Unauthorized)
        elseif (stripos($errorMessage, '401') !== false || stripos($errorMessage, 'unauthorized') !== false || stripos($errorMessage, 'invalid') !== false && stripos($errorMessage, 'key') !== false) {
            $errorType = 'invalid_api_key';
            $userFriendlyMessage = 'Xin lỗi, xác thực API thất bại. Vui lòng liên hệ quản trị viên.';
        }
        // 3. Rate limit exceeded (429 Too Many Requests)
        elseif (stripos($errorMessage, '429') !== false || stripos($errorMessage, 'rate limit') !== false || stripos($errorMessage, 'too many requests') !== false) {
            $errorType = 'rate_limit_exceeded';
            $userFriendlyMessage = 'Xin lỗi, hệ thống đang xử lý nhiều yêu cầu. Vui lòng thử lại sau vài giây.';
        }
        // 4. Request timeout
        elseif (stripos($errorMessage, 'timeout') !== false || stripos($errorMessage, 'timed out') !== false) {
            $errorType = 'timeout';
            $userFriendlyMessage = 'Xin lỗi, yêu cầu của bạn mất quá nhiều thời gian. Vui lòng thử lại.';
        }
        // 5. Network error (connection failed, DNS resolution failure, etc.)
        elseif (stripos($errorMessage, 'connection') !== false || stripos($errorMessage, 'network') !== false || stripos($errorMessage, 'resolve') !== false || stripos($errorMessage, 'unreachable') !== false) {
            $errorType = 'network_error';
            $userFriendlyMessage = 'Xin lỗi, không thể kết nối đến dịch vụ AI. Vui lòng kiểm tra kết nối mạng và thử lại.';
        }
        // 6. Service unavailable (503)
        elseif (stripos($errorMessage, '503') !== false || stripos($errorMessage, 'service unavailable') !== false || stripos($errorMessage, 'unavailable') !== false) {
            $errorType = 'service_unavailable';
            $userFriendlyMessage = 'Xin lỗi, dịch vụ AI tạm thời không khả dụng. Vui lòng thử lại sau.';
        }
        // 7. Malformed response (missing expected fields)
        elseif (stripos($errorMessage, 'malformed') !== false || stripos($errorMessage, 'parse') !== false || stripos($errorMessage, 'invalid response') !== false || stripos($errorMessage, 'unexpected format') !== false) {
            $errorType = 'malformed_response';
            $userFriendlyMessage = 'Xin lỗi, có lỗi khi xử lý phản hồi từ AI. Vui lòng thử lại.';
        }
        // 8. Student not found
        elseif (stripos($errorMessage, 'student not found') !== false || stripos($errorMessage, 'student') !== false && stripos($errorMessage, 'not found') !== false) {
            $errorType = 'student_not_found';
            $userFriendlyMessage = 'Không tìm thấy thông tin học viên.';
        }

        // Log error with full context
        Log::error('Gemini API Error', [
            'error_type' => $errorType,
            'message' => $errorMessage,
            'student_id' => $studentId,
            'user_message' => $userMessage ? substr($userMessage, 0, 100) : null, // Limit to 100 chars
            'api_response' => $apiResponse,
            'timestamp' => now()->toIso8601String(),
            'exception_class' => get_class($e),
            'trace' => $e->getTraceAsString()
        ]);

        return $userFriendlyMessage;
    }

    /**
     * Generate AI response for a message with student context
     *
     * This is the main public method that:
     * 1. Builds student context from database
     * 2. Formats prompt with system instructions and context
     * 3. Calls Gemini API
     * 4. Returns AI-generated response or handles errors
     *
     * @param string $message User's question
     * @param int $studentId Student ID for context building
     * @return string AI-generated response or error message
     */
    public function generateResponse(string $message, int $studentId): string
    {
        try {
            Log::info('Gemini: Generating AI response', [
                'student_id' => $studentId,
                'message_preview' => substr($message, 0, 50)
            ]);

            // Step 1: Build student context
            $context = $this->buildStudentContext($studentId);

            // Check if context is empty (student not found)
            if (empty($context)) {
                throw new Exception('Student not found');
            }

            // Step 2: Format prompt with system instructions and context
            $prompt = $this->formatPrompt($message, $context);

            // Step 3: Call Gemini API
            $response = $this->callGeminiAPI($prompt);

            Log::info('Gemini: AI response generated successfully', [
                'student_id' => $studentId,
                'response_length' => strlen($response)
            ]);

            return $response;

        } catch (Exception $e) {
            // Step 4: Handle errors and return user-friendly message
            return $this->handleAPIError($e, $studentId, $message);
        }
    }
}
