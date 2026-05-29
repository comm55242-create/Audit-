<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - USER MODEL
// Handles authentication validators for phones, emails, and auditor sessions.
// ==========================================================================

class UserModel {
    
    /**
     * Validates phone and email format correctness.
     */
    public static function validateCredentials($phone, $email) {
        $phoneClean = preg_replace('/[^0-9]/', '', $phone);
        if (empty($phoneClean) || strlen($phoneClean) < 7) {
            return [
                'status' => 'error',
                'message' => 'Please enter a valid auditor phone number!'
            ];
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => 'error',
                'message' => 'Please enter a valid corporate auditor email!'
            ];
        }

        return [
            'status' => 'success',
            'phone' => $phoneClean,
            'email' => strtolower(trim($email))
        ];
    }
}
?>
