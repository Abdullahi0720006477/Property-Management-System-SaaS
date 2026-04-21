<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?php renderBreadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Setup Wizard']]); ?>

<div class="card" style="max-width:700px;margin:0 auto;">
    <div class="card-body p-4">
        <!-- Step Indicator -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="text-center" style="flex:1;">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1"
                     style="width:36px;height:36px;background:<?= $i <= $step ? 'var(--color-primary)' : 'var(--color-border)' ?>;color:<?= $i <= $step ? '#fff' : 'var(--color-text-muted)' ?>;font-weight:700;font-size:0.85rem;">
                    <?= $i < $step ? '<i class="bi bi-check"></i>' : $i ?>
                </div>
                <div style="font-size:0.7rem;color:var(--color-text-secondary);">
                    <?= ['Company', 'Property', 'Units', 'Done'][$i-1] ?>
                </div>
            </div>
            <?php if ($i < 4): ?>
            <div style="flex:2;height:2px;background:<?= $i < $step ? 'var(--color-primary)' : 'var(--color-border)' ?>;margin-top:-12px;"></div>
            <?php endif; ?>
            <?php endfor; ?>
        </div>

        <?php if ($step === 1): ?>
        <!-- Step 1: Company Profile -->
        <h4 class="mb-1">Welcome! Let's set up your company</h4>
        <p class="text-muted mb-4" style="font-size:0.85rem;">Tell us about your property management company.</p>
        <form method="POST" action="?page=onboarding&action=save_company">
            <div class="mb-3">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control" value="<?= e($_SESSION['company_name'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Business Address</label>
                <input type="text" name="address" class="form-control" placeholder="e.g., Westlands, Nairobi">
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="Nairobi">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="+254...">
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Next: Add Property <i class="bi bi-arrow-right"></i></button>
            </div>
        </form>

        <?php elseif ($step === 2): ?>
        <!-- Step 2: First Property -->
        <h4 class="mb-1">Add your first property</h4>
        <p class="text-muted mb-4" style="font-size:0.85rem;">Register a building or property you manage.</p>
        <form method="POST" action="?page=onboarding&action=save_property">
            <div class="mb-3">
                <label class="form-label">Property Name</label>
                <input type="text" name="property_name" class="form-control" placeholder="e.g., Sunrise Apartments" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="property_address" class="form-control" placeholder="Property location">
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="property_city" class="form-control" value="Nairobi">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select name="property_type" class="form-select">
                        <option value="apartment">Apartment Building</option>
                        <option value="house">Houses</option>
                        <option value="commercial">Commercial</option>
                        <option value="mixed">Mixed Use</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <a href="?page=dashboard" class="btn btn-outline">Skip for now</a>
                <button type="submit" class="btn btn-primary">Next: Add Units <i class="bi bi-arrow-right"></i></button>
            </div>
        </form>

        <?php elseif ($step === 3): ?>
        <!-- Step 3: Add Units -->
        <h4 class="mb-1">Add units to your property</h4>
        <p class="text-muted mb-4" style="font-size:0.85rem;">Add apartment units, rooms, or spaces. You can add more later.</p>
        <form method="POST" action="?page=onboarding&action=save_units">
            <div id="unitRows">
                <?php for ($i = 0; $i < 3; $i++): ?>
                <div class="row mb-2 unit-row">
                    <div class="col-7">
                        <input type="text" name="unit_numbers[]" class="form-control form-control-sm" placeholder="Unit number (e.g., A<?= $i+1 ?>01)">
                    </div>
                    <div class="col-5">
                        <input type="number" name="rents[]" class="form-control form-control-sm" placeholder="Rent (KES)" step="100">
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            <button type="button" class="btn btn-sm btn-outline mb-3" onclick="addUnitRow()"><i class="bi bi-plus"></i> Add Row</button>
            <div class="d-flex justify-content-between">
                <a href="?page=dashboard" class="btn btn-outline">Skip for now</a>
                <button type="submit" class="btn btn-primary">Next: Finish <i class="bi bi-arrow-right"></i></button>
            </div>
        </form>
        <script>
        function addUnitRow() {
            var row = document.createElement('div');
            row.className = 'row mb-2 unit-row';
            row.innerHTML = '<div class="col-7"><input type="text" name="unit_numbers[]" class="form-control form-control-sm" placeholder="Unit number"></div><div class="col-5"><input type="number" name="rents[]" class="form-control form-control-sm" placeholder="Rent (KES)" step="100"></div>';
            document.getElementById('unitRows').appendChild(row);
        }
        </script>

        <?php elseif ($step === 4): ?>
        <!-- Step 4: Done! -->
        <div class="text-center py-4">
            <div class="mb-3" style="font-size:3rem;color:var(--color-success);"><i class="bi bi-check-circle-fill"></i></div>
            <h4>You're all set!</h4>
            <p class="text-muted mb-4">Your BizConnect workspace is ready. You can now manage properties, tenants, leases, and payments.</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="?page=onboarding&action=complete" class="btn btn-primary"><i class="bi bi-grid-1x2 me-1"></i> Go to Dashboard</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
