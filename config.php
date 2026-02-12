<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'hiking_app');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration (set SITE_URL to your app URL, e.g. http://localhost/melmel)
define('SITE_NAME', 'HikeBook Cebu');
define('SITE_URL', 'http://localhost/melmel');

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// RBAC: Role-Based Access Control
define('ROLE_USER', 'user');
define('ROLE_ADMIN', 'admin');

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === ROLE_ADMIN;
}

/** Check if current user has a specific role */
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/** Require admin; redirect to home if not admin (works from any subfolder) */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit();
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/** Full URL for a path (use for Admin link so it works from any page) */
function base_url($path = '') {
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}
?>
