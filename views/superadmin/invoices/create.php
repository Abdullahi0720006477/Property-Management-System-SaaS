<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Create Invoice</h4>
    <a href="superadmin.php?page=invoices" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Invoices</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="superadmin.php?page=invoices&action=create">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Company</label>
                    <select name="company_id" class="form-select" required>
                        <option value="">Select a company...</option>
                        <?php foreach ($companies as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Amount</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0" required placeholder="0.00">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Tax</label>
                    <input type="number" name="tax" class="form-control" step="0.01" min="0" value="0" placeholder="0.00">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Due Date</label>
                    <input type="date" name="due_date" class="form-control" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Notes</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Invoice notes or description..."></textarea>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Invoice</button>
            </div>
        </form>
    </div>
</div>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
