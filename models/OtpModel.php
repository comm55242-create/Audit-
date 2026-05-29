<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - OTP MODEL
// Decouples all ERP URLs, mock timers, and handles generate/confirm operations.
// ==========================================================================

class OtpModel {
    const ERP_OTP_URL = 'http://mock-erp.melcomgroup.com/api/v1/send-otp';
    const ERP_CONFIRM_URL = 'http://mock-erp.melcomgroup.com/api/v1/verify-otp';
    const OTP_EXPIRY_SECONDS = 120;

    /**
     * Detects if the ERP endpoint is configured for real production use.
     */
    public static function isRealErpActive() {
        $url = trim(self::ERP_OTP_URL);
        if (empty($url)) return false;
        if (filter_var($url, FILTER_VALIDATE_URL) === false) return false;
        if (stripos($url, 'mock') !== false || stripos($url, 'melcom-erp.local') !== false) {
            return false;
        }
        return true;
    }

    /**
     * Generates a 4-digit code and dispatches it via ERP API or local fallback mock.
     */
    public static function generateOtp($phone, $email) {
        $isReal = self::isRealErpActive();
        if ($isReal) {
            $postData = json_encode([
                'phone' => $phone,
                'email' => $email,
                'expires_in' => self::OTP_EXPIRY_SECONDS
            ]);

            $ch = @curl_init(self::ERP_OTP_URL);
            if ($ch) {
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode >= 200 && $httpCode < 300) {
                    $resData = json_decode($response, true);
                    $_SESSION['erp_otp_session'] = isset($resData['otp_session']) ? $resData['otp_session'] : 'active';
                    $_SESSION['otp_mode'] = 'real';
                    $_SESSION['otp_expiry'] = time() + self::OTP_EXPIRY_SECONDS;

                    return [
                        'status' => 'success',
                        'expires_in' => self::OTP_EXPIRY_SECONDS,
                        'mode' => 'real',
                        'message' => 'OTP has been dispatched via Melcom ERP API.'
                    ];
                } else {
                    return self::triggerSimulatedOtp($phone, $email, 'ERP API Error code ' . $httpCode);
                }
            } else {
                return self::triggerSimulatedOtp($phone, $email, 'cURL resource unavailable');
            }
        } else {
            return self::triggerSimulatedOtp($phone, $email);
        }
    }

    /**
     * Creates local simulated OTP code and returns it.
     */
    private static function triggerSimulatedOtp($phone, $email, $fallbackReason = '') {
        $code = (string)rand(1000, 9000);
        $_SESSION['otp_code'] = $code;
        $_SESSION['otp_expiry'] = time() + self::OTP_EXPIRY_SECONDS;
        $_SESSION['otp_mode'] = 'mock';

        $response = [
            'status' => 'success',
            'expires_in' => self::OTP_EXPIRY_SECONDS,
            'mode' => 'mock',
            'code' => $code,
            'message' => 'Simulated OTP generated successfully.'
        ];

        if (!empty($fallbackReason)) {
            $response['fallback_warning'] = $fallbackReason;
        }

        return $response;
    }

    /**
     * Validates the 4-digit user-entered code against the active session.
     */
    public static function confirmOtp($code) {
        $mode = isset($_SESSION['otp_mode']) ? $_SESSION['otp_mode'] : 'mock';

        if ($mode === 'real' && self::isRealErpActive()) {
            $postData = json_encode([
                'code' => $code,
                'otp_session' => isset($_SESSION['erp_otp_session']) ? $_SESSION['erp_otp_session'] : ''
            ]);

            $ch = @curl_init(self::ERP_CONFIRM_URL);
            if ($ch) {
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $resData = json_decode($response, true);
                $isVerified = ($httpCode >= 200 && $httpCode < 300 && isset($resData['verified']) && $resData['verified'] == true);

                if ($isVerified) {
                    return [
                        'status' => 'success',
                        'message' => 'OTP verified successfully via ERP!'
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Invalid or expired OTP code!'
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Verification service temporarily unavailable.'
                ];
            }
        } else {
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
                    'message' => 'Simulated OTP verified successfully!'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Invalid OTP code! Please try again.'
                ];
            }
        }
    }
}
?>
