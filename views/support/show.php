<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Support', 'url' => '?page=support'], ['label' => 'Ticket #' . $ticket['id'], 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Ticket #<?php echo $ticket['id']; ?></h4>
    <a href="?page=support" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Tickets
    </a>
</div>

<?php
    // Priority badge colors
    $priorityColors = [
        'low'    => 'bg-secondary',
        'medium' => 'bg-primary',
        'high'   => 'bg-warning text-dark',
        'urgent' => 'bg-danger',
    ];
    $priorityClass = $priorityColors[$ticket['priority']] ?? 'bg-secondary';

    // Status badge colors
    $statusColors = [
        'open'        => 'bg-primary',
        'in_progress' => 'bg-warning text-dark',
        'waiting'     => 'bg-secondary',
        'resolved'    => 'bg-success',
        'closed'      => 'bg-secondary',
    ];
    $statusClass = $statusColors[$ticket['status']] ?? 'bg-secondary';
?>

<!-- Ticket Header -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h5 class="mb-2"><?php echo e($ticket['subject']); ?></h5>
                <div class="d-flex flex-wrap gap-2 align-items-center text-muted small">
                    <span>Opened by <strong><?php echo e($ticket['user_name'] ?? 'Unknown'); ?></strong></span>
                    <span>&middot;</span>
                    <span><?php echo formatDateTime($ticket['created_at']); ?></span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <span class="badge <?php echo $priorityClass; ?> fs-6"><?php echo ucfirst(e($ticket['priority'])); ?></span>
                <span class="badge <?php echo $statusClass; ?> fs-6"><?php echo ucwords(str_replace('_', ' ', e($ticket['status']))); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Original Message -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-chat-text me-1"></i>Original Message
    </div>
    <div class="card-body">
        <p class="mb-0"><?php echo nl2br(e($ticket['message'])); ?></p>
    </div>
</div>

<!-- Conversation Thread -->
<?php if (!empty($replies)): ?>
<div class="mb-4">
    <h6 class="text-muted mb-3"><i class="bi bi-chat-dots me-1"></i>Conversation</h6>
    <?php foreach ($replies as $reply): ?>
        <?php $isStaff = ($reply['user_type'] === 'staff'); ?>
        <div class="d-flex mb-3 <?php echo $isStaff ? 'justify-content-end' : 'justify-content-start'; ?>">
            <div style="max-width: 75%; min-width: 40%;">
                <div class="card <?php echo $isStaff ? 'border-primary' : 'border-0'; ?>" style="background-color: <?php echo $isStaff ? '#e8f0fe' : '#f0f0f0'; ?>;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="small">
                                <?php if ($isStaff): ?>
                                    <i class="bi bi-person me-1"></i><?php echo e($reply['author_name'] ?? 'Staff'); ?>
                                <?php else: ?>
                                    <i class="bi bi-headset me-1"></i><?php echo e($reply['author_name'] ?? 'BizConnect Support'); ?>
                                <?php endif; ?>
                            </strong>
                            <small class="text-muted"><?php echo formatDateTime($reply['created_at']); ?></small>
                        </div>
                        <p class="mb-0 small"><?php echo nl2br(e($reply['message'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Reply Form -->
<?php if (!in_array($ticket['status'], ['closed', 'resolved'])): ?>
<div class="card">
    <div class="card-header">
        <i class="bi bi-reply me-1"></i>Reply
    </div>
    <div class="card-body">
        <form method="POST" action="?page=support&action=reply&id=<?php echo $ticket['id']; ?>">
            <?php echo csrfField(); ?>
            <div class="mb-3">
                <textarea name="message" class="form-control" rows="3" required placeholder="Type your reply..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i>Send Reply
            </button>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-secondary text-center">
    <i class="bi bi-lock me-1"></i>This ticket is <?php echo e($ticket['status']); ?>. No further replies can be added.
</div>
<?php endif; ?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
