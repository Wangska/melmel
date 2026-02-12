<?php
if (!defined('SITE_NAME')) {
    require_once dirname(__DIR__) . '/config.php';
}
$auth_login_url = 'login.php';
$auth_register_url = 'register.php';
?>
<!-- Auth Modals -->
<div id="auth-modal-overlay" class="auth-modal-overlay" aria-hidden="true">
    <div class="auth-modal" role="dialog" aria-labelledby="auth-modal-title" aria-modal="true">
        <button type="button" class="auth-modal-close" aria-label="Close">&times;</button>
        <div id="auth-modal-login" class="auth-modal-panel">
            <h2 id="auth-modal-title" class="auth-modal-title">Login</h2>
            <?php if (!empty($_GET['auth_error'])): ?>
                <div class="alert alert-error"><?php echo sanitize($_GET['auth_error']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['registered'])): ?>
                <div class="alert alert-success">Account created. Please log in.</div>
            <?php endif; ?>
            <form method="POST" action="<?php echo htmlspecialchars($auth_login_url); ?>" class="auth-form">
                <input type="hidden" name="redirect" id="auth-redirect-input" value="">
                <div class="form-group">
                    <label for="modal-email">Email</label>
                    <input type="email" id="modal-email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modal-password">Password</label>
                    <div class="input-password-wrap">
                        <input type="password" id="modal-password" name="password" class="form-control" required>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" title="Show password">
                            <span class="icon-show"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
                            <span class="icon-hide"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></span>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
                <p class="auth-modal-switch">Don't have an account? <a href="#" data-show="register">Register</a></p>
            </form>
        </div>
        <div id="auth-modal-register" class="auth-modal-panel" style="display: none;">
            <h2 class="auth-modal-title">Register</h2>
            <form method="POST" action="<?php echo htmlspecialchars($auth_register_url); ?>" class="auth-form">
                <div class="form-group">
                    <label for="modal-username">Username</label>
                    <input type="text" id="modal-username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modal-reg-email">Email</label>
                    <input type="email" id="modal-reg-email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modal-reg-password">Password</label>
                    <div class="input-password-wrap">
                        <input type="password" id="modal-reg-password" name="password" class="form-control" required minlength="6">
                        <button type="button" class="password-toggle-btn" aria-label="Show password" title="Show password">
                            <span class="icon-show"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
                            <span class="icon-hide"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></span>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="modal-confirm-password">Confirm Password</label>
                    <div class="input-password-wrap">
                        <input type="password" id="modal-confirm-password" name="confirm_password" class="form-control" required>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" title="Show password">
                            <span class="icon-show"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
                            <span class="icon-hide"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></span>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
                <p class="auth-modal-switch">Already have an account? <a href="#" data-show="login">Login</a></p>
            </form>
        </div>
    </div>
</div>
<script>
(function() {
    var overlay = document.getElementById('auth-modal-overlay');
    if (!overlay) return;
    var loginPanel = document.getElementById('auth-modal-login');
    var registerPanel = document.getElementById('auth-modal-register');
    var redirectInput = document.getElementById('auth-redirect-input');

    function openModal(panel) {
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        if (redirectInput) redirectInput.value = window.location.href;
        if (panel === 'register') {
            loginPanel.style.display = 'none';
            registerPanel.style.display = 'block';
        } else {
            loginPanel.style.display = 'block';
            registerPanel.style.display = 'none';
        }
    }
    function closeModal() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
    }

    document.querySelectorAll('.auth-modal-trigger').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            openModal(el.getAttribute('data-modal') || 'login');
        });
    });
    overlay.querySelector('.auth-modal-close').addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) closeModal();
    });
    overlay.querySelectorAll('[data-show]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            openModal(el.getAttribute('data-show'));
        });
    });
    if (window.location.search.indexOf('registered=1') !== -1 || window.location.search.indexOf('show=login') !== -1 || window.location.search.indexOf('auth_error') !== -1) {
        openModal('login');
    }
    if (window.location.search.indexOf('show=register') !== -1) {
        openModal('register');
    }
    // Password visibility toggle (eye icon)
    document.querySelectorAll('.password-toggle-btn').forEach(function(btn) {
        var wrap = btn.closest('.input-password-wrap');
        var input = wrap ? wrap.querySelector('input') : null;
        if (!input) return;
        btn.addEventListener('click', function() {
            var isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            btn.classList.toggle('showing', isPass);
            btn.setAttribute('aria-label', isPass ? 'Hide password' : 'Show password');
            btn.setAttribute('title', isPass ? 'Hide password' : 'Show password');
        });
    });
})();
</script>
