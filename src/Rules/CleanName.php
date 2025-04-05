<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class CleanName implements Rule
{
    // List of reserved keywords that should not be allowed in the username
    private $reserved_words = [
        'admin', 'root', 'superuser', 'administrator', 'system', 
        'login', 'logout', 'user', 'auth', 'usr', 'delete', 'queue', 'cron'
    ];

    public function passes($attribute, $value)
    {
        // Trim leading and trailing spaces
        $value = trim($value);

        // Allow a single space between words
        $value = preg_replace('/\s+/', ' ', $value);

        // Check for URLs (http:// or https://)
        if (preg_match('/https?:\/\/[^\s]+/', $value)) {
            return false;
        }

        // Check for email-like strings (contains @)
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Check for special characters or emojis
        // Allow alphanumeric, underscores, hyphens, and periods
        if (preg_match('/[^a-zA-Z0-9_-\.]/', $value)) {
            return false;
        }

        // Check for reserved keywords (case-insensitive)
        if (in_array(strtolower($value), $this->reserved_words)) {
            return false;
        }

        // Check for SQL injection or script attempts (simple pattern matching)
        if (preg_match('/(\b(select|insert|delete|drop|update|union|--|\/\*|\*\/)\b)/i', $value)) {
            return false;
        }

        // If all checks pass, return true
        return true;
    }

    public function message()
    {
        return 'The string contains invalid characters or patterns, such as spaces, special characters, URLs, or SQL injection attempts.';
    }
}
