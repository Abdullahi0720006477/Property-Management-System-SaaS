<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<?php
$priorityColors = ['low' => 'bg-secondary', 'medium' => 'bg-primary', 'high' => 'bg-warning text-dark', 'urgent' => 'bg-danger'];
$statusColors = ['open' => 'bg-primary', 'in_progress' => 'bg-warning text-dark', 'waiting' => 'bg-secondary', 'resolved' => 'bg-success', 'closed' => 'bg-secondary'];
$priorityClass = $priorityColors[$ticket['priority']] ?? 'bg-secondary';
$statusClass = $statusColors[$ticket['status']] ?? 'bg-secondary';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Ticket #<?= $ticket['id'] ?></h4>
    <a href="superadmin.php?page=support" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Tickets</a>
</div>

<!-- Ticket Header -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h5 class="mb-2"><?= e($ticket['subject']) ?></h5>
                <div class="d-flex flex-wrap gap-2 align-items-center text-muted small">
                    <span>Company: <strong><?= e($ticket['company_name'] ?? 'N/A') ?></strong></span>
                    <span>&middot;</span>
                    <span>Opened by <strong><?= e($ticket['user_name'] ?? 'Unknown') ?></strong></span>
                    <span>&middot;</span>
                    <span><?= formatDate($ticket['created_at']) ?></span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <span class="badge <?= $priorityClass ?> fs-6"><?= ucfirst(e($ticket['priority'])) ?></span>
                <span class="badge <?= $statusClass ?> fs-6"><?= ucwords(str_replace('_', ' ', e($ticket['status']))) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Controls -->
<div class="card mb-4">
    <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Ticket Controls</h6></div>
    <div class="card-body">
        <form method="POST" action="superadmin.php?page=support&action=show&id=<?= $ticket['id'] ?>">
            <input type="hidden" name="post_action" value="update">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                        <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="waiting" <?= $ticket['status'] === 'waiting' ? 'selected' : '' ?>>Waiting</option>
                        <option value="resolved" <?= $ticket['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                        <option value="closed" <?= $ticket['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Priority</label>
                    <select name="priority" class="form-select form-select-sm">
                        <option value="low" <?= $ticket['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= $ticket['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= $ticket['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                        <option value="urgent" <?= $ticket['priority'] === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Assign To</label>
                    <select name="assigned_to" class="form-select form-select-sm">
                        <option value="0">Unassigned</option>
                        <?php foreach ($admins as $admin): ?>
                            <option value="<?= $admin['id'] ?>" <?= ($ticket['assigned_to'] ?? 0) == $admin['id'] ? 'selected' : '' ?>><?= e($admin['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-check-lg me-1"></i>Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Original Message -->
<div class="card mb-4">
    <div class="card-header bg-transparent">
        <i class="bi bi-chat-text me-1"></i>Original Message
    </div>
    <div class="card-body">
        <p class="mb-0"><?= nl2br(e($ticket['message'])) ?></p>
    </div>
</div>

<!-- Conversation Thread -->
<?php if (!empty($replies)): ?>
<div class="mb-4">
    <h6 class="text-muted mb-3"><i class="bi bi-chat-dots me-1"></i>Conversation</h6>
    <?php foreach ($replies as $reply): ?>
        <?php $isAdmin = ($reply['user_type'] === 'super_admin'); ?>
        <div class="d-flex mb-3 <?= $isAdmin ? 'justify-content-end' : 'justify-content-start' ?>">
            <div style="max-width: 75%; min-width: 40%;">
                <div class="card <?= $isAdmin ? 'border-primary' : 'border-0' ?>" style="background-color: <?= $isAdmin ? '#e8f0fe' : '#f0f0f0' ?>;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="small">
                                <?php if ($isAdmin): ?>
                                    <i class="bi bi-shield-check me-1"></i><?= e($reply['author_name'] ?? 'Admin') ?>
                                <?php else: ?>
                                    <i class="bi bi-person me-1"></i><?= e($reply['author_name'] ?? 'User') ?>
                                <?php endif; ?>
                            </strong>
                            <small class="text-muted"><?= formatDate($reply['created_at']) ?></small>
                        </div>
                        <p class="mb-0 small"><?= nl2br(e($reply['message'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Reply Form -->
<?php if (!in_array($ticket['status'], ['closed'])): ?>
<div class="card">
    <div class="card-header bg-transparent">
        <i class="bi bi-reply me-1"></i>Reply
    </div>
    <div class="card-body">
        <form method="POST" action="superadmin.php?page=support&action=show&id=<?= $ticket['id'] ?>">
            <input type="hidden" name="post_action" value="reply">
            <div class="mb-3">
                <textarea name="message" class="form-control" rows="3" required placeholder="Type your reply..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Send Reply</button>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-secondary text-center">
    <i class="bi bi-lock me-1"></i>This ticket is closed. No further replies can be added.
</div>
<?php endif; ?>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
