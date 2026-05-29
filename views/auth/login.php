<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - LOGIN VIEW
// Dedicated Split-Screen Sign In page with green-tinted hero image.
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
            <form method="POST" action="index.php?route=login/submit" class="form-group-stack">
                <?php if (!empty($login_error)): ?>
                    <div style="background-color: #fef2f2; border: 1px solid #fee2e2; color: var(--color-danger); border-radius: 12px; padding: 0.75rem 1rem; font-size: 13px; font-weight: 700;">
                        <?php echo htmlspecialchars($login_error); ?>
                    </div>
                <?php endif; ?>

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

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem 2rem; margin-top: 0.5rem; justify-content: center;">
                    <span>Sign In</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            <p class="auth-footer-text">Melcom Group &copy; <?php echo date('Y'); ?> &middot; Secure Access</p>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
