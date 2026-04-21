<?php
require_once SRC_PATH . '/models/User.php';
$userModel_nav = new User();
$unreadCount = $userModel_nav->getUnreadNotificationCount(currentUserId());
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? 'Dashboard'); ?> | BizConnect</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">

    <!-- PWA Meta -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#1B2A4A">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Vendor CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Design System (before app styles) -->
    <link href="assets/css/design-system.css" rel="stylesheet">
    <link href="assets/css/chatbot.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Page Loader -->
    <div id="pageLoader" class="page-loader"><div class="page-loader-bar"></div></div>

    <!-- Sidebar -->
    <?php require_once VIEWS_PATH . '/layouts/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Header -->
        <div class="top-header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link d-lg-none text-dark p-0" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Global Search -->
                <div class="global-search-wrapper d-none d-md-block">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="globalSearch" placeholder="Search properties, tenants, units..." autocomplete="off">
                    <div id="searchResults" class="d-none"></div>
                </div>
                <!-- Dark Mode Toggle -->
                <button id="themeToggle" class="btn btn-link p-0" title="Toggle dark mode">
                    <i class="bi bi-moon-fill"></i>
                </button>
                <!-- Notifications -->
                <a href="?page=notifications" class="btn btn-link p-0 position-relative">
                    <i class="bi bi-bell fs-5"></i>
                    <?php if ($unreadCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>
                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link p-0 d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="dropdown">
                        <?php if (!empty($_SESSION['user_avatar'])): ?>
                        <img src="<?= uploadUrl($_SESSION['user_avatar']) ?>" class="rounded-circle" width="32" height="32" style="object-fit:cover;">
                        <?php else: ?>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:var(--color-primary);color:white;font-size:0.75rem;"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></div>
                        <?php endif; ?>
                        <span class="d-none d-md-inline" style="color:var(--color-text);font-size:0.85rem;"><?= e($_SESSION['user_name'] ?? '') ?></span>
                        <i class="bi bi-chevron-down" style="font-size:0.7rem;color:var(--color-text-muted);"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="?page=profile"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="?page=auth&action=logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Flash Messages as Toasts -->
        <?php
        $flash = getFlashMessages();
        if (!empty($flash)):
            foreach ($flash as $type => $msg):
                $toastType = match($type) { 'error' => 'error', 'danger' => 'error', 'success' => 'success', 'warning' => 'warning', default => 'info' };
        ?>
        <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode(is_string($msg) ? $msg : '') ?>, '<?= $toastType ?>'));</script>
        <?php endforeach; endif; ?>

        <!-- Page Content -->
        <div class="px-4 pb-4 pt-3">
