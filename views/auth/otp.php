<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - OTP VIEW
// Dedicated Split-Screen OTP Verification with step tracker.
// Optimized for 6-digit remote database link dispatch validation.
// ==========================================================================

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../models/OtpModel.php';

$display_otp = OtpModel::DISPLAY_OTP_ON_SCREEN;
$otp_code = isset($_SESSION['otp_code']) ? $_SESSION['otp_code'] : 'XXXXXX';
$display_code = $display_otp ? $otp_code : '******';
$timeLeft = isset($_SESSION['otp_expiry']) ? ($_SESSION['otp_expiry'] - time()) : 120;
if ($timeLeft < 0) $timeLeft = 0;
?>

<div class="auth-page">
    <!-- LEFT HERO: Green-tinted corporate image -->
    <div class="auth-hero">
        <div class="auth-hero-content">
            <img src="IMG/logo_wordmark.png" alt="Melcom" class="auth-hero-logo">
            <h1 class="auth-hero-title">Audit System</h1>
            <p class="auth-hero-subtitle">Stock Audit Setup Engine</p>
        </div>
        <div class="auth-hero-footer">Melcom Group &copy; <?php echo date('Y'); ?></div>
    </div>

    <!-- RIGHT PANEL: OTP Verification -->
    <div class="auth-form-panel">
        <div class="auth-form-container">
            <!-- Top Circle Badge (Green Gradient) -->
            <div class="otp-badge-circle" style="margin: 0 auto 1.25rem auto;">
                <svg class="otp-badge-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>

            <!-- Heading & Muted Subtitle -->
            <h2 class="otp-title-new" style="text-align: center;">Enter verification code</h2>
            <p class="otp-subtitle-new" style="text-align: center; margin-left: auto; margin-right: auto;">Confirm security credentials before audit initialization</p>

            <!-- 6 Digit Input Group -->
            <div class="otp-digit-container">
                <input type="text" class="otp-digit-input" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="0" autocomplete="off" placeholder="•" <?php echo $timeLeft > 0 ? '' : 'disabled'; ?>>
                <input type="text" class="otp-digit-input" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="1" autocomplete="off" placeholder="•" <?php echo $timeLeft > 0 ? '' : 'disabled'; ?>>
                <input type="text" class="otp-digit-input" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="2" autocomplete="off" placeholder="•" <?php echo $timeLeft > 0 ? '' : 'disabled'; ?>>
                <input type="text" class="otp-digit-input" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="3" autocomplete="off" placeholder="•" <?php echo $timeLeft > 0 ? '' : 'disabled'; ?>>
                <input type="text" class="otp-digit-input" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="4" autocomplete="off" placeholder="•" <?php echo $timeLeft > 0 ? '' : 'disabled'; ?>>
                <input type="text" class="otp-digit-input" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="5" autocomplete="off" placeholder="•" <?php echo $timeLeft > 0 ? '' : 'disabled'; ?>>
            </div>

            <!-- Resend Link -->
            <div class="otp-resend-row" style="justify-content: center;">
                <span>Didn't get a code?</span>
                <button type="button" id="btnResendOtp" class="otp-resend-btn" onclick="resendOtpCode()" style="display: <?php echo $timeLeft > 0 ? 'none' : 'inline-block'; ?>;">Click to resend</button>
            </div>

            <!-- Simulated OTP alert popup box (Displays code if screen toggle is enabled) -->
            <div id="simulatedOtpAlert" class="otp-alert <?php echo ($display_otp && $timeLeft > 0) ? '' : 'hidden'; ?>" style="margin-top: 0.5rem; margin-bottom: 0.5rem; width: 100%;">
                <div class="otp-alert-info">
                    <span class="label">Simulated OTP:</span>
                    <span id="otpCodePlaceholder" class="code"><?php echo htmlspecialchars($display_code); ?></span>
                </div>
                <span class="otp-alert-badge">Verification Mode</span>
            </div>

            <!-- OTP countdown timer display -->
            <div id="otpTimerContainer" class="otp-timer-wrapper <?php echo $timeLeft > 0 ? '' : 'hidden'; ?>" style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                <svg style="width:16px;height:16px;color:var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Code expires in: <span id="otpCountdown" style="color: var(--color-danger); min-width: 35px; display: inline-block;">02:00</span></span>
            </div>

            <!-- Divider Line -->
            <hr class="otp-divider">

            <!-- Footer Actions Side-by-Side (AlignUI-styled) -->
            <div class="otp-footer-buttons">
                <button type="button" class="btn-otp-cancel" onclick="cancelOtpFlow()">Cancel</button>
                <button type="button" id="btnConfirmOtp" class="btn-otp-verify" onclick="confirmOtpCode()" disabled>Verify</button>
            </div>
        </div>
    </div>
</div>

