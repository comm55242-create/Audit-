<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - LOGIN VIEW
// Dedicated Split-Screen Sign In page with integrated OTP popup modal.
// ==========================================================================

require_once __DIR__ . '/../layouts/header.php';

$login_error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']);
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

    <!-- RIGHT PANEL: Login form -->
    <div class="auth-form-panel">
        <div class="auth-form-container">
            <!-- Melcom Logo -->
            <img src="IMG/logo_wordmark.png" alt="Melcom" class="auth-form-logo">

            <!-- Access Badge -->
            <div class="auth-badge">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Authorized Access Only</span>
            </div>

            <!-- Heading -->
            <h2 class="auth-heading">Please Sign In.</h2>
            <p class="auth-subheading">Identify yourself to access the <strong>Audit Portal</strong></p>

            <!-- Login Form -->
            <form id="loginForm" class="form-group-stack" onsubmit="handleLoginSubmit(event)">
                <!-- Error Message Placeholder -->
                <div id="loginErrorMsg" style="<?php echo !empty($login_error) ? 'display: block;' : 'display: none;'; ?> background-color: #fef2f2; border: 1px solid #fee2e2; color: var(--color-danger); border-radius: 12px; padding: 0.75rem 1rem; font-size: 13px; font-weight: 700; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($login_error); ?>
                </div>

                <div class="form-field">
                    <label class="form-label">Phone Number</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </span>
                        <input type="tel" name="phone" id="loginPhone" class="form-input has-icon" placeholder="+233 24 000 0000" required>
                    </div>
                </div>

                <div class="form-field">
                    <label class="form-label">Email Address</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </span>
                        <input type="email" name="email" id="loginEmail" class="form-input has-icon" placeholder="auditor@melcomdc.com" required>
                    </div>
                </div>

                <button type="submit" id="btnSubmitLogin" class="btn btn-primary" style="width: 100%; padding: 0.85rem 2rem; margin-top: 0.5rem; justify-content: center; position: relative;">
                    <span id="btnSubmitText">Sign In</span>
                    <svg id="btnSubmitIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <p class="auth-footer-text">Melcom Group &copy; <?php echo date('Y'); ?> &middot; Secure Access</p>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- INTEGRATED POPUP MODAL FOR OTP VERIFICATION -->
<!-- ========================================== -->
<div id="otpModalBackdrop" class="otp-modal-backdrop">
    <div class="otp-modal-card">
        <!-- Close Button -->
        <button type="button" class="otp-modal-close" onclick="closeOtpModal()" aria-label="Close OTP Popup">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Step Tracker: Step 1 Done, Step 2 Active -->
        <div class="auth-step-tracker" style="margin-bottom: 1.5rem; padding-bottom: 1rem;">
            <div class="auth-step-dot done">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="auth-step-line done"></div>
            <div class="auth-step-dot current">2</div>
            <div class="auth-step-line pending"></div>
            <div class="auth-step-dot pending">3</div>
        </div>

        <!-- OTP Heading -->
        <h2 class="auth-otp-heading" style="font-size: 1.35rem; margin-bottom: 0.25rem;">OTP Verification</h2>
        <p class="auth-otp-subtitle" style="font-size: 0.8rem; margin-bottom: 1.5rem;">Confirm security credentials before audit initialization</p>

        <!-- OTP Form Area -->
        <div class="auth-otp-form-area">
            <div id="otpInputRow" class="otp-row" style="display: flex; gap: 0.75rem; align-items: center;">
                <div class="otp-input-group" style="display: flex; gap: 0.5rem; flex: 1;">
                    <input type="text" id="otpInput" class="otp-input" placeholder="6-Digit OTP" maxlength="6" oninput="toggleConfirmBtnState()" style="flex: 1; padding: 0.7rem; border-radius: 12px; border: 1px solid var(--color-border); font-size: 15px; font-weight: 800; text-align: center; letter-spacing: 0.15em;">
                    <button type="button" id="btnConfirmOtp" class="btn-confirm-disabled" onclick="confirmOtpCode()" disabled style="flex-shrink: 0; min-width: 90px; padding: 0.7rem 1.25rem; font-weight: 700; border-radius: 12px; font-size: 13px;">
                        Confirm
                    </button>
                </div>
                <button type="button" id="btnResendOtp" class="btn-otp-action" onclick="resendOtpCode()" style="display: none; flex-shrink: 0; min-width: 100px; padding: 0.7rem 1.25rem; font-weight: 700; border-radius: 12px; font-size: 13px;">
                    Resend OTP
                </button>
            </div>

            <!-- Simulated OTP alert popup box (in mock mode) -->
            <div id="simulatedOtpAlert" class="otp-alert hidden" style="margin-top: 1rem; padding: 0.75rem; border-radius: 12px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; display: flex; justify-content: space-between; align-items: center; font-size: 12px; font-weight: 700;">
                <div class="otp-alert-info" style="display: flex; gap: 0.35rem;">
                    <span class="label" style="opacity: 0.85;">Simulated OTP:</span>
                    <span id="otpCodePlaceholder" class="code" style="letter-spacing: 0.05em; color: var(--color-primary); font-weight: 900;">XXXX</span>
                </div>
                <span class="otp-alert-badge" style="background: var(--color-primary); color: #ffffff; font-size: 9px; padding: 2px 6px; border-radius: 6px; text-transform: uppercase;">Mock Mode</span>
            </div>

            <!-- OTP countdown timer display -->
            <div id="otpTimerContainer" class="otp-timer-wrapper" style="margin-top: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 13px; font-weight: 700; color: var(--color-text-muted);">
                <svg style="width:16px;height:16px;color:var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Code expires in: <span id="otpCountdown" style="color: var(--color-danger); min-width: 35px; display: inline-block;">02:00</span></span>
            </div>
        </div>
    </div>
