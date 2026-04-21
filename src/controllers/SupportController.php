<?php
/**
 * Support Ticket Controller
 */
requireAuth();

require_once SRC_PATH . '/models/SupportTicket.php';

$action = $action ?? 'index';

switch ($action) {
    case 'index':
        $tickets = SupportTicket::getByCompany(companyId());
        $pageTitle = 'Support Tickets';
        require_once VIEWS_PATH . '/support/index.php';
        break;

    case 'create':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=support&action=create');
            }

            $subject = trim(postData('subject'));
            $message = trim(postData('message'));
            $priority = postData('priority', 'medium');

            // Validate
            $errors = [];
            if (empty($subject)) {
                $errors[] = 'Subject is required.';
            }
            if (empty($message)) {
                $errors[] = 'Message is required.';
            }
            if (!in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
                $priority = 'medium';
            }

            if (!empty($errors)) {
                setFlashMessage('error', implode(' ', $errors));
                redirect('?page=support&action=create');
            }

            $ticketId = SupportTicket::create([
                'company_id' => companyId(),
                'user_id'    => currentUserId(),
                'subject'    => $subject,
                'message'    => $message,
                'priority'   => $priority,
            ]);

            setFlashMessage('success', 'Support ticket created. We\'ll respond shortly.');
            redirect('?page=support&action=show&id=' . $ticketId);
        }

        $pageTitle = 'New Support Ticket';
        require_once VIEWS_PATH . '/support/create.php';
        break;

    case 'show':
        $id = (int) ($id ?? 0);
        $ticket = SupportTicket::findById($id);

        if (!$ticket || $ticket['company_id'] != companyId()) {
            setFlashMessage('error', 'Ticket not found.');
            redirect('?page=support');
        }

        $replies = SupportTicket::getReplies($id);
        $pageTitle = 'Ticket #' . $id;
        require_once VIEWS_PATH . '/support/show.php';
        break;

    case 'reply':
        if (isPost()) {
            $id = (int) ($id ?? 0);

            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=support&action=show&id=' . $id);
            }

            $ticket = SupportTicket::findById($id);
            if ($ticket && $ticket['company_id'] == companyId()) {
                $message = trim(postData('message'));
                if (!empty($message)) {
                    SupportTicket::addReply($id, 'staff', currentUserId(), $message);
                    // Update ticket timestamp
                    $db = Database::getInstance();
                    $db->prepare("UPDATE support_tickets SET updated_at = NOW() WHERE id = ?")->execute([$id]);
                }
            }
            redirect('?page=support&action=show&id=' . $id);
        }
        redirect('?page=support');
        break;

    default:
        redirect('?page=support');
        break;
}
