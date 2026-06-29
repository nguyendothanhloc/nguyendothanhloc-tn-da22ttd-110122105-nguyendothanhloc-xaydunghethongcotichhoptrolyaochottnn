<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotKnowledge extends Model
{
    use HasFactory;

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

    protected $attributes = [
        'priority' => 50,
        'is_active' => true
    ];

    /**
     * Validation rules for creating/updating FAQ entries
     * 
     * @return array
     */
    public static function validationRules(): array
    {
        return [
            'category' => 'required|string|max:100',
            'question' => 'required|string|min:10',
            'answer' => 'required|string|min:20',
            'keywords' => 'nullable|string|max:500',
            'priority' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Predefined categories for FAQ entries
     * 
     * @return array
     */
    public static function categories(): array
    {
        return [
            'Chính sách hoàn tiền',
            'Quy định chuyển lớp',
            'Thủ tục nghỉ học / bảo lưu',
            'Điều kiện nhận ưu đãi / giảm giá',
            'Khác'
        ];
    }

    /**
     * Get normalized question (remove Vietnamese accents, lowercase)
     * 
     * @return string
     */
    public function getNormalizedQuestionAttribute(): string
    {
        return $this->removeVietnameseAccents($this->question ?? '');
    }

    /**
     * Get normalized keywords (remove Vietnamese accents, lowercase)
     * 
     * @return string
     */
    public function getNormalizedKeywordsAttribute(): string
    {
        return $this->removeVietnameseAccents($this->keywords ?? '');
    }

    /**
     * Remove Vietnamese accents and convert to lowercase
     * 
     * @param string $str
     * @return string
     */
    private function removeVietnameseAccents(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        
        $vietnameseMap = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd'
        ];
        
        return strtr($str, $vietnameseMap);
    }

    /**
     * Scope to filter only active FAQ entries
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to search text in normalized question and keywords
     * 
     * This implementation works for both SQLite (testing) and MySQL (production).
     * For production with MySQL and FULLTEXT indexes, consider using MATCH AGAINST for better performance.
     * 
     * The search strategy:
     * 1. Try to match the normalized search text against the stored text (case-insensitive)
     * 2. This works when users type without accents
     * 3. For accent-insensitive matching, we check if any word in the search matches any word in the text
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $searchText
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchText($query, string $searchText)
    {
        // Create a temporary instance to access the normalization method
        $instance = new static();
        $normalizedSearch = $instance->removeVietnameseAccents($searchText);
        
        // Split search into words for better matching
        $searchWords = array_filter(explode(' ', $normalizedSearch));
        
        return $query->where(function ($q) use ($normalizedSearch, $searchWords) {
            // Strategy 1: Direct substring match (for queries without accents)
            $q->whereRaw('LOWER(question) LIKE ?', ['%' . $normalizedSearch . '%'])
              ->orWhereRaw('LOWER(keywords) LIKE ?', ['%' . $normalizedSearch . '%']);
            
            // Strategy 2: Word-by-word matching for better accent handling
            // This helps when the database has accents but search doesn't
            foreach ($searchWords as $word) {
                $q->orWhereRaw('LOWER(question) LIKE ?', ['%' . $word . '%'])
                  ->orWhereRaw('LOWER(keywords) LIKE ?', ['%' . $word . '%']);
            }
        });
    }
}