</div>

<script>
    let secondsLeft = 120;
    let timerInterval = null;

    /**
     * Intercepts login credentials and posts them via AJAX to generate the OTP.
     */
    function handleLoginSubmit(e) {
        e.preventDefault();
        
        const phone = document.getElementById("loginPhone").value.trim();
        const email = document.getElementById("loginEmail").value.trim();
        const errorEl = document.getElementById("loginErrorMsg");
        const btnSubmit = document.getElementById("btnSubmitLogin");
        const btnText = document.getElementById("btnSubmitText");
        
        // UI loading state
        errorEl.style.display = "none";
        btnSubmit.disabled = true;
        btnText.innerText = "Processing...";

        // Trigger AJAX Login Submission
        fetch(`index.php?route=login/submit&format=json`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `phone=${encodeURIComponent(phone)}&email=${encodeURIComponent(email)}`
        })
        .then(res => res.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnText.innerText = "Sign In";

            if (data.status === 'success') {
                // Launch dynamic popup modal
                openOtpModal(data);
            } else {
                errorEl.innerText = data.message || "Invalid credentials. Please try again.";
                errorEl.style.display = "block";
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            btnText.innerText = "Sign In";
            errorEl.innerText = "Network connection timeout or server offline. Please verify network access.";
            errorEl.style.display = "block";
        });
    }

    /**
     * Opens the integrated OTP verification modal and initializes timer.
     */
    function openOtpModal(data) {
        const modal = document.getElementById("otpModalBackdrop");
        const input = document.getElementById("otpInput");
        
        // Reset modal fields
        input.value = "";
        input.disabled = false;
        toggleConfirmBtnState();

        // Render mock alerts if applicable
        const alertEl = document.getElementById("simulatedOtpAlert");
        if (data.display) {
            document.getElementById("otpCodePlaceholder").innerText = data.code;
            alertEl.classList.remove("hidden");
            alertEl.style.display = "flex";
        } else {
            alertEl.classList.add("hidden");
            alertEl.style.display = "none";
        }

        // Initialize countdown seconds
        secondsLeft = data.expires_in || 120;
        
        // Show the Modal Backdrop
        modal.classList.add("open");
        
        // Start live OTP countdown
        startTimer();
        
        // Auto-focus input
        setTimeout(() => input.focus(), 150);
    }

    /**
     * Closes the OTP popup modal safely.
     */
    function closeOtpModal() {
        const modal = document.getElementById("otpModalBackdrop");
        modal.classList.remove("open");
        
        if (timerInterval) clearInterval(timerInterval);
        secondsLeft = 0;
    }

    /**
     * Toggle the status of the OTP Submit button depending on length.
     */
    function toggleConfirmBtnState() {
        const val = document.getElementById("otpInput").value.trim();
        const btn = document.getElementById("btnConfirmOtp");
        if (val.length === 6 && secondsLeft > 0) {
            btn.disabled = false;
            btn.className = "btn btn-primary";
        } else {
            btn.disabled = true;
            btn.className = "btn-confirm-disabled";
        }
    }

    /**
     * Runs countdown timer inside the modal.
     */
    function startTimer() {
        if (timerInterval) clearInterval(timerInterval);
        const container = document.getElementById("otpTimerContainer");
        const countdown = document.getElementById("otpCountdown");
        
        if (secondsLeft <= 0) {
            handleExpiry();
            return;
        }

        container.style.display = "flex";
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
     * Handles expired code updates immediately inside the popup.
     */
    function handleExpiry() {
        document.getElementById("otpInput").disabled = true;
        document.getElementById("otpInput").value = "";
        
        const btnConfirm = document.getElementById("btnConfirmOtp");
        btnConfirm.disabled = true;
        btnConfirm.className = "btn-confirm-disabled";
        
        document.getElementById("otpTimerContainer").style.display = "none";
        document.getElementById("simulatedOtpAlert").style.display = "none";
        document.getElementById("btnResendOtp").style.display = "block";
        
        alert("The OTP code has expired! Please click Resend OTP to request a new code.");
    }

    /**
     * Verifies the 4-digit code using the backend API.
     */
    function confirmOtpCode() {
        const val = document.getElementById("otpInput").value.trim();
        if (val.length !== 6) return;

        const btnConfirm = document.getElementById("btnConfirmOtp");
        btnConfirm.disabled = true;
        btnConfirm.innerText = "Checking...";

        fetch(`index.php?route=otp/verify&code=${encodeURIComponent(val)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            btnConfirm.innerText = "Confirm";
            if (data.status === 'success') {
                if (timerInterval) clearInterval(timerInterval);
                alert("Security Credentials Confirmed!");
                location.href = "index.php?route=dashboard";
            } else {
                alert("Verification Failed: " + data.message);
                document.getElementById("otpInput").value = "";
                toggleConfirmBtnState();
            }
        })
        .catch(err => {
            btnConfirm.disabled = false;
            btnConfirm.innerText = "Confirm";
            alert("Connection error: Could not verify OTP code.");
        });
    }

    /**
     * Trigger AJAX dispatch to resend a new OTP.
     */
    function resendOtpCode() {
        document.getElementById("btnResendOtp").style.display = "none";
        
        fetch(`index.php?route=otp/resend`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                secondsLeft = data.expires_in || 120;
                
                const alertEl = document.getElementById("simulatedOtpAlert");
                if (data.display) {
                    document.getElementById("otpCodePlaceholder").innerText = data.code;
                    alertEl.style.display = "flex";
                } else {
                    alertEl.style.display = "none";
                }

                const input = document.getElementById("otpInput");
                input.disabled = false;
                input.value = "";
                input.focus();
                
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
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
