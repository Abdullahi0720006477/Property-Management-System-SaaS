<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Settings', 'url' => '?page=settings'], ['label' => 'Preferences', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Settings</h4>
</div>

<!-- Tab Navigation -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link" href="?page=settings&action=company">
            <i class="bi bi-building me-1"></i> Company
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="?page=settings&action=preferences">
            <i class="bi bi-sliders me-1"></i> Preferences
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="?page=settings&action=profile">
            <i class="bi bi-person me-1"></i> Profile
        </a>
    </li>
</ul>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-sliders me-2"></i>Application Preferences</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="?page=settings&action=preferences">
            <?php echo csrfField(); ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="currency" class="form-label">Currency</label>
                    <select class="form-select" id="currency" name="currency">
                        <option value="KES" <?php echo ($settings['currency'] ?? 'KES') === 'KES' ? 'selected' : ''; ?>>KES - Kenyan Shilling</option>
                        <option value="USD" <?php echo ($settings['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD - US Dollar</option>
                        <option value="EUR" <?php echo ($settings['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                        <option value="GBP" <?php echo ($settings['currency'] ?? '') === 'GBP' ? 'selected' : ''; ?>>GBP - British Pound</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="date_format" class="form-label">Date Format</label>
                    <select class="form-select" id="date_format" name="date_format">
                        <option value="Y-m-d" <?php echo ($settings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : ''; ?>>2026-03-19 (Y-m-d)</option>
                        <option value="d/m/Y" <?php echo ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : ''; ?>>19/03/2026 (d/m/Y)</option>
                        <option value="m/d/Y" <?php echo ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : ''; ?>>03/19/2026 (m/d/Y)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="timezone" class="form-label">Timezone</label>
                    <select class="form-select" id="timezone" name="timezone">
                        <option value="Africa/Nairobi" <?php echo ($settings['timezone'] ?? 'Africa/Nairobi') === 'Africa/Nairobi' ? 'selected' : ''; ?>>Africa/Nairobi (EAT)</option>
                        <option value="Africa/Lagos" <?php echo ($settings['timezone'] ?? '') === 'Africa/Lagos' ? 'selected' : ''; ?>>Africa/Lagos (WAT)</option>
                        <option value="Africa/Cairo" <?php echo ($settings['timezone'] ?? '') === 'Africa/Cairo' ? 'selected' : ''; ?>>Africa/Cairo (EET)</option>
                        <option value="Africa/Johannesburg" <?php echo ($settings['timezone'] ?? '') === 'Africa/Johannesburg' ? 'selected' : ''; ?>>Africa/Johannesburg (SAST)</option>
                        <option value="UTC" <?php echo ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                        <option value="America/New_York" <?php echo ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : ''; ?>>America/New_York (EST)</option>
                        <option value="Europe/London" <?php echo ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : ''; ?>>Europe/London (GMT)</option>
                    </select>
                </div>

                <div class="col-12">
                    <hr>
                    <h6 class="text-muted mb-3">Late Payment Settings</h6>
                </div>

                <div class="col-md-6">
                    <label for="late_fee_percentage" class="form-label">Late Payment Fee (%)</label>
                    <input type="number" class="form-control" id="late_fee_percentage" name="late_fee_percentage" value="<?php echo e($settings['late_fee_percentage'] ?? '0'); ?>" min="0" max="100" step="0.1">
                    <div class="form-text">Percentage charged on overdue invoices.</div>
                </div>

                <div class="col-md-6">
                    <label for="late_fee_grace_days" class="form-label">Grace Days</label>
                    <input type="number" class="form-control" id="late_fee_grace_days" name="late_fee_grace_days" value="<?php echo e($settings['late_fee_grace_days'] ?? '0'); ?>" min="0" max="90">
                    <div class="form-text">Number of days after due date before late fee applies.</div>
                </div>

                <div class="col-12">
                    <hr>
                    <h6 class="text-muted mb-3">Automation</h6>
                </div>

                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="auto_rent_invoices" name="auto_rent_invoices" <?php echo !empty($settings['auto_rent_invoices']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="auto_rent_invoices">Auto-generate rent invoices</label>
                    </div>
                    <div class="form-text">Automatically create monthly rent invoices for active leases.</div>
                </div>

                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="sms_reminders" name="sms_reminders" <?php echo !empty($settings['sms_reminders']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="sms_reminders">SMS payment reminders</label>
                    </div>
                    <div class="form-text text-warning"><i class="bi bi-clock me-1"></i>Coming soon</div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Save Preferences</button>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
