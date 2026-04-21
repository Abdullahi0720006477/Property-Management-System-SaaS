<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Admin Dashboard</h4>
    <span class="text-muted"><?php echo date(DISPLAY_DATE_FORMAT); ?></span>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3"><i class="bi bi-buildings"></i></div>
                <div>
                    <div class="stat-value"><?php echo $totalProperties; ?></div>
                    <div class="stat-label">Properties</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3"><i class="bi bi-door-open"></i></div>
                <div>
                    <div class="stat-value"><?php echo $occupancyRate; ?>%</div>
                    <div class="stat-label">Occupancy Rate (<?php echo $occupiedUnits; ?>/<?php echo $totalUnits; ?>)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="stat-value"><?php echo formatCurrency($monthlyRevenue); ?></div>
                    <div class="stat-label">Revenue This Month</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="stat-value"><?php echo $overdueCount; ?></div>
                    <div class="stat-label">Overdue Payments</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Revenue Chart -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Revenue (Last 12 Months)</span>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">Quick Stats</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Vacant Units</span>
                    <strong><?php echo $vacantUnits; ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Open Maintenance</span>
                    <strong class="text-warning"><?php echo $maintenanceOpen; ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Active Leases</span>
                    <strong><?php echo $leaseModel->getActiveCount(); ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Expiring Leases (30 days)</span>
                    <strong class="text-danger"><?php echo count($expiringLeases); ?></strong>
                </div>
            </div>
        </div>

        <?php if (!empty($expiringLeases)): ?>
        <div class="card mt-3">
            <div class="card-header text-danger">Expiring Leases</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($expiringLeases, 0, 5) as $lease): ?>
                        <a href="?page=leases&action=show&id=<?php echo $lease['id']; ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <strong><?php echo e($lease['tenant_name']); ?></strong>
                                <small class="text-danger"><?php echo formatDate($lease['end_date']); ?></small>
                            </div>
                            <small class="text-muted"><?php echo e($lease['property_name']); ?> - <?php echo e($lease['unit_number']); ?></small>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Maintenance -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Recent Maintenance Requests</span>
        <a href="?page=maintenance" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Title</th><th>Unit</th><th>Priority</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($recentMaintenance as $req): ?>
                        <tr>
                            <td><a href="?page=maintenance&action=show&id=<?php echo $req['id']; ?>"><?php echo e($req['title']); ?></a></td>
                            <td><?php echo e($req['property_name']); ?> - <?php echo e($req['unit_number']); ?></td>
                            <td><?php echo priorityBadge($req['priority']); ?></td>
                            <td><?php echo statusBadge($req['status']); ?></td>
                            <td><?php echo formatDate($req['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentMaintenance)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">No maintenance requests</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const revenueData = <?php echo json_encode($revenueData); ?>;
    const labels = revenueData.map(r => r.month);
    const data = revenueData.map(r => parseFloat(r.total));

    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (<?php echo CURRENCY_CODE; ?>)',
                data: data,
                backgroundColor: 'rgba(78, 115, 223, 0.7)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { callback: v => '<?php echo CURRENCY_SYMBOL; ?> ' + v.toLocaleString() } } },
            plugins: { legend: { display: false } }
        }
    });
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
