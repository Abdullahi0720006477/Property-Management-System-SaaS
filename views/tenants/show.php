<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Tenants', 'url' => '?page=tenants'], ['label' => e($tenant['first_name'] . ' ' . $tenant['last_name']), 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?php echo e($tenant['first_name'] . ' ' . $tenant['last_name']); ?></h1>
    <div class="d-flex gap-2">
        <a href="?page=tenants&action=edit&id=<?php echo $tenant['id']; ?>" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="?page=tenants" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Tenants
        </a>
    </div>
</div>

<div class="row">
    <!-- Tenant Profile Card -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-person-circle display-1 text-primary"></i>
                </div>
                <h5 class="card-title"><?php echo e($tenant['first_name'] . ' ' . $tenant['last_name']); ?></h5>
                <p class="mb-1">
                    <?php echo $tenant['is_active'] ? statusBadge('active') : statusBadge('expired'); ?>
                </p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Phone</span>
                    <span><?php echo e($tenant['phone']); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Email</span>
                    <span><?php echo e($tenant['email']); ?></span>
                </li>
                <?php if (!empty($tenant['id_number'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">ID Number</span>
                    <span><?php echo e($tenant['id_number']); ?></span>
                </li>
                <?php endif; ?>
                <?php if (!empty($tenant['date_of_birth'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Date of Birth</span>
                    <span><?php echo formatDate($tenant['date_of_birth']); ?></span>
                </li>
                <?php endif; ?>
                <?php if (!empty($tenant['occupation'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Occupation</span>
                    <span><?php echo e($tenant['occupation']); ?></span>
                </li>
                <?php endif; ?>
                <?php if (!empty($tenant['employer'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Employer</span>
                    <span><?php echo e($tenant['employer']); ?></span>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Emergency Contact -->
        <?php if (!empty($tenant['emergency_contact_name']) || !empty($tenant['emergency_contact_phone'])): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="bi bi-telephone-fill me-2"></i>Emergency Contact</h6>
            </div>
            <ul class="list-group list-group-flush">
                <?php if (!empty($tenant['emergency_contact_name'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Name</span>
                    <span><?php echo e($tenant['emergency_contact_name']); ?></span>
                </li>
                <?php endif; ?>
                <?php if (!empty($tenant['emergency_contact_phone'])): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Phone</span>
                    <span><?php echo e($tenant['emergency_contact_phone']); ?></span>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Notes -->
        <?php if (!empty($tenant['notes'])): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="bi bi-sticky me-2"></i>Notes</h6>
            </div>
            <div class="card-body">
                <p class="mb-0"><?php echo nl2br(e($tenant['notes'])); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tenant['is_active']): ?>
            <div class="card mt-3">
                <div class="card-footer">
                    <form method="POST" action="?page=tenants&action=delete&id=<?php echo $tenant['id']; ?>" onsubmit="return confirm('Are you sure you want to deactivate this tenant?');">
                        <?php echo csrfField(); ?>
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="bi bi-person-x me-1"></i> Deactivate Tenant
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Lease & Payments -->
    <div class="col-lg-8">
        <!-- Active Lease -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2"></i>Active Lease</h5>
            </div>
            <div class="card-body">
                <?php if ($activeLease): ?>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <span class="text-muted d-block small">Property</span>
                            <strong><?php echo e($activeLease['property_name']); ?></strong>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <span class="text-muted d-block small">Unit</span>
                            <strong><?php echo e($activeLease['unit_number']); ?></strong>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <span class="text-muted d-block small">Monthly Rent</span>
                            <strong><?php echo formatCurrency($activeLease['monthly_rent']); ?></strong>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <span class="text-muted d-block small">Security Deposit</span>
                            <strong><?php echo formatCurrency($activeLease['security_deposit']); ?></strong>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <span class="text-muted d-block small">Start Date</span>
                            <strong><?php echo formatDate($activeLease['start_date']); ?></strong>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <span class="text-muted d-block small">End Date</span>
                            <strong><?php echo formatDate($activeLease['end_date']); ?></strong>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <span class="text-muted d-block small">Status</span>
                            <?php echo statusBadge($activeLease['lease_status']); ?>
                        </div>
                        <?php if (!empty($activeLease['property_address'])): ?>
                            <div class="col-sm-6 mb-3">
                                <span class="text-muted d-block small">Address</span>
                                <strong><?php echo e($activeLease['property_address']); ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="?page=leases&action=show&id=<?php echo $activeLease['id']; ?>" class="btn btn-sm btn-outline-primary">
                        View Full Lease Details
                    </a>
                <?php else: ?>
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-file-earmark-x fs-3"></i>
                        <p class="mt-2 mb-0">No active lease found for this tenant.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-cash-stack me-2"></i>Payment History</h5>
                <a href="?page=payments&action=tenant_history&id=<?php echo $tenant['id']; ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($payments)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-cash fs-3"></i>
                        <p class="mt-2 mb-0">No payments recorded yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Property / Unit</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?php echo formatDate($payment['payment_date']); ?></td>
                                        <td><?php echo e($payment['property_name']); ?> - <?php echo e($payment['unit_number']); ?></td>
                                        <td><?php echo formatCurrency($payment['amount']); ?></td>
                                        <td><?php echo e(ucfirst($payment['payment_method'] ?? 'N/A')); ?></td>
                                        <td><?php echo statusBadge($payment['status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
