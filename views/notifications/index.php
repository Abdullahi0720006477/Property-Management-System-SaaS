<?php $pageTitle = 'Notifications'; require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Notifications', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Notifications</h4>
    <?php if (!empty($notifications)): ?>
        <a href="?page=notifications&action=read_all" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-check-all me-1"></i>Mark All as Read
        </a>
    <?php endif; ?>
</div>

<?php if (empty($notifications)): ?>
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-bell fs-1 d-block mb-3"></i>
            <p>No notifications yet.</p>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="list-group list-group-flush">
            <?php foreach ($notifications as $notif): ?>
                <div class="list-group-item <?php echo !$notif['is_read'] ? 'bg-light' : ''; ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <?php if (!$notif['is_read']): ?>
                                    <span class="badge bg-primary me-2">New</span>
                                <?php endif; ?>
                                <?php
                                $icon = match($notif['type']) {
                                    'payment_due' => 'bi-cash-coin text-warning',
                                    'maintenance' => 'bi-tools text-info',
                                    'lease_expiry' => 'bi-file-earmark-text text-danger',
                                    default => 'bi-bell text-secondary',
                                };
                                ?>
                                <i class="bi <?php echo $icon; ?> me-2"></i>
                                <strong><?php echo e($notif['title']); ?></strong>
                            </div>
                            <p class="mb-1 text-muted"><?php echo e($notif['message']); ?></p>
                            <small class="text-muted"><?php echo formatDateTime($notif['created_at']); ?></small>
                        </div>
                        <div class="ms-3 d-flex gap-1">
                            <?php if (!$notif['is_read']): ?>
                                <a href="?page=notifications&action=read&id=<?php echo $notif['id']; ?>" class="btn btn-sm btn-outline-success" title="Mark as read">
                                    <i class="bi bi-check"></i>
                                </a>
                            <?php endif; ?>
                            <a href="?page=notifications&action=delete&id=<?php echo $notif['id']; ?>" class="btn btn-sm btn-outline-danger btn-delete" title="Delete">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
