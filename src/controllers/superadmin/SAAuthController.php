<?php
require_once __DIR__ . '/../../models/SuperAdmin.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        if (isPost()) {
            $email = postData('email');
            $password = postData('password');
            $errors = [];

            if (empty($email) || empty($password)) {
                $errors[] = 'Email and password are required.';
            }

            if (empty($errors)) {
                $admin = SuperAdmin::findByEmail($email);
                if ($admin && password_verify($password, $admin['password_hash'])) {
                    $_SESSION['sa_id'] = $admin['id'];
                    $_SESSION['sa_name'] = $admin['full_name'];
                    $_SESSION['sa_role'] = $admin['role'] ?? 'super_admin';
                    SuperAdmin::updateLastLogin($admin['id']);
                    header('Location: superadmin.php?page=dashboard');
                    exit;
                } else {
                    $errors[] = 'Invalid email or password.';
                }
            }
        }
        require_once VIEWS_PATH . '/superadmin/auth/login.php';
        break;

    case 'logout':
        session_destroy();
        header('Location: superadmin.php?page=auth&action=login');
        exit;
        break;

    default:
        header('Location: superadmin.php?page=auth&action=login');
        exit;
}
