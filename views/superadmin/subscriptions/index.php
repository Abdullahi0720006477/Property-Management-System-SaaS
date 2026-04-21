<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Subscriptions</h4>
    <span class="badge bg-secondary"><?= count($subscriptions) ?> active</span>
</div>

<!-- Plan Stat Cards -->
<div class="row g-3 mb-4">
    <?php
    $planIcons = ['trial' => 'bi-clock', 'starter' => 'bi-rocket', 'professional' => 'bi-briefcase', 'enterprise' => 'bi-building'];
    $planBgs = ['trial' => 'rgba(245,158,11,0.1)', 'starter' => 'rgba(59,130,246,0.1)', 'professional' => 'rgba(16,185,129,0.1)', 'enterprise' => 'rgba(139,92,246,0.1)'];
    $planColors = ['trial' => '#F59E0B', 'starter' => 'var(--color-primary)', 'professional' => '#10B981', 'enterprise' => '#8B5CF6'];
    foreach (['trial', 'starter', 'professional', 'enterprise'] as $planKey):
        $plan = SUBSCRIPTION_PLANS[$planKey] ?? null;
        if (!$plan) continue;
    ?>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.8rem;"><?= e($plan['name']) ?></p>
                        <h3 class="fw-bold mb-0"><?= $planDistribution[$planKey] ?? 0 ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:<?= $planBgs[$planKey] ?>;color:<?= $planColors[$planKey] ?>;">
                        <i class="bi <?= $planIcons[$planKey] ?>"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Subscriptions Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="font-size:0.8rem;">Company</th>
                        <th style="font-size:0.8rem;">Plan</th>
                        <th style="font-size:0.8rem;">Status</th>
                        <th style="font-size:0.8rem;">Start Date</th>
                        <th style="font-size:0.8rem;">End Date</th>
                        <th style="font-size:0.8rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($subscriptions as $s): ?>
                    <tr>
                        <td>
                            <a href="superadmin.php?page=companies&action=show&id=<?= $s['id'] ?>" class="fw-semibold text-decoration-none"><?= e($s['name']) ?></a>
                            <div style="font-size:0.75rem;color:var(--color-text-secondary);"><?= e($s['email']) ?></div>
                        </td>
                        <td>
                            <?php $p = SUBSCRIPTION_PLANS[$s['subscription_plan']] ?? null; ?>
                            <span class="badge" style="background:<?= $p['badge_color'] ?? '#94A3B8' ?>;"><?= e($p['name'] ?? $s['subscription_plan']) ?></span>
                        </td>
                        <td><?= statusBadge($s['subscription_status'] ?? 'active') ?></td>
                        <td style="font-size:0.8rem;"><?= !empty($s['subscription_start']) ? formatDate($s['subscription_start']) : '-' ?></td>
                        <td style="font-size:0.8rem;"><?= !empty($s['subscription_end']) ? formatDate($s['subscription_end']) : '-' ?></td>
                        <td>
                            <a href="superadmin.php?page=subscriptions&action=manage&id=<?= $s['id'] ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-gear me-1"></i>Manage</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($subscriptions)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No active subscriptions found</td></tr>
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
