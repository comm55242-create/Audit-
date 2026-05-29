<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - OTP VIEW
// Dedicated Split-Screen OTP Verification with step tracker.
// ==========================================================================

require_once __DIR__ . '/../layouts/header.php';

$otp_code = isset($_SESSION['otp_code']) ? $_SESSION['otp_code'] : 'XXXX';
$otp_mode = isset($_SESSION['otp_mode']) ? $_SESSION['otp_mode'] : 'mock';
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
            <!-- Step Tracker: Step 1 Done, Step 2 Active -->
            <div class="auth-step-tracker">
                <div class="auth-step-dot done">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="auth-step-line done"></div>
                <div class="auth-step-dot current">2</div>
                <div class="auth-step-line pending"></div>
                <div class="auth-step-dot pending">3</div>
            </div>

            <!-- OTP Heading -->
            <h2 class="auth-otp-heading">OTP Verification</h2>
            <p class="auth-otp-subtitle">Confirm security credentials before audit initialization</p>

            <!-- OTP Form Area -->
            <div class="auth-otp-form-area">
                <div id="otpInputRow" class="otp-row" style="display: flex; gap: 0.75rem; align-items: center;">
                    <div class="otp-input-group" style="display: flex; gap: 0.5rem; flex: 1;">
                        <input type="text" id="otpInput" class="otp-input" placeholder="OTP" maxlength="4" oninput="toggleConfirmBtnState()" <?php echo $timeLeft > 0 ? '' : 'disabled'; ?> style="flex: 1;">
                        <button type="button" id="btnConfirmOtp" class="btn-confirm-disabled" onclick="confirmOtpCode()" disabled style="flex-shrink: 0; min-width: 100px;">
                            Confirm
                        </button>
                    </div>
                    <button type="button" id="btnResendOtp" class="btn-otp-action" onclick="resendOtpCode()" style="display: <?php echo $timeLeft > 0 ? 'none' : 'block'; ?>; flex-shrink: 0; min-width: 110px;">
                        Resend OTP
                    </button>
                </div>

                <!-- Simulated OTP alert popup box -->
                <div id="simulatedOtpAlert" class="otp-alert <?php echo ($otp_mode === 'mock' && $timeLeft > 0) ? '' : 'hidden'; ?>" style="margin-top: 1rem;">
                    <div class="otp-alert-info">
                        <span class="label">Simulated OTP:</span>
                        <span id="otpCodePlaceholder" class="code"><?php echo htmlspecialchars($otp_code); ?></span>
                    </div>
                    <span class="otp-alert-badge">Mock Mode</span>
                </div>

                <!-- OTP countdown timer display -->
                <div id="otpTimerContainer" class="otp-timer-wrapper <?php echo $timeLeft > 0 ? '' : 'hidden'; ?>" style="margin-top: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 13px; font-weight: 700; color: var(--color-text-muted);">
                    <svg style="width:16px;height:16px;color:var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Code expires in: <span id="otpCountdown" style="color: var(--color-danger); min-width: 35px; display: inline-block;">02:00</span></span>
                </div>
            </div>

            <!-- Cancel/Back link -->
            <a class="auth-cancel-link" onclick="cancelOtpFlow()">Cancel and return to Sign In</a>
        </div>
    </div>
</div>

<script>
    let secondsLeft = <?php echo $timeLeft; ?>;
    let timerInterval = null;

    function toggleConfirmBtnState() {
        const val = document.getElementById("otpInput").value.trim();
        const btn = document.getElementById("btnConfirmOtp");
        if (val.length === 4 && secondsLeft > 0) {
            btn.disabled = false;
            btn.className = "btn btn-primary";
        } else {
            btn.disabled = true;
            btn.className = "btn-confirm-disabled";
        }
    }

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

    function handleExpiry() {
        document.getElementById("otpInput").disabled = true;
        document.getElementById("otpInput").value = "";
        
        const btnConfirm = document.getElementById("btnConfirmOtp");
        btnConfirm.disabled = true;
        btnConfirm.className = "btn-confirm-disabled";
        
        document.getElementById("otpTimerContainer").classList.add("hidden");
        document.getElementById("simulatedOtpAlert").classList.add("hidden");
        document.getElementById("btnResendOtp").style.display = "block";
        
        alert("The OTP has expired! Please click Resend OTP to request a new code.");
    }

    function confirmOtpCode() {
        const val = document.getElementById("otpInput").value.trim();
        if (val.length !== 4) return;

        fetch(`index.php?route=otp/verify&code=${encodeURIComponent(val)}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (timerInterval) clearInterval(timerInterval);
                alert("OTP Confirmed successfully!");
                location.href = "index.php?route=dashboard";
            } else {
                alert("Verification Failed: " + data.message);
                document.getElementById("otpInput").value = "";
                toggleConfirmBtnState();
            }
        })
        .catch(err => {
            alert("Connection error: Could not verify OTP.");
        });
    }

    function resendOtpCode() {
        document.getElementById("btnResendOtp").style.display = "none";
        
        fetch(`index.php?route=otp/resend`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                secondsLeft = data.expires_in || 120;
                
                const alertEl = document.getElementById("simulatedOtpAlert");
                if (data.mode === 'mock') {
                    document.getElementById("otpCodePlaceholder").innerText = data.code;
                    alertEl.classList.remove("hidden");
                } else {
                    alertEl.classList.add("hidden");
                }

                document.getElementById("otpInput").disabled = false;
                document.getElementById("otpInput").value = "";
                document.getElementById("otpInput").focus();
                
                toggleConfirmBtnState();
                startTimer();
                alert("A new OTP code has been dispatched successfully.");
            } else {
                alert("Resend Failed: " + data.message);
                document.getElementById("btnResendOtp").style.display = "block";
            }
        })
        .catch(err => {
            alert("Connection error: Could not resend OTP.");
            document.getElementById("btnResendOtp").style.display = "block";
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
