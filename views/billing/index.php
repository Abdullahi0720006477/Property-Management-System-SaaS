<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Billing', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-receipt me-2"></i>Billing</h4>
</div>

<!-- Current Plan Card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="card-title mb-1">Current Plan</h5>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge fs-6" style="background-color: <?= e($currentPlan['badge_color']); ?>;">
                        <?= e($currentPlan['name']); ?>
                    </span>
                    <?= statusBadge($company['subscription_status'] ?? 'active'); ?>
                </div>
                <?php if (!empty($company['subscription_end'])): ?>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar-event me-1"></i>
                        <?php
                        $daysLeft = (int)((strtotime($company['subscription_end']) - time()) / 86400);
                        if ($daysLeft > 0): ?>
                            Expires on <?= formatDate($company['subscription_end']); ?>
                            <span class="text-<?= $daysLeft <= 7 ? 'danger' : 'muted'; ?>">
                                (<?= $daysLeft; ?> day<?= $daysLeft !== 1 ? 's' : ''; ?> remaining)
                            </span>
                        <?php else: ?>
                            <span class="text-danger">Expired on <?= formatDate($company['subscription_end']); ?></span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <?php if ($currentPlan['price_monthly'] > 0): ?>
                    <p class="text-muted mb-0 mt-1">
                        <i class="bi bi-cash me-1"></i>
                        <?= formatCurrency($currentPlan['price_monthly']); ?>/month
                    </p>
                <?php endif; ?>
            </div>
            <a href="?page=billing&action=plans" class="btn btn-outline-primary">
                <i class="bi bi-arrow-repeat me-1"></i> Change Plan
            </a>
        </div>
    </div>
</div>

<!-- Usage Meters -->
<div class="row g-3 mb-4">
    <?php
    $meters = [
        'properties' => ['label' => 'Properties', 'icon' => 'bi-building'],
        'units'      => ['label' => 'Units',      'icon' => 'bi-door-open'],
        'users'      => ['label' => 'Users',      'icon' => 'bi-people'],
    ];
    foreach ($meters as $key => $meter):
        $used = $usage[$key]['used'];
        $max  = $usage[$key]['max'];
        $isUnlimited = ($max === -1);
        $percentage = $isUnlimited ? 0 : ($max > 0 ? round(($used / $max) * 100) : 0);
        $barColor = $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-primary');
    ?>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="bi <?= $meter['icon']; ?> me-1"></i> <?= $meter['label']; ?></h6>
                    <span class="text-muted">
                        <?= $used; ?> of <?= $isUnlimited ? 'Unlimited' : $max; ?>
                    </span>
                </div>
                <?php if ($isUnlimited): ?>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 100%;"></div>
                    </div>
                    <small class="text-success">Unlimited</small>
                <?php else: ?>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar <?= $barColor; ?>" style="width: <?= min($percentage, 100); ?>%;"></div>
                    </div>
                    <small class="text-muted"><?= $percentage; ?>% used</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Invoice History -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2"></i>Invoice History</h5>
    </div>
    <div class="card-body">
        <?php if (empty($invoices)): ?>
            <div class="text-center py-4 text-muted">
                <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                No invoices found.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table data-table align-middle">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Paid Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $inv): ?>
                        <tr>
                            <td><strong><?= e($inv['invoice_number']); ?></strong></td>
                            <td><?= formatCurrency((float)($inv['total_amount'] ?? $inv['amount'])); ?></td>
                            <td><?= statusBadge($inv['status']); ?></td>
                            <td><?= formatDate($inv['due_date'] ?? null); ?></td>
                            <td><?= formatDate($inv['paid_date'] ?? null); ?></td>
                            <td>
                                <a href="?page=billing&action=invoice_detail&id=<?= (int)$inv['id']; ?>" class="btn btn-sm btn-outline-primary" title="View Invoice">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