<script>
    let secondsLeft = <?php echo $timeLeft; ?>;
    let timerInterval = null;

    /**
     * Helper to read the combined 6-digit code from the separate boxes.
     */
    function getCombinedOtpValue() {
        let code = "";
        document.querySelectorAll(".otp-digit-input").forEach(input => {
            code += input.value.trim();
        });
        return code;
    }

    /**
     * Setup 6-box input listeners for focus movement, numeric filtering, backspacing, and pasting.
     */
    document.addEventListener("DOMContentLoaded", () => {
        const inputs = document.querySelectorAll(".otp-digit-input");
        inputs.forEach((input, idx) => {
            // Typing input
            input.addEventListener("input", (e) => {
                const val = e.target.value;
                e.target.value = val.replace(/[^0-9]/g, ""); // strip non-numeric
                
                if (e.target.value.length === 1) {
                    if (idx < inputs.length - 1) {
                        inputs[idx + 1].focus();
                    }
                }
                toggleConfirmBtnState();
            });

            // Backspace/delete key
            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace") {
                    if (input.value === "") {
                        if (idx > 0) {
                            inputs[idx - 1].focus();
                            inputs[idx - 1].value = "";
                            toggleConfirmBtnState();
                        }
                    } else {
                        input.value = "";
                        toggleConfirmBtnState();
                    }
                    e.preventDefault();
                }
            });

            // Paste 6-digit code
            input.addEventListener("paste", (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData("text").trim();
                if (/^\d{6}$/.test(pasted)) {
                    for (let i = 0; i < 6; i++) {
                        inputs[i].value = pasted[i];
                    }
                    inputs[5].focus();
                    toggleConfirmBtnState();
                }
            });
        });

        // Auto-focus first input on load
        if (inputs.length > 0 && secondsLeft > 0) {
            setTimeout(() => inputs[0].focus(), 150);
        }
    });

    /**
     * Toggle the status of the OTP Submit button depending on length.
     */
    function toggleConfirmBtnState() {
        const val = getCombinedOtpValue();
        const btn = document.getElementById("btnConfirmOtp");
        if (val.length === 6 && secondsLeft > 0) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    }

    /**
     * Runs countdown timer inside the page.
     */
    function startTimer() {
        if (timerInterval) clearInterval(timerInterval);
        const container = document.getElementById("otpTimerContainer");
        const countdown = document.getElementById("otpCountdown");
        
        if (secondsLeft <= 0) {
            handleExpiry();
            return;
        }

        container.classList.remove("hidden");
        updateDisplay();

        timerInterval = setInterval(() => {
            secondsLeft--;
            if (secondsLeft <= 0) {
                clearInterval(timerInterval);
                handleExpiry();
            } else {
                updateDisplay();
            }
        }, 1000);

        function updateDisplay() {
            const mins = Math.floor(secondsLeft / 60);
            const secs = secondsLeft % 60;
            countdown.innerText = `${mins < 10 ? "0" + mins : mins}:${secs < 10 ? "0" + secs : secs}`;
        }
    }

    /**
     * Handles expired code updates immediately on the page.
     */
    function handleExpiry() {
        document.querySelectorAll(".otp-digit-input").forEach(input => {
            input.disabled = true;
            input.value = "";
        });
        
        const btnConfirm = document.getElementById("btnConfirmOtp");
        btnConfirm.disabled = true;
        
        document.getElementById("otpTimerContainer").classList.add("hidden");
        document.getElementById("simulatedOtpAlert").classList.add("hidden");
        document.getElementById("btnResendOtp").style.display = "inline-block";
        
        alert("The OTP code has expired! Please click Resend OTP to request a new code.");
    }

    /**
     * Verifies the 6-digit code using the backend API.
     */
    function confirmOtpCode() {
        const val = getCombinedOtpValue();
        if (val.length !== 6) return;

        const btnConfirm = document.getElementById("btnConfirmOtp");
        btnConfirm.disabled = true;
        btnConfirm.innerText = "Checking...";

        fetch(`index.php?route=otp/verify&code=${encodeURIComponent(val)}`)
        .then(res => res.json())
        .then(data => {
            btnConfirm.innerText = "Verify";
            if (data.status === 'success') {
                if (timerInterval) clearInterval(timerInterval);
                // Security credentials confirmed, redirect silently
                location.href = "index.php?route=dashboard";
            } else {
                alert("Verification Failed: " + data.message);
                document.querySelectorAll(".otp-digit-input").forEach(input => input.value = "");
                document.querySelectorAll(".otp-digit-input")[0].focus();
                toggleConfirmBtnState();
            }
        })
        .catch(err => {
            btnConfirm.disabled = false;
            btnConfirm.innerText = "Verify";
            alert("Connection error: Could not verify OTP code.");
        });
    }

    /**
     * Trigger AJAX dispatch to resend a new OTP.
     */
    function resendOtpCode() {
        document.getElementById("btnResendOtp").style.display = "none";
        
        fetch(`index.php?route=otp/resend`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                secondsLeft = data.expires_in || 120;
                
                const alertEl = document.getElementById("simulatedOtpAlert");
                if (data.display) {
                    document.getElementById("otpCodePlaceholder").innerText = data.code;
                    alertEl.classList.remove("hidden");
                } else {
                    alertEl.classList.add("hidden");
                }

                const inputs = document.querySelectorAll(".otp-digit-input");
                inputs.forEach(input => {
                    input.disabled = false;
                    input.value = "";
                });
                inputs[0].focus();
                
                toggleConfirmBtnState();
                startTimer();
                alert("A new OTP code has been dispatched successfully.");
            } else {
                alert("Resend Failed: " + data.message);
                document.getElementById("btnResendOtp").style.display = "inline-block";
            }
        })
        .catch(err => {
            alert("Connection error: Could not resend OTP.");
            document.getElementById("btnResendOtp").style.display = "inline-block";
        });
    }

    function cancelOtpFlow() {
        if (confirm("Are you sure you want to cancel the validation process and return?")) {
            location.href = "index.php?route=logout";
        }
    }

    // Launch countdown timer on load
    startTimer();
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
