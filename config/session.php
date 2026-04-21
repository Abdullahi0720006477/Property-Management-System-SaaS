<?php
/**
 * Session Configuration and Auth Helpers
 */

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', 1800);
    session_start();
}

// Session timeout (30 minutes) - skip on auth pages
$currentPage = $_GET['page'] ?? 'dashboard';
if ($currentPage !== 'auth') {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
        // Session expired
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        session_start();
        setFlashMessage('warning', 'Your session has expired. Please log in again.');
        if (!headers_sent()) {
            header('Location: ?page=auth&action=login');
            exit;
        }
    }
}
// Update last activity time
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}

// Remember Me: auto-login from cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $tokenHash = hash('sha256', $token);

    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT rt.user_id, u.* FROM remember_tokens rt JOIN users u ON rt.user_id = u.id WHERE rt.token_hash = ? AND rt.expires_at > NOW() AND u.is_active = 1");
    $stmt->execute([$tokenHash]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Auto-login
        session_regenerate_id(true);
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['user_role'] = $result['role'];
        $_SESSION['user_name'] = $result['full_name'];
        $_SESSION['user_email'] = $result['email'];
        $_SESSION['user_avatar'] = $result['avatar'];
        $_SESSION['last_activity'] = time();
    } else {
        // Invalid token - clear cookie
        setcookie('remember_me', '', time() - 3600, '/', '', false, true);
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged-in user ID
 */
function currentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user's role
 */
function currentUserRole(): ?string
{
    return $_SESSION['user_role'] ?? null;
}

/**
 * Get current user's name
 */
function currentUserName(): ?string
{
    return $_SESSION['user_name'] ?? null;
}

/**
 * Check if current user has a specific role
 */
function hasRole(string $role): bool
{
    return currentUserRole() === $role;
}

/**
 * Check if current user is admin
 */
function isAdmin(): bool
{
    return hasRole('company_admin');
}

/**
 * Check if current user is manager
 */
function isManager(): bool
{
    return hasRole('manager');
}

/**
 * Check if current user is tenant
 */
function isTenant(): bool
{
    return hasRole('tenant');
}

/**
 * Require authentication - redirect to login if not logged in
 */
function requireAuth(): void
{
    if (!isLoggedIn()) {
        setFlashMessage('error', 'Please log in to access this page.');
        redirect('?page=auth&action=login');
    }
}

/**
 * Require a specific role
 */
function requireRole(string ...$roles): void
{
    requireAuth();
    if (!in_array(currentUserRole(), $roles)) {
        setFlashMessage('error', 'You do not have permission to access this page.');
        redirect('?page=dashboard');
    }
}

/**
 * Generate CSRF token
 */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Get CSRF token hidden input field
 */
function csrfField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validate CSRF token
 */
function validateCsrfToken(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set flash message
 */
function setFlashMessage(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Get and clear flash messages
 */
function getFlashMessages(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/**
 * Redirect to a URL
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}
