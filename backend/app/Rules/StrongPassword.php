<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a string.');

            return;
        }

        // Mínimo de 8 caracteres
        if (strlen($value) < 8) {
            $fail('The :attribute must be at least 8 characters.');

            return;
        }

        // Pelo menos uma letra maiúscula
        if (! preg_match('/[A-Z]/', $value)) {
            $fail('The :attribute must contain at least one uppercase letter.');

            return;
        }

        // Pelo menos uma letra minúscula
        if (! preg_match('/[a-z]/', $value)) {
            $fail('The :attribute must contain at least one lowercase letter.');

            return;
        }

        // Pelo menos um número
        if (! preg_match('/[0-9]/', $value)) {
            $fail('The :attribute must contain at least one number.');

            return;
        }

        // Pelo menos um caractere especial
        if (! preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
            $fail('The :attribute must contain at least one special character.');

            return;
        }
    }
}
