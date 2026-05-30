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

        $dbStatus = 'unattempted';
        $dbError = '';

        // 2. Insert into remote shop database link
        try {
            $conn = Database::getConnection();
            if ($conn) {
                $sql = "INSERT INTO STK_OTP@DB_LINK_SHOP (PHONE_NO, TOTP, TEMAIL, VC_MACHINE_NAME, VC_MACHINE_IP) 
                        VALUES (:phone, :totp, :email, :machine_name, :machine_ip)";
                $stmt = oci_parse($conn, $sql);
                
                $clean_phone = trim($phone);
                $otp_num = intval($code);
                $clean_email = trim($email);
                $machine_name = 'IT';
                $machine_ip = isset($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '123456';
                
                oci_bind_by_name($stmt, ':phone', $clean_phone);
                oci_bind_by_name($stmt, ':totp', $otp_num);
                oci_bind_by_name($stmt, ':email', $clean_email);
                oci_bind_by_name($stmt, ':machine_name', $machine_name);
                oci_bind_by_name($stmt, ':machine_ip', $machine_ip);
                
                $exec = @oci_execute($stmt);
                if ($exec) {
                    $commit = oci_parse($conn, "COMMIT");
                    oci_execute($commit);
                    oci_free_statement($commit);
                    $dbStatus = 'success';
                } else {
                    $e = oci_error($stmt);
                    $dbStatus = 'failed';
                    $dbError = isset($e['message']) ? $e['message'] : 'Oracle execution failed';
                }
                oci_free_statement($stmt);
            } else {
                $dbStatus = 'failed';
                $dbError = 'Database connection unavailable';
            }
        } catch (Exception $e) {
            $dbStatus = 'failed';
            $dbError = $e->getMessage();
        }

        return [
            'status' => 'success',
            'expires_in' => self::OTP_EXPIRY_SECONDS,
            'mode' => 'real',
            'code' => self::DISPLAY_OTP_ON_SCREEN ? $code : '******',
            'display' => self::DISPLAY_OTP_ON_SCREEN,
            'db_status' => $dbStatus,
            'db_error' => $dbError,
            'message' => $dbStatus === 'success' 
                ? 'OTP generated and sent to phone number and Gmail successfully.' 
                : 'OTP generated in fallback/simulated mode due to database issue.'
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
