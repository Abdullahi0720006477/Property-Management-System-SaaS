<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Expenses', 'url' => '?page=expenses'], ['label' => 'Add Expense', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add Expense</h4>
    <a href="?page=expenses" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Expenses
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=expenses&action=create" enctype="multipart/form-data">
            <?php echo csrfField(); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="property_id" class="form-label">Property <span class="text-danger">*</span></label>
                    <select class="form-select" id="property_id" name="property_id" required>
                        <option value="">Select Property</option>
                        <?php foreach ($properties as $prop): ?>
                            <option value="<?php echo $prop['id']; ?>" <?php echo postData('property_id') == $prop['id'] ? 'selected' : ''; ?>>
                                <?php echo e($prop['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="maintenance" <?php echo postData('category') === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        <option value="utilities" <?php echo postData('category') === 'utilities' ? 'selected' : ''; ?>>Utilities</option>
                        <option value="insurance" <?php echo postData('category') === 'insurance' ? 'selected' : ''; ?>>Insurance</option>
                        <option value="taxes" <?php echo postData('category') === 'taxes' ? 'selected' : ''; ?>>Taxes</option>
                        <option value="management" <?php echo postData('category') === 'management' ? 'selected' : ''; ?>>Management</option>
                        <option value="other" <?php echo postData('category') === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo e(postData('description')); ?></textarea>
                </div>

                <div class="col-md-6">
                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="amount" name="amount" value="<?php echo e(postData('amount')); ?>" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?php echo e(postData('expense_date')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="receipt" class="form-label">Receipt</label>
                    <input type="file" class="form-control" id="receipt" name="receipt" accept="image/jpeg,image/png,image/gif,image/webp,application/pdf">
                    <div class="form-text">Accepted formats: JPEG, PNG, GIF, WebP, PDF</div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Record Expense</button>
                <a href="?page=expenses" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
