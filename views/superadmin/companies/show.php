<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<?= breadcrumbs([
    ['label' => 'Companies', 'url' => 'superadmin.php?page=companies'],
    ['label' => $company['name'], 'url' => '']
]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><?= e($company['name']) ?></h4>
    <div class="d-flex gap-2">
        <a href="superadmin.php?page=companies&action=edit&id=<?= $company['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="superadmin.php?page=subscriptions&action=manage&id=<?= $company['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-credit-card me-1"></i>Manage Plan</a>
    </div>
</div>

<!-- Company Detail Card -->
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Company Details</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.75rem;">Email</label>
                        <p class="mb-2 fw-semibold"><?= e($company['email']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.75rem;">Phone</label>
                        <p class="mb-2 fw-semibold"><?= e($company['phone'] ?? 'N/A') ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.75rem;">Address</label>
                        <p class="mb-2 fw-semibold"><?= e($company['address'] ?? 'N/A') ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.75rem;">City / Country</label>
                        <p class="mb-2 fw-semibold"><?= e($company['city'] ?? '') ?> <?= e($company['country'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.75rem;">Plan</label>
                        <p class="mb-2"><?php $p = SUBSCRIPTION_PLANS[$company['subscription_plan']] ?? null; ?>
                            <span class="badge" style="background:<?= $p['badge_color'] ?? '#94A3B8' ?>;"><?= e($p['name'] ?? $company['subscription_plan']) ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.75rem;">Status</label>
                        <p class="mb-2"><?= statusBadge($company['subscription_status'] ?? 'active') ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.75rem;">Subscription Start</label>
                        <p class="mb-2 fw-semibold"><?= formatDate($company['subscription_start'] ?? null) ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted" style="font-size:0.75rem;">Subscription End</label>
                        <p class="mb-2 fw-semibold"><?= formatDate($company['subscription_end'] ?? null) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Usage Stats</h6></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span style="font-size:0.85rem;">Properties</span>
                    <span class="fw-bold"><?= $propertyCount ?> / <?= $company['max_properties'] == -1 ? 'Unlimited' : $company['max_properties'] ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span style="font-size:0.85rem;">Users</span>
                    <span class="fw-bold"><?= $userCount ?> / <?= $company['max_users'] == -1 ? 'Unlimited' : $company['max_users'] ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span style="font-size:0.85rem;">Units</span>
                    <span class="fw-bold"><?= $unitCount ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span style="font-size:0.85rem;">Joined</span>
                    <span class="fw-bold"><?= formatDate($company['created_at']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subscription History -->
<div class="card mb-4">
    <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Subscription History</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead><tr>
                    <th style="font-size:0.8rem;">Action</th>
                    <th style="font-size:0.8rem;">Old Plan</th>
                    <th style="font-size:0.8rem;">New Plan</th>
                    <th style="font-size:0.8rem;">Amount</th>
                    <th style="font-size:0.8rem;">By</th>
                    <th style="font-size:0.8rem;">Date</th>
                    <th style="font-size:0.8rem;">Notes</th>
                </tr></thead>
                <tbody>
                <?php foreach ($subscriptionHistory as $sh): ?>
                    <tr>
                        <td><span class="badge bg-info"><?= e($sh['action']) ?></span></td>
                        <td><?= e($sh['old_plan'] ?? '-') ?></td>
                        <td><?= e($sh['new_plan'] ?? '-') ?></td>
                        <td><?= $sh['amount'] ? formatCurrency($sh['amount']) : '-' ?></td>
                        <td style="font-size:0.8rem;"><?= e($sh['performed_by_name'] ?? 'System') ?></td>
                        <td style="font-size:0.8rem;"><?= formatDateTime($sh['created_at']) ?></td>
                        <td style="font-size:0.8rem;"><?= e($sh['notes'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($subscriptionHistory)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">No subscription history</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Invoices -->
<div class="card">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Invoices</h6>
        <a href="superadmin.php?page=invoices&action=create&company_id=<?= $company['id'] ?>" class="btn btn-sm btn-outline-primary">Create Invoice</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead><tr>
                    <th style="font-size:0.8rem;">Invoice #</th>
                    <th style="font-size:0.8rem;">Amount</th>
                    <th style="font-size:0.8rem;">Status</th>
                    <th style="font-size:0.8rem;">Due Date</th>
                    <th style="font-size:0.8rem;">Created</th>
                </tr></thead>
                <tbody>
                <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td><a href="superadmin.php?page=invoices&action=show&id=<?= $inv['id'] ?>" class="text-decoration-none"><?= e($inv['invoice_number']) ?></a></td>
                        <td class="fw-semibold"><?= formatCurrency($inv['total_amount']) ?></td>
                        <td><?= statusBadge($inv['status']) ?></td>
                        <td style="font-size:0.8rem;"><?= formatDate($inv['due_date']) ?></td>
                        <td style="font-size:0.8rem;"><?= formatDate($inv['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($invoices)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">No invoices</td></tr>
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
