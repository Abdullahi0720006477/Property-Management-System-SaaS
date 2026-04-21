<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Invoices</h4>
    <a href="superadmin.php?page=invoices&action=create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Create Invoice</a>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="invoices">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="paid" <?= ($_GET['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="overdue" <?= ($_GET['status'] ?? '') === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                    <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="superadmin.php?page=invoices" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Invoices Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="font-size:0.8rem;">Invoice #</th>
                        <th style="font-size:0.8rem;">Company</th>
                        <th style="font-size:0.8rem;">Amount</th>
                        <th style="font-size:0.8rem;">Status</th>
                        <th style="font-size:0.8rem;">Due Date</th>
                        <th style="font-size:0.8rem;">Paid Date</th>
                        <th style="font-size:0.8rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($inv['invoice_number']) ?></td>
                        <td>
                            <a href="superadmin.php?page=companies&action=show&id=<?= $inv['company_id'] ?>" class="text-decoration-none"><?= e($inv['company_name'] ?? 'N/A') ?></a>
                        </td>
                        <td class="fw-semibold"><?= formatCurrency($inv['total_amount'] ?? $inv['amount'] ?? 0) ?></td>
                        <td><?= statusBadge($inv['status'] ?? 'pending') ?></td>
                        <td style="font-size:0.8rem;"><?= !empty($inv['due_date']) ? formatDate($inv['due_date']) : '-' ?></td>
                        <td style="font-size:0.8rem;"><?= !empty($inv['paid_date']) ? formatDate($inv['paid_date']) : '-' ?></td>
                        <td>
                            <a href="superadmin.php?page=invoices&action=show&id=<?= $inv['id'] ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($invoices)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No invoices found</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
