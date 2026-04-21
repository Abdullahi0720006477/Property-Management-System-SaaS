<?php $pageTitle = 'Forgot Password'; require_once VIEWS_PATH . '/layouts/auth_layout.php'; ?>

<h4 class="text-center mb-4">Forgot Password</h4>
<p class="text-muted text-center small">Enter your email address and we'll send you instructions to reset your password.</p>

<form method="POST" action="?page=auth&action=forgot_password">
    <?php echo csrfField(); ?>

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email" name="email" required autofocus>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-envelope"></i> Send Reset Link
    </button>

    <p class="text-center small mb-0">
        <a href="?page=auth&action=login"><i class="bi bi-arrow-left"></i> Back to Login</a>
    </p>
</form>

<?php require_once VIEWS_PATH . '/layouts/auth_footer.php'; ?>
