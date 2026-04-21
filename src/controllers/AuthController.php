<?php
/**
 * Authentication Controller
 * BizConnect SaaS - handles login, company registration, logout, password reset
 */
require_once __DIR__ . '/../models/User.php';
require_once SRC_PATH . '/models/ActivityLog.php';

$userModel = new User();
$db = Database::getInstance();

switch ($action) {
    case 'login':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid request. Please try again.');
                redirect('?page=auth&action=login');
            }

            $email = postData('email');
            $password = postData('password');

            $validator = new Validator($_POST);
            $validator->required('email')->email('email')->required('password');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=auth&action=login');
            }

            $user = $userModel->findByEmail($email);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                setFlashMessage('error', 'Invalid email or password.');
                redirect('?page=auth&action=login');
            }

            if (!$user['is_active']) {
                setFlashMessage('error', 'Your account has been deactivated. Contact admin.');
                redirect('?page=auth&action=login');
            }

            // Look up user's company
            $stmtCompany = $db->prepare("SELECT c.* FROM companies c WHERE c.id = (SELECT company_id FROM users WHERE id = ?)");
            $stmtCompany->execute([$user['id']]);
            $company = $stmtCompany->fetch(PDO::FETCH_ASSOC);

            // Set session
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_avatar'] = $user['avatar'];

            // Set company session vars
            if ($company) {
                $_SESSION['company_id'] = $company['id'];
                $_SESSION['company_name'] = $company['name'];
                $_SESSION['company_plan'] = $company['subscription_plan'];

                // Check if company subscription is active
                if (!empty($company['subscription_end']) && strtotime($company['subscription_end']) < time()) {
                    setFlashMessage('warning', 'Your company subscription has expired. Please renew to continue using all features.');
                }
            }

            // Remember Me
            if (!empty($_POST['remember_me'])) {
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);

                // Clean up old tokens for this user
                $stmt = $db->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
                $stmt->execute([$user['id']]);

                // Store new token
                $stmt = $db->prepare("INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))");
                $stmt->execute([$user['id'], $tokenHash]);

                // Set cookie (30 days)
                setcookie('remember_me', $token, [
                    'expires' => time() + (30 * 24 * 60 * 60),
                    'path' => '/',
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }

            ActivityLog::log('login', 'user', $user['id'], 'User logged in: ' . $user['full_name']);

            setFlashMessage('success', 'Welcome back, ' . $user['full_name'] . '!');
            redirect('?page=dashboard');
        }
        require_once VIEWS_PATH . '/auth/login.php';
        break;

    case 'register':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid request.');
                redirect('?page=auth&action=register');
            }

            $validator = new Validator($_POST);
            $validator->required('company_name')->maxLength('company_name', 150)
                      ->required('full_name')->maxLength('full_name', 100)
                      ->required('email')->email('email')->unique('email', 'users', 'email')
                      ->required('phone')->phone('phone', 'Phone')
                      ->required('password')->minLength('password', 8, 'Password')
                      ->required('password_confirm')->match('password', 'password_confirm', 'Password');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                $_SESSION['old_input'] = $_POST;
                redirect('?page=auth&action=register');
            }

            try {
                $db->beginTransaction();

                // Generate company slug from name
                $companyName = postData('company_name');
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $companyName), '-'));
                // Ensure slug uniqueness
                $slugBase = $slug;
                $slugCounter = 1;
                while (true) {
                    $stmtSlug = $db->prepare("SELECT id FROM companies WHERE slug = ?");
                    $stmtSlug->execute([$slug]);
                    if (!$stmtSlug->fetch()) break;
                    $slug = $slugBase . '-' . $slugCounter++;
                }

                // Create company record (plan=trial, subscription_end=+14 days)
                $stmtCompany = $db->prepare(
                    "INSERT INTO companies (name, slug, subscription_plan, subscription_end, is_active, created_at)
                     VALUES (?, ?, 'trial', DATE_ADD(NOW(), INTERVAL 14 DAY), 1, NOW())"
                );
                $stmtCompany->execute([$companyName, $slug]);
                $companyId = (int) $db->lastInsertId();

                // Create user record (role=company_admin, linked to new company)
                $userId = $userModel->create([
                    'full_name' => postData('full_name'),
                    'email' => postData('email'),
                    'phone' => postData('phone'),
                    'password' => postData('password'),
                    'role' => 'company_admin',
                    'company_id' => $companyId,
                ]);

                $db->commit();

                // Auto-login
                session_regenerate_id(true);
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_role'] = 'company_admin';
                $_SESSION['user_name'] = postData('full_name');
                $_SESSION['user_email'] = postData('email');
                $_SESSION['user_avatar'] = null;
                $_SESSION['company_id'] = $companyId;
                $_SESSION['company_name'] = $companyName;
                $_SESSION['company_plan'] = 'trial';
                $_SESSION['last_activity'] = time();

                ActivityLog::log('create', 'company', $companyId, 'New company registered: ' . $companyName);
                ActivityLog::log('create', 'user', $userId, 'Company admin registered: ' . postData('full_name'));

                setFlashMessage('success', 'Welcome to BizConnect! Your 14-day free trial has started.');
                redirect('?page=onboarding');

            } catch (\Throwable $e) {
                $db->rollBack();
                error_log('Registration error: ' . $e->getMessage());
                setFlashMessage('error', 'Registration failed. Please try again.');
                $_SESSION['old_input'] = $_POST;
                redirect('?page=auth&action=register');
            }
        }
        require_once VIEWS_PATH . '/auth/register.php';
        break;

    case 'logout':
        ActivityLog::log('logout', 'user', $_SESSION['user_id'] ?? null, 'User logged out');

        // Clear remember me token
        if (isset($_COOKIE['remember_me'])) {
            $tokenHash = hash('sha256', $_COOKIE['remember_me']);
            $stmt = $db->prepare("DELETE FROM remember_tokens WHERE token_hash = ?");
            $stmt->execute([$tokenHash]);
            setcookie('remember_me', '', time() - 3600, '/', '', false, true);
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        // Start new session for flash message
        session_start();
        setFlashMessage('success', 'You have been logged out.');
        redirect('?page=auth&action=login');
        break;

    case 'forgot_password':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid request. Please try again.');
                redirect('?page=auth&action=forgot_password');
            }

            $email = postData('email');
            $validator = new Validator($_POST);
            $validator->required('email')->email('email');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=auth&action=forgot_password');
            }

            $user = $userModel->findByEmail($email);

            if ($user) {
                // Invalidate previous tokens for this user
                $stmt = $db->prepare("UPDATE password_reset_tokens SET used = 1 WHERE user_id = ? AND used = 0");
                $stmt->execute([$user['id']]);

                // Generate new token (use MySQL NOW() to avoid timezone mismatch)
                $token = bin2hex(random_bytes(32));

                $stmt = $db->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
                $stmt->execute([$user['id'], $token]);

                // For local dev: show the reset link as a flash message
                $resetLink = '?page=auth&action=resetPassword&token=' . $token;
                setFlashMessage('info', 'Reset link (dev mode): <a href="' . $resetLink . '">' . $resetLink . '</a>');
            }

            // Always show same message to prevent email enumeration
            setFlashMessage('success', 'If an account with that email exists, a reset link has been generated.');
            redirect('?page=auth&action=forgot_password');
        }
        require_once VIEWS_PATH . '/auth/forgot_password.php';
        break;

    case 'resetPassword':
        $token = getData('token') ?: postData('token');

        if (empty($token)) {
            setFlashMessage('error', 'Invalid or missing reset token.');
            redirect('?page=auth&action=login');
        }

        $stmt = $db->prepare("SELECT * FROM password_reset_tokens WHERE token = ? AND used = 0 AND expires_at > NOW()");
        $stmt->execute([$token]);
        $resetToken = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resetToken) {
            setFlashMessage('error', 'This reset link is invalid or has expired. Please request a new one.');
            redirect('?page=auth&action=forgot_password');
        }

        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid request. Please try again.');
                redirect('?page=auth&action=resetPassword&token=' . $token);
            }

            $password = postData('password');
            $confirmPassword = postData('confirm_password');

            $validator = new Validator($_POST);
            $validator->required('password', 'Password')
                      ->minLength('password', 8, 'Password')
                      ->required('confirm_password', 'Confirm Password')
                      ->match('password', 'confirm_password', 'Password');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=auth&action=resetPassword&token=' . $token);
            }

            // Update password
            $userModel->updatePassword($resetToken['user_id'], $password);

            // Mark token as used
            $stmt = $db->prepare("UPDATE password_reset_tokens SET used = 1 WHERE id = ?");
            $stmt->execute([$resetToken['id']]);

            ActivityLog::log('update', 'user', $resetToken['user_id'], 'Password reset completed');

            setFlashMessage('success', 'Password reset successfully. Please log in with your new password.');
            redirect('?page=auth&action=login');
        }

        $pageTitle = 'Reset Password';
        require_once VIEWS_PATH . '/auth/reset_password.php';
        break;

    default:
        redirect('?page=auth&action=login');
        break;
}
