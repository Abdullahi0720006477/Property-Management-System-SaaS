<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Manager Dashboard</h4>
    <span class="text-muted"><?php echo date(DISPLAY_DATE_FORMAT); ?></span>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3"><i class="bi bi-buildings"></i></div>
                <div>
                    <div class="stat-value"><?php echo $totalProperties; ?></div>
                    <div class="stat-label">My Properties</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3"><i class="bi bi-door-open"></i></div>
                <div>
                    <div class="stat-value"><?php echo $occupancyRate; ?>%</div>
                    <div class="stat-label">Occupancy (<?php echo $occupiedUnits; ?>/<?php echo $totalUnits; ?>)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3"><i class="bi bi-tools"></i></div>
                <div>
                    <div class="stat-value"><?php echo $maintenanceOpen; ?></div>
                    <div class="stat-label">Open Maintenance</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Properties -->
<div class="card mb-4">
    <div class="card-header">My Properties</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Property</th><th>City</th><th>Units</th><th>Occupied</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($properties as $p): ?>
                        <tr>
                            <td><?php echo e($p['name']); ?></td>
                            <td><?php echo e($p['city']); ?></td>
                            <td><?php echo $p['unit_count']; ?></td>
                            <td><?php echo $p['occupied_count']; ?></td>
                            <td><a href="?page=properties&action=show&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Maintenance -->
<div class="card">
    <div class="card-header">Recent Maintenance Requests</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Title</th><th>Unit</th><th>Priority</th><th>Status</th><th>Date</th></tr></thead>
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
                        <tr><td colspan="5" class="text-center text-muted py-3">No requests</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
