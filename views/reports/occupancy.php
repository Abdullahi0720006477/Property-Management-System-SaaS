<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Occupancy Report</h4>
        <small class="text-muted">Occupancy rates across all properties</small>
    </div>
    <div>
        <a href="?page=reports&action=export_csv&type=occupancy" class="btn btn-outline-success me-2">
            <i class="bi bi-download me-1"></i> Export CSV
        </a>
        <a href="?page=reports" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Overall Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="stat-value text-primary"><?php echo count($properties); ?></div>
                <div class="stat-label text-muted">Total Properties</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="stat-value"><?php echo $totalUnitsAll; ?></div>
                <div class="stat-label text-muted">Total Units</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="stat-value text-success"><?php echo $totalOccupiedAll; ?></div>
                <div class="stat-label text-muted">Occupied Units</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="stat-value text-info"><?php echo $overallOccupancy; ?>%</div>
                <div class="stat-label text-muted">Overall Occupancy</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Occupancy Table -->
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header">Occupancy by Property</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Property</th>
                                <th class="text-center">Total Units</th>
                                <th class="text-center">Occupied</th>
                                <th class="text-center">Vacant</th>
                                <th class="text-center">Occupancy</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($properties)): ?>
                                <?php foreach ($properties as $p):
                                    $unitCount = (int) $p['unit_count'];
                                    $occupied  = (int) $p['occupied_count'];
                                    $vacant    = $unitCount - $occupied;
                                    $pct       = $unitCount > 0 ? round(($occupied / $unitCount) * 100, 1) : 0;
                                ?>
                                    <tr>
                                        <td><?php echo e($p['name']); ?></td>
                                        <td class="text-center"><?php echo $unitCount; ?></td>
                                        <td class="text-center text-success"><?php echo $occupied; ?></td>
                                        <td class="text-center text-danger"><?php echo $vacant; ?></td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="progress me-2" style="height: 6px; width: 60px;">
                                                    <div class="progress-bar <?php echo $pct >= 75 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger'); ?>"
                                                         style="width: <?php echo $pct; ?>%"></div>
                                                </div>
                                                <small><?php echo $pct; ?>%</small>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No properties found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Occupancy Doughnut Chart -->
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header">Overall Occupancy</div>
            <div class="card-body">
                <canvas id="occupancyDoughnut"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const occupied = <?php echo $totalOccupiedAll; ?>;
    const vacant = <?php echo $totalUnitsAll - $totalOccupiedAll; ?>;

    new Chart(document.getElementById('occupancyDoughnut'), {
        type: 'doughnut',
        data: {
            labels: ['Occupied', 'Vacant'],
            datasets: [{
                data: [occupied, vacant],
                backgroundColor: ['rgba(40, 167, 69, 0.8)', 'rgba(220, 53, 69, 0.5)'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            const total = occupied + vacant;
                            const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                            return ctx.label + ': ' + ctx.raw + ' units (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
