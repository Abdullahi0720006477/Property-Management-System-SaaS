<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<h4 class="fw-bold mb-4">Reports</h4>

<!-- Top Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.8rem;">Monthly Recurring Revenue</p>
                        <h3 class="fw-bold mb-0"><?= formatCurrency($mrr) ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(16,185,129,0.1);color:#10B981;">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.8rem;">Total Revenue</p>
                        <h3 class="fw-bold mb-0"><?= formatCurrency($totalRevenue) ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(59,130,246,0.1);color:var(--color-primary);">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.8rem;">Total Companies</p>
                        <h3 class="fw-bold mb-0"><?= $totalCompanies ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(139,92,246,0.1);color:#8B5CF6;">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.8rem;">Active Companies</p>
                        <h3 class="fw-bold mb-0"><?= $statusCounts['active'] ?? 0 ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(245,158,11,0.1);color:#F59E0B;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Plan Distribution -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Plan Distribution</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th style="font-size:0.8rem;">Plan</th>
                            <th style="font-size:0.8rem;" class="text-end">Companies</th>
                            <th style="font-size:0.8rem;" class="text-end">MRR</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach (SUBSCRIPTION_PLANS as $key => $plan): ?>
                        <tr>
                            <td><span class="badge" style="background:<?= $plan['badge_color'] ?>;"><?= e($plan['name']) ?></span></td>
                            <td class="text-end fw-semibold"><?= $planDistribution[$key] ?? 0 ?></td>
                            <td class="text-end fw-semibold">
                                <?php
                                $planMrr = ($key !== 'trial') ? ($planDistribution[$key] ?? 0) * $plan['price_monthly'] : 0;
                                echo formatCurrency($planMrr);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Company Status Breakdown -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Status Breakdown</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th style="font-size:0.8rem;">Status</th><th style="font-size:0.8rem;" class="text-end">Count</th></tr></thead>
                    <tbody>
                    <?php
                    $statusLabels = ['active' => 'Active', 'trial' => 'Trial', 'cancelled' => 'Cancelled', 'expired' => 'Expired'];
                    foreach ($statusLabels as $sKey => $sLabel):
                    ?>
                        <tr>
                            <td><?= statusBadge($sKey) ?></td>
                            <td class="text-end fw-semibold"><?= $statusCounts[$sKey] ?? 0 ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue (last 6 months) -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Revenue by Month</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th style="font-size:0.8rem;">Month</th><th style="font-size:0.8rem;" class="text-end">Revenue</th></tr></thead>
                    <tbody>
                    <?php foreach ($monthlyRevenue as $mr): ?>
                        <tr>
                            <td style="font-size:0.85rem;"><?= date('M Y', strtotime($mr['month'] . '-01')) ?></td>
                            <td class="text-end fw-semibold"><?= formatCurrency($mr['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($monthlyRevenue)): ?>
                        <tr><td colspan="2" class="text-center text-muted py-3">No revenue data yet</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
