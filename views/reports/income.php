<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Income Report</h4>
        <small class="text-muted">Revenue breakdown by property</small>
    </div>
    <div>
        <a href="?page=reports&action=export_csv&type=income&start_date=<?php echo e($startDate); ?>&end_date=<?php echo e($endDate); ?>" class="btn btn-outline-success me-2">
            <i class="bi bi-download me-1"></i> Export CSV
        </a>
        <a href="?page=reports" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="reports">
            <input type="hidden" name="action" value="income">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo e($startDate); ?>">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo e($endDate); ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Income by Property Table -->
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header">
                <span>Income by Property</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Property</th>
                                <th class="text-end">Total Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($incomeByProperty)): ?>
                                <?php foreach ($incomeByProperty as $row): ?>
                                    <tr>
                                        <td><?php echo e($row['property_name']); ?></td>
                                        <td class="text-end"><?php echo formatCurrency((float) $row['total_income']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">No income data found for the selected period.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>Total</th>
                                <th class="text-end"><?php echo formatCurrency($totalIncome); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Income Bar Chart -->
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header">Income by Property</div>
            <div class="card-body">
                <canvas id="incomeBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Revenue Line Chart -->
<div class="card mt-4">
    <div class="card-header">Monthly Revenue (Last 12 Months)</div>
    <div class="card-body">
        <canvas id="monthlyRevenueChart" height="80"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Income by Property Bar Chart
    const incomeData = <?php echo json_encode($incomeByProperty); ?>;
    if (incomeData.length > 0) {
        const barLabels = incomeData.map(r => r.property_name);
        const barValues = incomeData.map(r => parseFloat(r.total_income));
        const barColors = [
            'rgba(40, 167, 69, 0.7)', 'rgba(0, 123, 255, 0.7)', 'rgba(255, 193, 7, 0.7)',
            'rgba(220, 53, 69, 0.7)', 'rgba(23, 162, 184, 0.7)', 'rgba(111, 66, 193, 0.7)',
            'rgba(253, 126, 20, 0.7)', 'rgba(32, 201, 151, 0.7)'
        ];

        new Chart(document.getElementById('incomeBarChart'), {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{
                    label: 'Income (<?php echo CURRENCY_CODE; ?>)',
                    data: barValues,
                    backgroundColor: barColors.slice(0, barLabels.length),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => '<?php echo CURRENCY_SYMBOL; ?> ' + v.toLocaleString() } },
                    x: { ticks: { maxRotation: 45 } }
                }
            }
        });
    }

    // Monthly Revenue Line Chart
    const monthlyData = <?php echo json_encode($monthlyRevenue); ?>;
    if (monthlyData.length > 0) {
        new Chart(document.getElementById('monthlyRevenueChart'), {
            type: 'line',
            data: {
                labels: monthlyData.map(r => r.month),
                datasets: [{
                    label: 'Revenue (<?php echo CURRENCY_CODE; ?>)',
                    data: monthlyData.map(r => parseFloat(r.total)),
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(40, 167, 69, 1)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => '<?php echo CURRENCY_SYMBOL; ?> ' + v.toLocaleString() } }
                },
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
