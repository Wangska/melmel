<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect(base_url('admin/dashboard.php'));
    }
    redirect('index.php');
}

$error = '';
$redirect_url = 'index.php';
if (!empty($_POST['redirect'])) {
    $redirect_url = $_POST['redirect'];
} elseif (!empty($_GET['redirect'])) {
    $redirect_url = $_GET['redirect'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Check user credentials
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Admins go straight to admin dashboard
            if ($user['role'] === ROLE_ADMIN) {
                redirect(base_url('admin/dashboard.php'));
            }
            redirect($redirect_url);
        } else {
            $error = 'Invalid email or password.';
            $sep = (strpos($redirect_url, '?') !== false) ? '&' : '?';
            redirect($redirect_url . $sep . 'auth_error=' . urlencode($error));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <img src="images/logo.png" alt="HikeBook Cebu Logo">
                <?php echo SITE_NAME; ?>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="hikes.php">Explore</a></li>
                <li><a href="login.php">Log in</a></li>
                <li><a href="register.php">Sign up</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2 style="text-align: center; color: var(--primary); margin-bottom: 2rem;">Login to Your Account</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo sanitize($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-password-wrap">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <button type="button" class="password-toggle-btn" aria-label="Show password" title="Show password">
                            <span class="icon-show"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
                            <span class="icon-hide"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></span>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>

            <p style="text-align: center; margin-top: 1.5rem; color: var(--text-light);">
                Don't have an account? <a href="register.php" style="color: var(--primary); font-weight: 600;">Register here</a>
            </p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>
    <script>
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
    </script>
</body>
</html>
