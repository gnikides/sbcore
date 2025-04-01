<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class CleanString implements Rule
{
    // List of reserved keywords that should not be allowed in the username
    private $reservedKeywords = ['admin', 'root', 'superuser'];

    public function passes($attribute, $value)
    {
        // Remove leading, trailing, and internal spaces
        $value = preg_replace('/\s+/', '', $value); // Removes all spaces

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
        if (in_array(strtolower($value), $this->reservedKeywords)) {
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
