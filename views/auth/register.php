<?php
$pageTitle = 'Start Your Free Trial';
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
require_once VIEWS_PATH . '/layouts/auth_layout.php';
?>

<h4 class="text-center mb-1">Start Your Free Trial</h4>
<p class="text-center text-muted small mb-4">Create your BizConnect account</p>

<form method="POST" action="?page=auth&action=register">
    <?php echo csrfField(); ?>

    <div class="mb-3">
        <label for="company_name" class="form-label">Company Name</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-building"></i></span>
            <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo e($old['company_name'] ?? ''); ?>" placeholder="Your company or business name" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="full_name" class="form-label">Full Name</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo e($old['full_name'] ?? ''); ?>" placeholder="Your full name" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo e($old['email'] ?? ''); ?>" placeholder="you@company.com" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">Phone Number</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo e($old['phone'] ?? ''); ?>" placeholder="+254..." required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required minlength="8" placeholder="Min 8 characters">
        </div>
        <div class="col-md-6 mb-3">
            <label for="password_confirm" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required placeholder="Repeat password">
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-rocket-takeoff"></i> Start Free Trial
    </button>

    <p class="text-center text-muted small mb-0">
        Already have an account? <a href="?page=auth&action=login">Sign In</a>
    </p>
</form>

<?php require_once VIEWS_PATH . '/layouts/auth_footer.php'; ?>
