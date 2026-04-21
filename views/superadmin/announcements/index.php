<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Announcements</h4>
    <a href="superadmin.php?page=announcements&action=create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Create Announcement</a>
</div>

<?php
$typeBadges = [
    'info' => 'bg-primary',
    'warning' => 'bg-warning text-dark',
    'feature' => 'bg-success',
    'maintenance' => 'bg-secondary',
];
?>

<!-- Announcements Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="font-size:0.8rem;">Title</th>
                        <th style="font-size:0.8rem;">Type</th>
                        <th style="font-size:0.8rem;">Target</th>
                        <th style="font-size:0.8rem;">Active</th>
                        <th style="font-size:0.8rem;">Start Date</th>
                        <th style="font-size:0.8rem;">End Date</th>
                        <th style="font-size:0.8rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($announcements as $a): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($a['title']) ?></td>
                        <td><span class="badge <?= $typeBadges[$a['type']] ?? 'bg-secondary' ?>"><?= ucfirst(e($a['type'])) ?></span></td>
                        <td>
                            <?php if ($a['target'] === 'all'): ?>
                                <span class="badge bg-dark">All</span>
                            <?php else: ?>
                                <?php $tp = SUBSCRIPTION_PLANS[$a['target']] ?? null; ?>
                                <span class="badge" style="background:<?= $tp['badge_color'] ?? '#94A3B8' ?>;"><?= e($tp['name'] ?? ucfirst($a['target'])) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($a['is_active']): ?>
                                <span class="badge bg-success">Yes</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">No</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:0.8rem;"><?= !empty($a['starts_at']) ? formatDate($a['starts_at']) : '-' ?></td>
                        <td style="font-size:0.8rem;"><?= !empty($a['ends_at']) ? formatDate($a['ends_at']) : '-' ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="superadmin.php?page=announcements&action=edit&id=<?= $a['id'] ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                                <a href="superadmin.php?page=announcements&action=toggle&id=<?= $a['id'] ?>" class="btn btn-outline-<?= $a['is_active'] ? 'warning' : 'success' ?> btn-sm"><i class="bi bi-<?= $a['is_active'] ? 'pause' : 'play' ?>"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($announcements)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No announcements yet</td></tr>
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
