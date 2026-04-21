<?php $pageTitle = 'Reset Password'; require_once VIEWS_PATH . '/layouts/auth_layout.php'; ?>

<h4 class="text-center mb-4">Reset Password</h4>
<p class="text-muted text-center small">Enter your new password below.</p>

<form method="POST" action="?page=auth&action=resetPassword">
    <?php echo csrfField(); ?>
    <input type="hidden" name="token" value="<?php echo e($token); ?>">

    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 8 characters" required minlength="8">
        </div>
    </div>

    <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm New Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required minlength="8">
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-check-circle"></i> Reset Password
    </button>

    <p class="text-center small mb-0">
        <a href="?page=auth&action=login"><i class="bi bi-arrow-left"></i> Back to Login</a>
    </p>
</form>

<?php require_once VIEWS_PATH . '/layouts/auth_footer.php'; ?>
