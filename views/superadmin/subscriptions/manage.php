<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Manage Subscription</h4>
    <a href="superadmin.php?page=subscriptions" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Subscriptions</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i><?= e($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Company Info -->
<div class="card mb-4">
    <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Company Information</h6></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Company Name</p>
                <p class="fw-semibold"><?= e($company['name']) ?></p>
            </div>
            <div class="col-md-4">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Email</p>
                <p class="fw-semibold"><?= e($company['email']) ?></p>
            </div>
            <div class="col-md-4">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Current Plan</p>
                <?php $currentPlan = SUBSCRIPTION_PLANS[$company['subscription_plan']] ?? null; ?>
                <span class="badge" style="background:<?= $currentPlan['badge_color'] ?? '#94A3B8' ?>;"><?= e($currentPlan['name'] ?? $company['subscription_plan']) ?></span>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Status</p>
                <p><?= statusBadge($company['subscription_status'] ?? 'active') ?></p>
            </div>
            <div class="col-md-4">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Start Date</p>
                <p class="fw-semibold"><?= !empty($company['subscription_start']) ? formatDate($company['subscription_start']) : '-' ?></p>
            </div>
            <div class="col-md-4">
                <p class="text-muted mb-1" style="font-size:0.8rem;">End Date</p>
                <p class="fw-semibold"><?= !empty($company['subscription_end']) ? formatDate($company['subscription_end']) : '-' ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Change Plan Form -->
<div class="card">
    <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Change Plan</h6></div>
    <div class="card-body">
        <form method="POST" action="superadmin.php?page=subscriptions&action=manage&id=<?= $company['id'] ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">New Plan</label>
                    <select name="new_plan" class="form-select" required>
                        <option value="">Select a plan...</option>
                        <?php foreach (SUBSCRIPTION_PLANS as $key => $plan): ?>
                            <option value="<?= $key ?>" <?= $company['subscription_plan'] === $key ? 'selected' : '' ?>>
                                <?= e($plan['name']) ?> - <?= formatCurrency($plan['price_monthly']) ?>/mo
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Notes</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Reason for plan change..."></textarea>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Subscription</button>
            </div>
        </form>
    </div>
</div>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
