<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([
    ['label' => 'Dashboard', 'url' => '?page=dashboard'],
    ['label' => 'Billing', 'url' => '?page=billing'],
    ['label' => 'Invoice #' . e($invoice['invoice_number']), 'url' => '']
]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Invoice <?= e($invoice['invoice_number']); ?></h4>
    <div>
        <a href="?page=billing" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        <button onclick="window.print();" class="btn btn-primary">
            <i class="bi bi-printer me-1"></i> Print
        </button>
    </div>
</div>

<div class="card" id="invoicePrintArea">
    <div class="card-body p-4 p-md-5">
        <!-- Header: BizConnect + Company Info -->
        <div class="row mb-4">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <h3 class="fw-bold mb-1" style="color: #1B2A4A;">BizConnect</h3>
                <p class="text-muted mb-0 small">Property Management Platform</p>
                <p class="text-muted mb-0 small">Nairobi, Kenya</p>
                <p class="text-muted mb-0 small">support@bizconnect.co.ke</p>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="fw-bold mb-1"><?= e($company['name']); ?></h6>
                <?php if (!empty($company['email'])): ?>
                    <p class="text-muted mb-0 small"><?= e($company['email']); ?></p>
                <?php endif; ?>
                <?php if (!empty($company['phone'])): ?>
                    <p class="text-muted mb-0 small"><?= e($company['phone']); ?></p>
                <?php endif; ?>
                <?php if (!empty($company['address'])): ?>
                    <p class="text-muted mb-0 small">
                        <?= e($company['address']); ?>
                        <?php if (!empty($company['city'])): ?>, <?= e($company['city']); ?><?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <hr>

        <!-- Invoice Meta -->
        <div class="row mb-4">
            <div class="col-sm-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width: 130px;">Invoice Number</td>
                        <td><strong><?= e($invoice['invoice_number']); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Issue Date</td>
                        <td><?= formatDate($invoice['created_at']); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Due Date</td>
                        <td><?= formatDate($invoice['due_date'] ?? null); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-6 text-sm-end">
                <div class="mb-2">
                    <span class="text-muted">Status:</span>
                    <?= statusBadge($invoice['status']); ?>
                </div>
                <?php if (!empty($invoice['paid_date'])): ?>
                    <div>
                        <span class="text-muted">Paid on:</span>
                        <?= formatDate($invoice['paid_date']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($invoice['payment_method'])): ?>
                    <div>
                        <span class="text-muted">Payment Method:</span>
                        <?= e(ucwords(str_replace('_', ' ', $invoice['payment_method']))); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($invoice['payment_reference'])): ?>
                    <div>
                        <span class="text-muted">Reference:</span>
                        <?= e($invoice['payment_reference']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Line Items -->
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-end" style="width: 150px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?= e($currentPlan['name']); ?> Plan - Monthly Subscription
                            <br>
                            <small class="text-muted">
                                Up to <?= $currentPlan['max_properties'] === -1 ? 'unlimited' : $currentPlan['max_properties']; ?> properties,
                                <?= $currentPlan['max_units'] === -1 ? 'unlimited' : $currentPlan['max_units']; ?> units,
                                <?= $currentPlan['max_users'] === -1 ? 'unlimited' : $currentPlan['max_users']; ?> users
                            </small>
                        </td>
                        <td class="text-end"><?= formatCurrency((float)($invoice['amount'] ?? 0)); ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <?php if (!empty($invoice['tax_amount']) && (float)$invoice['tax_amount'] > 0): ?>
                    <tr>
                        <td class="text-end"><strong>Subtotal</strong></td>
                        <td class="text-end"><?= formatCurrency((float)$invoice['amount']); ?></td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>Tax</strong></td>
                        <td class="text-end"><?= formatCurrency((float)$invoice['tax_amount']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="table-light">
                        <td class="text-end"><strong class="fs-5">Total</strong></td>
                        <td class="text-end"><strong class="fs-5"><?= formatCurrency((float)($invoice['total_amount'] ?? $invoice['amount'])); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notes -->
        <?php if (!empty($invoice['notes'])): ?>
        <div class="mt-4">
            <h6 class="text-muted">Notes</h6>
            <p class="mb-0"><?= nl2br(e($invoice['notes'])); ?></p>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="text-center mt-5 pt-4 border-top">
            <p class="text-muted small mb-0">Thank you for your business.</p>
            <p class="text-muted small mb-0">BizConnect - Property Management Platform</p>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .topbar, nav[aria-label="breadcrumb"], footer,
    .btn, .toast-container, #chatbot-widget, .d-flex.justify-content-between.align-items-center.mb-4 {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    #invoicePrintArea {
        border: none !important;
        box-shadow: none !important;
    }
    body {
        background: white !important;
    }
}
</style>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
