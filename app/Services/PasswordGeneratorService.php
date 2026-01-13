<?php

namespace App\Services;

class PasswordGeneratorService
{
    private const UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
    private const DIGITS = '0123456789';
    private const SPECIAL_CHARS = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    private const PASSWORD_LENGTH = 12;

    /**
     * Generate a secure random password
     * Rules:
     * ✓ At least 8 characters (generates 12)
     * ✓ Uppercase
     * ✓ Lowercase
     * ✓ Number
     * ✓ Special character
     */
    public function generateSecurePassword(): string
    {
        $password = [];

        // Ensure at least one character from each group
        $password[] = $this->randomChar(self::UPPERCASE);
        $password[] = $this->randomChar(self::LOWERCASE);
        $password[] = $this->randomChar(self::DIGITS);
        $password[] = $this->randomChar(self::SPECIAL_CHARS);

        $allChars = self::UPPERCASE . self::LOWERCASE . self::DIGITS . self::SPECIAL_CHARS;

        while (count($password) < self::PASSWORD_LENGTH) {
            $password[] = $this->randomChar($allChars);
        }

        // Shuffle to avoid predictable order
        shuffle($password);

        return implode('', $password);
    }

    private function randomChar(string $chars): string
    {
        return $chars[random_int(0, strlen($chars) - 1)];
    }

    /**
     * Optional: validate password strength
     */
    public function isValidPassword(string $password): bool
    {
        if (strlen($password) < 8) {
            return false;
        }

        return preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/\d/', $password)
            && preg_match('/[!@#$%^&*()_\+\-\=\[\]{}|;:,.<>?]/', $password);
    }
}
