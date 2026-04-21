<?php
require_once __DIR__ . '/../../models/Announcement.php';

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $announcements = Announcement::getAll();
        $pageTitle = 'Announcements';
        require_once VIEWS_PATH . '/superadmin/announcements/index.php';
        break;

    case 'create':
        if (isPost()) {
            $data = [
                'title' => postData('title'),
                'message' => postData('message'),
                'type' => postData('type') ?: 'info',
                'target' => postData('target') ?: 'all',
                'is_active' => 1,
                'starts_at' => postData('starts_at') ?: null,
                'ends_at' => postData('ends_at') ?: null,
                'created_by' => $_SESSION['sa_id'],
            ];
            Announcement::create($data);
            header('Location: superadmin.php?page=announcements');
            exit;
        }
        $pageTitle = 'Create Announcement';
        require_once VIEWS_PATH . '/superadmin/announcements/create.php';
        break;

    default:
        header('Location: superadmin.php?page=announcements');
        exit;
}
