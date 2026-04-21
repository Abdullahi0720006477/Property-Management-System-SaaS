<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Support Tickets</h4>
    <span class="badge bg-secondary"><?= count($tickets) ?> tickets</span>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="support">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="open" <?= ($_GET['status'] ?? '') === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="in_progress" <?= ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="waiting" <?= ($_GET['status'] ?? '') === 'waiting' ? 'selected' : '' ?>>Waiting</option>
                    <option value="resolved" <?= ($_GET['status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                    <option value="closed" <?= ($_GET['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select form-select-sm">
                    <option value="">All Priorities</option>
                    <option value="low" <?= ($_GET['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= ($_GET['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= ($_GET['priority'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                    <option value="urgent" <?= ($_GET['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="superadmin.php?page=support" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<?php
$priorityColors = ['low' => 'bg-secondary', 'medium' => 'bg-primary', 'high' => 'bg-warning text-dark', 'urgent' => 'bg-danger'];
$statusColors = ['open' => 'bg-primary', 'in_progress' => 'bg-warning text-dark', 'waiting' => 'bg-secondary', 'resolved' => 'bg-success', 'closed' => 'bg-secondary'];
?>

<!-- Tickets Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="font-size:0.8rem;">Ticket #</th>
                        <th style="font-size:0.8rem;">Company</th>
                        <th style="font-size:0.8rem;">Subject</th>
                        <th style="font-size:0.8rem;">Priority</th>
                        <th style="font-size:0.8rem;">Status</th>
                        <th style="font-size:0.8rem;">Assigned To</th>
                        <th style="font-size:0.8rem;">Updated</th>
                        <th style="font-size:0.8rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td class="fw-semibold">#<?= $t['id'] ?></td>
                        <td>
                            <a href="superadmin.php?page=companies&action=show&id=<?= $t['company_id'] ?>" class="text-decoration-none"><?= e($t['company_name'] ?? 'N/A') ?></a>
                        </td>
                        <td>
                            <a href="superadmin.php?page=support&action=show&id=<?= $t['id'] ?>" class="text-decoration-none"><?= e($t['subject']) ?></a>
                        </td>
                        <td><span class="badge <?= $priorityColors[$t['priority']] ?? 'bg-secondary' ?>"><?= ucfirst(e($t['priority'])) ?></span></td>
                        <td><span class="badge <?= $statusColors[$t['status']] ?? 'bg-secondary' ?>"><?= ucwords(str_replace('_', ' ', e($t['status']))) ?></span></td>
                        <td style="font-size:0.85rem;"><?= e($t['assigned_name'] ?? 'Unassigned') ?></td>
                        <td style="font-size:0.8rem;"><?= !empty($t['updated_at']) ? formatDate($t['updated_at']) : '-' ?></td>
                        <td>
                            <a href="superadmin.php?page=support&action=show&id=<?= $t['id'] ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($tickets)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No tickets found</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
