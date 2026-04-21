<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Support', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Support Tickets</h4>
    <a href="?page=support&action=create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>New Ticket
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th>Subject</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $ticket): ?>
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
                            <tr>
                                <td><strong>#<?php echo $ticket['id']; ?></strong></td>
                                <td>
                                    <a href="?page=support&action=show&id=<?php echo $ticket['id']; ?>">
                                        <?php echo e($ticket['subject']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge <?php echo $priorityClass; ?>">
                                        <?php echo ucfirst(e($ticket['priority'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo ucwords(str_replace('_', ' ', e($ticket['status']))); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDateTime($ticket['updated_at'] ?? $ticket['created_at']); ?></td>
                                <td>
                                    <a href="?page=support&action=show&id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-headset fs-1 d-block mb-2"></i>
                                No support tickets found. <a href="?page=support&action=create">Create one</a> to get help.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
