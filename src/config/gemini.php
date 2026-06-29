<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gemini API Key
    |--------------------------------------------------------------------------
    |
    | Your Google Gemini API key from https://aistudio.google.com/app/apikey
    | This key is required to authenticate requests to the Gemini API.
    |
    */
    'api_key' => env('GEMINI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Gemini Model
    |--------------------------------------------------------------------------
    |
    | The Gemini model to use (e.g., gemini-pro, gemini-pro-vision)
    | Default: gemini-pro for text generation
    |
    */
    'model' => env('GEMINI_MODEL', 'gemini-pro'),

    /*
    |--------------------------------------------------------------------------
    | API Endpoint
    |--------------------------------------------------------------------------
    |
    | The base URL for Gemini API
    |
    */
    'api_endpoint' => env('GEMINI_API_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time (in seconds) to wait for API response
    | Default: 10 seconds
    |
    */
    'timeout' => env('GEMINI_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Generation Parameters
    |--------------------------------------------------------------------------
    |
    | Control the creativity and length of AI responses
    |
    | temperature: Controls randomness (0.0 to 1.0)
    |              Lower values = more focused/deterministic
    |              Higher values = more creative/random
    |
    | max_tokens: Maximum number of tokens in the response
    |             (roughly 4 characters per token)
    |
    | top_p: Controls diversity via nucleus sampling (0.0 to 1.0)
    |        Lower values = less diverse responses
    |
    | top_k: Controls diversity via top-k sampling
    |        Only considers the k most likely tokens
    |
    */
    'temperature' => env('GEMINI_TEMPERATURE', 0.7),
    'max_tokens' => env('GEMINI_MAX_TOKENS', 500),
    'top_p' => env('GEMINI_TOP_P', 0.95),
    'top_k' => env('GEMINI_TOP_K', 40),
];
