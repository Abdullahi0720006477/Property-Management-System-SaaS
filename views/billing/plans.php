<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Billing', 'url' => '?page=billing'], ['label' => 'Plans', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-grid me-2"></i>Subscription Plans</h4>
    <a href="?page=billing" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Billing
    </a>
</div>

<div class="row g-4">
    <?php
    $featureLabels = [
        'properties' => 'Property Management',
        'units' => 'Unit Management',
        'tenants' => 'Tenant Management',
        'leases' => 'Lease Management',
        'payments' => 'Payment Tracking',
        'maintenance' => 'Maintenance Requests',
        'expenses' => 'Expense Tracking',
        'basic_reports' => 'Basic Reports',
        'advanced_reports' => 'Advanced Reports',
        'email_notifications' => 'Email Notifications',
        'document_storage' => 'Document Storage',
        'api_access' => 'API Access',
        'priority_support' => 'Priority Support',
        'all' => 'All Features Included',
    ];

    foreach ($plans as $planKey => $plan):
        $isCurrent = ($planKey === $currentPlanKey);
        $isUnlimited = ($plan['max_properties'] === -1);
    ?>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 <?= $isCurrent ? 'border-primary border-2' : ''; ?>">
            <?php if ($isCurrent): ?>
                <div class="card-header bg-primary text-white text-center py-2">
                    <strong><i class="bi bi-check-circle me-1"></i> Current Plan</strong>
                </div>
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
                <div class="text-center mb-3">
                    <span class="badge fs-6 mb-2" style="background-color: <?= e($plan['badge_color']); ?>;">
                        <?= e($plan['name']); ?>
                    </span>
                    <div class="mt-2">
                        <?php if ($plan['price_monthly'] == 0): ?>
                            <span class="fs-3 fw-bold">Free</span>
                            <?php if (!empty($plan['duration_days'])): ?>
                                <small class="text-muted d-block"><?= $plan['duration_days']; ?> days trial</small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="fs-3 fw-bold"><?= formatCurrency($plan['price_monthly']); ?></span>
                            <small class="text-muted d-block">per month</small>
                            <small class="text-muted"><?= formatCurrency($plan['price_yearly']); ?>/year</small>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <!-- Limits -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Properties</span>
                        <strong><?= $plan['max_properties'] === -1 ? 'Unlimited' : $plan['max_properties']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Units</span>
                        <strong><?= $plan['max_units'] === -1 ? 'Unlimited' : $plan['max_units']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Users</span>
                        <strong><?= $plan['max_users'] === -1 ? 'Unlimited' : $plan['max_users']; ?></strong>
                    </div>
                </div>

                <hr>

                <!-- Features -->
                <ul class="list-unstyled small flex-grow-1">
                    <?php foreach ($plan['features'] as $feature): ?>
                        <li class="mb-1">
                            <i class="bi bi-check-lg text-success me-1"></i>
                            <?= e($featureLabels[$feature] ?? ucwords(str_replace('_', ' ', $feature))); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Action -->
                <div class="mt-3 text-center">
                    <?php if ($isCurrent): ?>
                        <button class="btn btn-primary w-100" disabled>
                            <i class="bi bi-check-circle me-1"></i> Current Plan
                        </button>
                    <?php else: ?>
                        <button class="btn btn-outline-primary w-100" disabled title="Contact support to change your plan">
                            <?php
                            $planOrder = ['trial' => 0, 'starter' => 1, 'professional' => 2, 'enterprise' => 3];
                            $currentOrder = $planOrder[$currentPlanKey] ?? 0;
                            $thisOrder = $planOrder[$planKey] ?? 0;
                            echo $thisOrder > $currentOrder ? 'Upgrade' : 'Downgrade';
                            ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="alert alert-info mt-4">
    <i class="bi bi-info-circle me-2"></i>
    To change your subscription plan, please contact support at
    <a href="mailto:support@bizconnect.co.ke">support@bizconnect.co.ke</a>
    or use the <a href="?page=support">support page</a>.
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
