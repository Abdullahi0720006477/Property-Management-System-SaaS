<?php $currentPage = $_GET['page'] ?? 'dashboard'; ?>
<!-- Sidebar Backdrop for Mobile -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="?page=dashboard" class="sidebar-brand">
            <img src="assets/img/logo-mark-white.svg" alt="BC" style="height:24px;" class="sidebar-brand-icon">
            <img src="assets/img/logo-full-white.svg" alt="BizConnect" class="sidebar-brand-text" style="height:24px;">
        </a>
        <button class="sidebar-collapse-btn d-none d-lg-flex" id="sidebarCollapseBtn" title="Collapse sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>
    <div class="nav-section-title" style="padding-top:0.5rem;"><?= e($_SESSION['company_name'] ?? '') ?></div>

    <div class="sidebar-nav">
        <div class="nav-section-title">MAIN</div>
        <a href="?page=dashboard" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i>
            <span class="nav-text">Dashboard</span>
        </a>

        <?php if (!isTenant()): ?>
        <a href="?page=calendar" class="nav-link <?= $currentPage === 'calendar' ? 'active' : '' ?>">
            <i class="bi bi-calendar3"></i>
            <span class="nav-text">Calendar</span>
        </a>

        <div class="nav-section-title">MANAGEMENT</div>
        <a href="?page=properties" class="nav-link <?= $currentPage === 'properties' ? 'active' : '' ?>">
            <i class="bi bi-building"></i>
            <span class="nav-text">Properties</span>
        </a>
        <a href="?page=units" class="nav-link <?= $currentPage === 'units' ? 'active' : '' ?>">
            <i class="bi bi-door-open"></i>
            <span class="nav-text">Units</span>
        </a>
        <a href="?page=tenants" class="nav-link <?= $currentPage === 'tenants' ? 'active' : '' ?>">
            <i class="bi bi-people"></i>
            <span class="nav-text">Tenants</span>
        </a>
        <a href="?page=leases" class="nav-link <?= $currentPage === 'leases' ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text"></i>
            <span class="nav-text">Leases</span>
        </a>

        <?php if (isAdmin() || isManager() || hasRole('accountant')): ?>
        <div class="nav-section-title">FINANCE</div>
        <a href="?page=payments" class="nav-link <?= $currentPage === 'payments' ? 'active' : '' ?>">
            <i class="bi bi-credit-card"></i>
            <span class="nav-text">Payments</span>
        </a>
        <a href="?page=expenses" class="nav-link <?= $currentPage === 'expenses' ? 'active' : '' ?>">
            <i class="bi bi-cash-stack"></i>
            <span class="nav-text">Expenses</span>
        </a>
        <a href="?page=reports" class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>">
            <i class="bi bi-graph-up"></i>
            <span class="nav-text">Reports</span>
        </a>
        <?php endif; ?>

        <div class="nav-section-title">OPERATIONS</div>
        <a href="?page=maintenance" class="nav-link <?= $currentPage === 'maintenance' ? 'active' : '' ?>">
            <i class="bi bi-tools"></i>
            <span class="nav-text">Maintenance</span>
        </a>
        <a href="?page=documents" class="nav-link <?= $currentPage === 'documents' ? 'active' : '' ?>">
            <i class="bi bi-folder"></i>
            <span class="nav-text">Documents</span>
        </a>

        <?php else: /* Tenant navigation */ ?>
        <div class="nav-section-title">MY TENANCY</div>
        <a href="?page=payments&action=tenant_history&id=<?= $_SESSION['tenant_profile_id'] ?? '' ?>" class="nav-link <?= $currentPage === 'payments' ? 'active' : '' ?>">
            <i class="bi bi-credit-card"></i>
            <span class="nav-text">My Payments</span>
        </a>
        <a href="?page=maintenance&action=my_requests" class="nav-link <?= $currentPage === 'maintenance' ? 'active' : '' ?>">
            <i class="bi bi-tools"></i>
            <span class="nav-text">Maintenance</span>
        </a>
        <?php endif; ?>

        <div class="nav-section-title">SYSTEM</div>
        <?php if (isAdmin()): ?>
        <a href="?page=staff" class="nav-link <?= $currentPage === 'staff' ? 'active' : '' ?>">
            <i class="bi bi-person-gear"></i>
            <span class="nav-text">Staff</span>
        </a>
        <?php endif; ?>
        <?php if (!isTenant()): ?>
        <a href="?page=settings" class="nav-link <?= $currentPage === 'settings' ? 'active' : '' ?>">
            <i class="bi bi-gear"></i>
            <span class="nav-text">Settings</span>
        </a>
        <?php endif; ?>
        <?php if (isAdmin()): ?>
        <a href="?page=billing" class="nav-link <?= $currentPage === 'billing' ? 'active' : '' ?>">
            <i class="bi bi-receipt"></i>
            <span class="nav-text">Billing</span>
        </a>
        <?php endif; ?>
        <a href="?page=support" class="nav-link <?= $currentPage === 'support' ? 'active' : '' ?>">
            <i class="bi bi-question-circle"></i>
            <span class="nav-text">Support</span>
        </a>
        <?php if (isAdmin()): ?>
        <a href="?page=activity" class="nav-link <?= $currentPage === 'activity' ? 'active' : '' ?>">
            <i class="bi bi-clock-history"></i>
            <span class="nav-text">Activity Log</span>
        </a>
        <?php endif; ?>
        <a href="?page=notifications" class="nav-link <?= $currentPage === 'notifications' ? 'active' : '' ?>">
            <i class="bi bi-bell"></i>
            <span class="nav-text">Notifications</span>
        </a>
    </div>

    <?php
    $planColors = ['trial' => '#94A3B8', 'starter' => '#3B82F6', 'professional' => '#F59E0B', 'enterprise' => '#8B5CF6'];
    $plan = $_SESSION['company_plan'] ?? 'trial';
    ?>
    <div style="padding:0.5rem var(--space-lg);"><span class="badge" style="background:<?= $planColors[$plan] ?? '#94A3B8' ?>;color:#fff;font-size:0.65rem;padding:0.25rem 0.5rem;border-radius:var(--radius-full);"><?= ucfirst($plan) ?> Plan</span></div>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <?php if (!empty($_SESSION['user_avatar'])): ?>
            <img src="<?= uploadUrl($_SESSION['user_avatar']) ?>" class="sidebar-user-avatar" alt="Avatar">
            <?php else: ?>
            <div class="sidebar-user-avatar-placeholder"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></div>
            <?php endif; ?>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?= e($_SESSION['user_name'] ?? '') ?></span>
                <span class="sidebar-user-role"><?= isTenant() ? 'Tenant' : ucfirst(str_replace('_', ' ', $_SESSION['user_role'] ?? '')) ?></span>
            </div>
        </div>
    </div>
</nav>
