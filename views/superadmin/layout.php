<?php $saPage = $_GET['page'] ?? 'dashboard'; ?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> | BizConnect Admin</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/design-system.css" rel="stylesheet">
</head>
<body>
<!-- SA Sidebar -->
<nav class="sidebar" id="sidebar" style="width:240px;">
    <div class="sidebar-header">
        <a href="superadmin.php" class="sidebar-brand">
            <img src="assets/img/logo-mark-white.svg" style="height:24px;">
            <span class="sidebar-brand-text" style="font-size:0.9rem;">BizConnect Admin</span>
        </a>
    </div>
    <div class="sidebar-nav">
        <a href="superadmin.php?page=dashboard" class="nav-link <?= $saPage==='dashboard'?'active':'' ?>"><i class="bi bi-grid-1x2"></i><span class="nav-text">Dashboard</span></a>
        <a href="superadmin.php?page=companies" class="nav-link <?= $saPage==='companies'?'active':'' ?>"><i class="bi bi-building"></i><span class="nav-text">Companies</span></a>
        <a href="superadmin.php?page=subscriptions" class="nav-link <?= $saPage==='subscriptions'?'active':'' ?>"><i class="bi bi-credit-card"></i><span class="nav-text">Subscriptions</span></a>
        <a href="superadmin.php?page=invoices" class="nav-link <?= $saPage==='invoices'?'active':'' ?>"><i class="bi bi-receipt"></i><span class="nav-text">Invoices</span></a>
        <a href="superadmin.php?page=support" class="nav-link <?= $saPage==='support'?'active':'' ?>"><i class="bi bi-headset"></i><span class="nav-text">Support</span></a>
        <a href="superadmin.php?page=announcements" class="nav-link <?= $saPage==='announcements'?'active':'' ?>"><i class="bi bi-megaphone"></i><span class="nav-text">Announcements</span></a>
        <a href="superadmin.php?page=reports" class="nav-link <?= $saPage==='reports'?'active':'' ?>"><i class="bi bi-graph-up"></i><span class="nav-text">Reports</span></a>
        <div style="margin-top:auto;padding:1rem;">
            <a href="superadmin.php?page=auth&action=logout" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i><span class="nav-text">Logout</span></a>
        </div>
    </div>
</nav>
<div class="main-content" style="margin-left:240px;">
    <div class="top-header">
        <div></div>
        <div class="d-flex align-items-center gap-2">
            <span style="font-size:0.85rem;color:var(--color-text-secondary);"><?= e($_SESSION['sa_name'] ?? 'Admin') ?></span>
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:var(--color-primary);color:white;font-size:0.75rem;">SA</div>
        </div>
    </div>
    <div class="px-4 pb-4 pt-3">
