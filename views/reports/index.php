<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Reports', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Reports</h4>
    <span class="text-muted"><?php echo date(DISPLAY_DATE_FORMAT); ?></span>
</div>

<div class="row g-4">
    <!-- Income Report -->
    <div class="col-md-6 col-xl-3">
        <a href="?page=reports&action=income" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm report-card">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <span class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="bi bi-cash-stack fs-2"></i>
                        </span>
                    </div>
                    <h5 class="card-title text-dark">Income Report</h5>
                    <p class="card-text text-muted small">View income by property with monthly revenue trends and totals.</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Expense Report -->
    <div class="col-md-6 col-xl-3">
        <a href="?page=reports&action=expenses" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm report-card">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <span class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="bi bi-receipt fs-2"></i>
                        </span>
                    </div>
                    <h5 class="card-title text-dark">Expense Report</h5>
                    <p class="card-text text-muted small">Track expenses by property and category with visual breakdowns.</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Occupancy Report -->
    <div class="col-md-6 col-xl-3">
        <a href="?page=reports&action=occupancy" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm report-card">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <span class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="bi bi-building fs-2"></i>
                        </span>
                    </div>
                    <h5 class="card-title text-dark">Occupancy Report</h5>
                    <p class="card-text text-muted small">View occupancy rates across all properties and units.</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Overdue Payments Report -->
    <div class="col-md-6 col-xl-3">
        <a href="?page=reports&action=overdue" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm report-card">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <span class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="bi bi-exclamation-triangle fs-2"></i>
                        </span>
                    </div>
                    <h5 class="card-title text-dark">Overdue Payments</h5>
                    <p class="card-text text-muted small">List of all overdue payments with tenant details and amounts.</p>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
.report-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.report-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12) !important;
}
</style>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
