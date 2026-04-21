<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? 'Login'); ?> | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a1c2e 0%, #2d3561 100%); min-height: 100vh; }
        .auth-card { max-width: 420px; margin: auto; }
        .auth-card .card { border: none; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        .auth-logo { font-size: 2rem; font-weight: 700; color: #fff; }
        .auth-logo i { color: #4e73df; }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="auth-card w-100">
            <div class="text-center mb-4">
                <div class="auth-logo"><i class="bi bi-buildings"></i> BizConnect</div>
                <p class="text-white-50">Property Management Platform</p>
            </div>

            <?php
            $flash = getFlashMessages();
            if (!empty($flash)):
                foreach ($flash as $type => $msg):
                    $alertClass = match($type) { 'error' => 'danger', 'success' => 'success', 'warning' => 'warning', default => 'info' };
            ?>
                <div class="alert alert-<?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
                    <?php echo $type === 'info' ? $msg : e($msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; endif; ?>

            <div class="card">
                <div class="card-body p-4">
