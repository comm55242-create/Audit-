<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - AUTH CONTROLLER
// Handles multi-step secure auditor login, OTP validation requests, and sessions.
// ==========================================================================

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/OtpModel.php';

class AuthController {
    
    /**
     * Renders the premium login page view.
     */
    public function showLogin() {
        if (isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated'] === true) {
            header("Location: index.php?route=dashboard");
            exit;
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Processes form POST credentials, triggers OTP dispatch, and routes users to OTP entry.
     */
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=login");
            exit;
        }

        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';

        $validation = UserModel::validateCredentials($phone, $email);
        if ($validation['status'] === 'error') {
            $_SESSION['login_error'] = $validation['message'];
            header("Location: index.php?route=login");
            exit;
        }

        // Store temp details in session state
        $_SESSION['temp_phone'] = $validation['phone'];
        $_SESSION['temp_email'] = $validation['email'];

        // Dispatch OTP token
        $otpRes = OtpModel::generateOtp($validation['phone'], $validation['email']);
        if ($otpRes['status'] === 'success') {
            $_SESSION['otp_pending'] = true;
            header("Location: index.php?route=otp");
            exit;
        } else {
            $_SESSION['login_error'] = $otpRes['message'];
            header("Location: index.php?route=login");
            exit;
        }
    }

    /**
     * Renders the OTP verification view card.
     */
    public function showOtp() {
        if (!isset($_SESSION['otp_pending']) || $_SESSION['otp_pending'] !== true) {
            header("Location: index.php?route=login");
            exit;
        }
        require_once __DIR__ . '/../views/auth/otp.php';
    }

    /**
     * Handles AJAX verification of entered 4-digit codes.
     */
    public function handleVerify() {
        header('Content-Type: application/json; charset=utf-8');
        
        $code = isset($_GET['code']) ? trim($_GET['code']) : '';
        if (empty($code)) {
            echo json_encode(['status' => 'error', 'message' => 'OTP code is required!']);
            exit;
        }

        $res = OtpModel::confirmOtp($code);
        if ($res['status'] === 'success') {
            $_SESSION['user_authenticated'] = true;
            $_SESSION['logged_in_phone'] = $_SESSION['temp_phone'];
            $_SESSION['logged_in_email'] = $_SESSION['temp_email'];
            
            unset($_SESSION['temp_phone']);
            unset($_SESSION['temp_email']);
            unset($_SESSION['otp_pending']);

            echo json_encode(['status' => 'success', 'message' => 'OTP authenticated successfully!']);
            exit;
        } else {
            echo json_encode($res);
            exit;
        }
    }

    /**
     * Handles AJAX triggers to resend a new OTP code to the current auditor info.
     */
    public function handleResend() {
        header('Content-Type: application/json; charset=utf-8');
        
        $phone = isset($_SESSION['temp_phone']) ? $_SESSION['temp_phone'] : '';
        $email = isset($_SESSION['temp_email']) ? $_SESSION['temp_email'] : '';

        if (empty($phone) || empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Auditor details expired. Please log in again.']);
            exit;
        }

        $otpRes = OtpModel::generateOtp($phone, $email);
        echo json_encode($otpRes);
        exit;
    }

    /**
     * Handles logouts by resetting session values.
     */
    public function handleLogout() {
        session_unset();
        session_destroy();
        header("Location: index.php?route=login");
        exit;
    }
}
?>
