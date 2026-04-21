<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Invoice <?= e($invoice['invoice_number']) ?></h4>
    <a href="superadmin.php?page=invoices" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Invoices</a>
</div>

<!-- Invoice Detail -->
<div class="card mb-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Invoice Details</h6>
        <?= statusBadge($invoice['status'] ?? 'pending') ?>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Invoice Number</p>
                <p class="fw-semibold"><?= e($invoice['invoice_number']) ?></p>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Company</p>
                <p class="fw-semibold">
                    <a href="superadmin.php?page=companies&action=show&id=<?= $invoice['company_id'] ?>" class="text-decoration-none"><?= e($invoice['company_name'] ?? 'N/A') ?></a>
                </p>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Due Date</p>
                <p class="fw-semibold"><?= !empty($invoice['due_date']) ? formatDate($invoice['due_date']) : '-' ?></p>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Paid Date</p>
                <p class="fw-semibold"><?= !empty($invoice['paid_date']) ? formatDate($invoice['paid_date']) : '-' ?></p>
            </div>
        </div>
        <hr>
        <div class="row g-3">
            <div class="col-md-3">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Amount</p>
                <p class="fw-semibold"><?= formatCurrency($invoice['amount'] ?? 0) ?></p>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Tax</p>
                <p class="fw-semibold"><?= formatCurrency($invoice['tax_amount'] ?? 0) ?></p>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Total</p>
                <h5 class="fw-bold text-primary"><?= formatCurrency($invoice['total_amount'] ?? 0) ?></h5>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1" style="font-size:0.8rem;">Currency</p>
                <p class="fw-semibold"><?= e($invoice['currency'] ?? 'KES') ?></p>
            </div>
        </div>
        <?php if (!empty($invoice['notes'])): ?>
            <hr>
            <p class="text-muted mb-1" style="font-size:0.8rem;">Notes</p>
            <p class="mb-0"><?= nl2br(e($invoice['notes'])) ?></p>
        <?php endif; ?>
    </div>
</div>

<!-- Payment Recording Form (if unpaid) -->
<?php if (in_array($invoice['status'], ['pending', 'overdue'])): ?>
<div class="card">
    <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Record Payment</h6></div>
    <div class="card-body">
        <form method="POST" action="superadmin.php?page=invoices&action=pay&id=<?= $invoice['id'] ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Payment Date</label>
                    <input type="date" name="paid_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Payment Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="card">Card</option>
                        <option value="cash">Cash</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Reference</label>
                    <input type="text" name="payment_reference" class="form-control" placeholder="Transaction reference...">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Mark as Paid</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
