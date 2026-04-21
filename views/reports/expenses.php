<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Expense Report</h4>
        <small class="text-muted">Expense breakdown by property and category</small>
    </div>
    <div>
        <a href="?page=reports&action=export_csv&type=expenses&start_date=<?php echo e($startDate); ?>&end_date=<?php echo e($endDate); ?>" class="btn btn-outline-success me-2">
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
            <input type="hidden" name="action" value="expenses">
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
    <!-- Expenses by Property Table -->
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header">Expenses by Property</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Property</th>
                                <th class="text-end">Total Expenses</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($expensesByProperty)): ?>
                                <?php foreach ($expensesByProperty as $row): ?>
                                    <tr>
                                        <td><?php echo e($row['property_name']); ?></td>
                                        <td class="text-end"><?php echo formatCurrency((float) $row['total_expense']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">No expense data found for the selected period.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>Total</th>
                                <th class="text-end"><?php echo formatCurrency($totalExpenses); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Expenses by Category Table -->
        <div class="card mt-4">
            <div class="card-header">Expenses by Category</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($expensesByCategory)): ?>
                                <?php foreach ($expensesByCategory as $row): ?>
                                    <tr>
                                        <td><?php echo e(ucwords(str_replace('_', ' ', $row['category']))); ?></td>
                                        <td class="text-end"><?php echo formatCurrency((float) $row['total']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">No category data found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Pie Chart -->
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header">Expenses by Category</div>
            <div class="card-body">
                <canvas id="categoryPieChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryData = <?php echo json_encode($expensesByCategory); ?>;
    if (categoryData.length > 0) {
        const pieColors = [
            'rgba(220, 53, 69, 0.8)', 'rgba(0, 123, 255, 0.8)', 'rgba(255, 193, 7, 0.8)',
            'rgba(40, 167, 69, 0.8)', 'rgba(23, 162, 184, 0.8)', 'rgba(111, 66, 193, 0.8)',
            'rgba(253, 126, 20, 0.8)', 'rgba(32, 201, 151, 0.8)', 'rgba(108, 117, 125, 0.8)',
            'rgba(255, 99, 132, 0.8)'
        ];

        new Chart(document.getElementById('categoryPieChart'), {
            type: 'pie',
            data: {
                labels: categoryData.map(r => r.category.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())),
                datasets: [{
                    data: categoryData.map(r => parseFloat(r.total)),
                    backgroundColor: pieColors.slice(0, categoryData.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = ((ctx.raw / total) * 100).toFixed(1);
                                return ctx.label + ': <?php echo CURRENCY_SYMBOL; ?> ' + ctx.raw.toLocaleString() + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
