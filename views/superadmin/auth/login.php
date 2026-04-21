<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | BizConnect Admin</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/design-system.css" rel="stylesheet">
    <style>
        body { background: var(--color-bg-secondary); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
<div style="width:100%;max-width:420px;padding:1rem;">
    <div class="text-center mb-4">
        <img src="assets/img/logo-mark-white.svg" alt="BizConnect" style="height:40px;background:var(--color-primary);padding:8px;border-radius:8px;" onerror="this.style.display='none'">
        <h4 class="mt-3 fw-bold">BizConnect Admin</h4>
        <p class="text-muted" style="font-size:0.85rem;">Sign in to the super admin panel</p>
    </div>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger py-2" style="font-size:0.85rem;">
            <?php foreach ($errors as $err): ?>
                <div><?= e($err) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="superadmin.php?page=auth&action=login">
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="admin@bizconnect.com" value="<?= e($email ?? '') ?>" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Sign In</button>
            </form>
        </div>
    </div>
    <p class="text-center text-muted mt-3" style="font-size:0.75rem;">BizConnect Super Admin Panel</p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
