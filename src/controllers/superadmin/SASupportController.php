<?php
require_once __DIR__ . '/../../models/SupportTicket.php';
require_once __DIR__ . '/../../models/SuperAdmin.php';

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $filters = [];
        if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
        if (!empty($_GET['priority'])) $filters['priority'] = $_GET['priority'];
        $tickets = SupportTicket::getAll($filters);
        $pageTitle = 'Support Tickets';
        require_once VIEWS_PATH . '/superadmin/support/index.php';
        break;

    case 'show':
        $id = (int)($_GET['id'] ?? 0);
        $ticket = SupportTicket::findById($id);
        if (!$ticket) {
            echo '<h3>Ticket not found</h3>';
            exit;
        }
        // Handle reply
        if (isPost()) {
            $postAction = postData('post_action');
            if ($postAction === 'reply') {
                $message = postData('message');
                if (!empty($message)) {
                    SupportTicket::addReply($id, 'super_admin', $_SESSION['sa_id'], $message);
                }
            } elseif ($postAction === 'update') {
                $updateData = [];
                if (!empty($_POST['status'])) $updateData['status'] = postData('status');
                if (!empty($_POST['priority'])) $updateData['priority'] = postData('priority');
                if (isset($_POST['assigned_to'])) $updateData['assigned_to'] = (int)postData('assigned_to') ?: null;
                if (!empty($updateData)) {
                    SupportTicket::update($id, $updateData);
                }
            }
            // Refresh ticket data
            $ticket = SupportTicket::findById($id);
        }
        $replies = SupportTicket::getReplies($id);
        $admins = SuperAdmin::getAll();
        $pageTitle = 'Ticket: ' . $ticket['subject'];
        require_once VIEWS_PATH . '/superadmin/support/show.php';
        break;

    default:
        header('Location: superadmin.php?page=support');
        exit;
}
