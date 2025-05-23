<?php

namespace Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class CleanName implements Rule
{
    // List of reserved keywords that should not be allowed in the username
    private $reserved_words = [
        'admin', 'root', 'superuser', 'login', 'logout', 'auth', 'usr', 'cron'
    ];

    public function passes($attribute, $value)
    {
        // Check for email-like strings (contains @)
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Reject if string contains anything except letters, marks, spaces, numbers, commas, periods, apostrophes, or hyphens
        // if (preg_match('/[^\p{L}\p{M}\p{Zs}0-9,\.\'\-]/u', $value)) {
        //     return false;
        // }

        // Check for reserved keywords (case-insensitive)
        if (in_array(strtolower($value), $this->reserved_words)) {
            return false;
        }

        // Check for SQL injection or script attempts (simple pattern matching)
        if (preg_match('/(\b(select|insert|delete|drop|update|union|--|\/\*|\*\/)\b)/i', $value)) {
            return false;
        }
        return true;
    }

    public function message()
    {
        return 'The string contains invalid characters or patterns, such as spaces, special characters, URLs, or SQL injection attempts.';
    }
}
