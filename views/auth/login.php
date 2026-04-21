<?php $pageTitle = 'Login'; require_once VIEWS_PATH . '/layouts/auth_layout.php'; ?>

<div class="text-center mb-4">
    <img src="assets/img/logo-full.svg" alt="BizConnect" style="height:40px;">
</div>

<h4 class="text-center mb-4">Sign In</h4>

<form method="POST" action="?page=auth&action=login">
    <?php echo csrfField(); ?>

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" value="1">
            <label class="form-check-label" for="remember_me">Remember me for 30 days</label>
        </div>
        <a href="?page=auth&action=forgot_password" class="text-decoration-none small">Forgot password?</a>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-box-arrow-in-right"></i> Sign In
    </button>

    <p class="text-center text-muted small mb-0">
        Don't have an account? <a href="?page=auth&action=register">Start Free Trial</a>
    </p>
</form>

<?php require_once VIEWS_PATH . '/layouts/auth_footer.php'; ?>
