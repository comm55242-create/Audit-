<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - OTP MODEL
// Decouples all ERP URLs, mock timers, and handles generate/confirm operations.
// Integrates with remote shop database STK_OTP@DB_LINK_SHOP.
// ==========================================================================

class OtpModel {
    const OTP_EXPIRY_SECONDS = 120;

    // Toggle whether to display the OTP on screen (set to true for manual testing/simulated view, false to hide it)
    const DISPLAY_OTP_ON_SCREEN = true; 

    /**
     * Generates a 6-digit code and dispatches it via remote STK_OTP@DB_LINK_SHOP insertion.
     */
    public static function generateOtp($phone, $email) {
        require_once __DIR__ . '/Database.php';

        // 1. Generate a six-digit OTP code
        $code = (string)rand(100000, 999999);
        $_SESSION['otp_code'] = $code;
        $_SESSION['otp_expiry'] = time() + self::OTP_EXPIRY_SECONDS;
        $_SESSION['otp_mode'] = 'real';

        // 2. Insert into remote shop database link
        $conn = Database::getConnection();
        if (!$conn) {
            throw new Exception("Database connection unavailable for OTP dispatch.");
        }
        
        $sql = "INSERT INTO STK_OTP@DB_LINK_SHOP (PHONE_NO, TOTP, TEMAIL, VC_MACHINE_NAME, VC_MACHINE_IP) 
                VALUES (:phone, :totp, :email, :machine_name, :machine_ip)";
        $stmt = oci_parse($conn, $sql);
        if (!$stmt) {
            $e = oci_error($conn);
            throw new Exception("Oracle SQL parsing failed for OTP: " . $e['message']);
        }
        
        $clean_phone = trim($phone);
        $otp_num = intval($code);
        $clean_email = trim($email);

        // 1. Resolve client laptop IP address robustly
        $ip = '127.0.0.1';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $machine_ip = substr(trim($ip), 0, 30);

        // 2. Resolve client laptop host name robustly via reverse DNS lookup
        $laptop_name = 'IT';
        if ($ip !== '127.0.0.1' && $ip !== '::1') {
            $resolved = @gethostbyaddr($ip);
            if ($resolved && $resolved !== $ip) {
                $parts = explode('.', $resolved);
                $laptop_name = $parts[0];
            }
        } else {
            $laptop_name = @gethostname(); // Local server machine name fallback
        }
        if (empty($laptop_name)) {
            $laptop_name = 'IT';
        }
        $machine_name = substr(strtoupper(trim($laptop_name)), 0, 30);
        
        oci_bind_by_name($stmt, ':phone', $clean_phone);
        oci_bind_by_name($stmt, ':totp', $otp_num);
        oci_bind_by_name($stmt, ':email', $clean_email);
        oci_bind_by_name($stmt, ':machine_name', $machine_name);
        oci_bind_by_name($stmt, ':machine_ip', $machine_ip);
        
        $exec = @oci_execute($stmt);
        if (!$exec) {
            $e = oci_error($stmt);
            oci_free_statement($stmt);
            throw new Exception("Oracle STK_OTP dispatch failed: " . (isset($e['message']) ? $e['message'] : 'Oracle execution failed'));
        }
        
        $commit = oci_parse($conn, "COMMIT");
        oci_execute($commit);
        oci_free_statement($commit);
        oci_free_statement($stmt);

        return [
            'status' => 'success',
            'expires_in' => self::OTP_EXPIRY_SECONDS,
            'mode' => 'real',
            'code' => self::DISPLAY_OTP_ON_SCREEN ? $code : '******',
            'display' => self::DISPLAY_OTP_ON_SCREEN,
            'db_status' => 'success',
            'db_error' => '',
            'message' => 'OTP generated and sent to phone number and Gmail successfully.'
        ];
    }

    /**
     * Validates the 6-digit user-entered code against the active session.
     */
    public static function confirmOtp($code) {
        $storedOtp = isset($_SESSION['otp_code']) ? $_SESSION['otp_code'] : '';
        $expiryTime = isset($_SESSION['otp_expiry']) ? $_SESSION['otp_expiry'] : 0;

        if (empty($storedOtp) || time() > $expiryTime) {
            return [
                'status' => 'error',
                'message' => 'OTP has expired! Please request a new code.'
            ];
        }

        if ($code === $storedOtp) {
            unset($_SESSION['otp_code']);
            unset($_SESSION['otp_expiry']);
            return [
                'status' => 'success',
                'message' => 'OTP verified successfully!'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Invalid OTP code! Please try again.'
            ];
        }
    }
}
?>
