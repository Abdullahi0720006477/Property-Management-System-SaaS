<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<h4 class="fw-bold mb-4">Dashboard</h4>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.8rem;">Total Companies</p>
                        <h3 class="fw-bold mb-0"><?= $totalCompanies ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(59,130,246,0.1);color:var(--color-primary);">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
                <div style="font-size:0.75rem;color:var(--color-text-secondary);margin-top:0.5rem;">
                    <span class="text-success"><?= $activeCount ?> active</span> &middot;
                    <span class="text-warning"><?= $trialCount ?> trial</span> &middot;
                    <span class="text-danger"><?= $cancelledCount ?> cancelled</span>
                </div>
            </div>
        </div>
    </div>
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
                        <p class="text-muted mb-1" style="font-size:0.8rem;">New This Month</p>
                        <h3 class="fw-bold mb-0"><?= $newThisMonth ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(245,158,11,0.1);color:#F59E0B;">
                        <i class="bi bi-person-plus"></i>
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
                        <p class="text-muted mb-1" style="font-size:0.8rem;">Open Tickets</p>
                        <h3 class="fw-bold mb-0"><?= $openTickets ?></h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(239,68,68,0.1);color:#EF4444;">
                        <i class="bi bi-headset"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Two Columns -->
<div class="row g-3">
    <!-- Plan Distribution -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-transparent"><h6 class="fw-bold mb-0">Plan Distribution</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th style="font-size:0.8rem;">Plan</th><th style="font-size:0.8rem;" class="text-end">Companies</th></tr></thead>
                    <tbody>
                    <?php foreach (SUBSCRIPTION_PLANS as $key => $plan): ?>
                        <tr>
                            <td><span class="badge" style="background:<?= $plan['badge_color'] ?>;"><?= e($plan['name']) ?></span></td>
                            <td class="text-end fw-semibold"><?= $planDistribution[$key] ?? 0 ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Signups -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Recent Signups</h6>
                <a href="superadmin.php?page=companies" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead><tr>
                            <th style="font-size:0.8rem;">Company</th>
                            <th style="font-size:0.8rem;">Plan</th>
                            <th style="font-size:0.8rem;">Properties</th>
                            <th style="font-size:0.8rem;">Users</th>
                            <th style="font-size:0.8rem;">Joined</th>
                        </tr></thead>
                        <tbody>
                        <?php foreach ($recentCompanies as $c): ?>
                            <tr>
                                <td>
                                    <a href="superadmin.php?page=companies&action=show&id=<?= $c['id'] ?>" class="fw-semibold text-decoration-none"><?= e($c['name']) ?></a>
                                    <div style="font-size:0.75rem;color:var(--color-text-secondary);"><?= e($c['email']) ?></div>
                                </td>
                                <td><?php $p = SUBSCRIPTION_PLANS[$c['subscription_plan']] ?? null; ?>
                                    <span class="badge" style="background:<?= $p['badge_color'] ?? '#94A3B8' ?>;"><?= e($p['name'] ?? $c['subscription_plan']) ?></span>
                                </td>
                                <td><?= $c['property_count'] ?? 0 ?></td>
                                <td><?= $c['user_count'] ?? 0 ?></td>
                                <td style="font-size:0.8rem;"><?= formatDate($c['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentCompanies)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">No companies yet</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
