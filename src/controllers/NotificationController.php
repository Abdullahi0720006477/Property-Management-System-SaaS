<?php
/**
 * Notification Controller
 */
require_once SRC_PATH . '/models/Notification.php';

requireAuth();

$notifModel = new Notification();
$userId = currentUserId();

switch ($action) {
    case 'index':
        $pageTitle = 'Notifications';
        $notifications = $notifModel->getByUser($userId, 100);
        require_once VIEWS_PATH . '/notifications/index.php';
        break;

    case 'read':
        if ($id) {
            $notifModel->markAsRead($id, $userId);
        }
        redirect('?page=notifications');
        break;

    case 'read_all':
        $notifModel->markAllAsRead($userId);
        setFlashMessage('success', 'All notifications marked as read.');
        redirect('?page=notifications');
        break;

    case 'delete':
        if ($id) {
            $notifModel->delete($id, $userId);
            setFlashMessage('success', 'Notification deleted.');
        }
        redirect('?page=notifications');
        break;

    default:
        $pageTitle = 'Notifications';
        $notifications = $notifModel->getByUser($userId, 100);
        require_once VIEWS_PATH . '/notifications/index.php';
        break;
}
