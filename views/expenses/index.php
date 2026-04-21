<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Expenses', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Expenses</h4>
    <a href="?page=expenses&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Expense
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="expenses">
            <div class="col-md-3">
                <label for="property_id" class="form-label">Property</label>
                <select class="form-select" id="property_id" name="property_id">
                    <option value="">All Properties</option>
                    <?php foreach ($properties as $prop): ?>
                        <option value="<?php echo $prop['id']; ?>" <?php echo ($propertyId ?? '') == $prop['id'] ? 'selected' : ''; ?>>
                            <?php echo e($prop['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    <option value="maintenance" <?php echo $category === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    <option value="utilities" <?php echo $category === 'utilities' ? 'selected' : ''; ?>>Utilities</option>
                    <option value="insurance" <?php echo $category === 'insurance' ? 'selected' : ''; ?>>Insurance</option>
                    <option value="taxes" <?php echo $category === 'taxes' ? 'selected' : ''; ?>>Taxes</option>
                    <option value="management" <?php echo $category === 'management' ? 'selected' : ''; ?>>Management</option>
                    <option value="other" <?php echo $category === 'other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo e($startDate); ?>">
            </div>
            <div class="col-md-2">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo e($endDate); ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="?page=expenses" class="btn btn-outline-secondary ms-1">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Expenses Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Property</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Recorded By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($expenses)): ?>
                        <?php $total = 0; ?>
                        <?php foreach ($expenses as $expense): ?>
                            <?php $total += (float) $expense['amount']; ?>
                            <tr>
                                <td><?php echo e($expense['property_name']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo e(ucfirst($expense['category'])); ?></span></td>
                                <td><?php echo e($expense['description']); ?></td>
                                <td><?php echo formatCurrency((float) $expense['amount']); ?></td>
                                <td><?php echo formatDate($expense['expense_date']); ?></td>
                                <td><?php echo e($expense['recorded_by_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <form method="POST" action="?page=expenses&action=delete&id=<?php echo $expense['id']; ?>" class="d-inline delete-form">
                                        <?php echo csrfField(); ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm" title="Delete"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                data-item-name="<?php echo e($expense['description']); ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No expenses found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($expenses)): ?>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="3" class="text-end">Total:</td>
                            <td><?php echo formatCurrency($total); ?></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php
    $baseUrl = '?page=expenses&property_id=' . urlencode($propertyId ?? '') . '&category=' . urlencode($category) . '&start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate);
    echo paginate($totalRecords, $currentPage, RECORDS_PER_PAGE, $baseUrl);
?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
