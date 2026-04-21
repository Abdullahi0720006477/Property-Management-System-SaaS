<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Support', 'url' => '?page=support'], ['label' => 'New Ticket', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">New Support Ticket</h4>
    <a href="?page=support" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Tickets
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=support&action=create">
            <?php echo csrfField(); ?>

            <div class="mb-3">
                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                <input type="text" name="subject" id="subject" class="form-control" maxlength="255" required placeholder="Brief summary of your issue">
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                <select name="priority" id="priority" class="form-select" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                <textarea name="message" id="message" class="form-control" rows="5" required placeholder="Describe your issue in detail..."></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>Submit Ticket
                </button>
                <a href="?page=support" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
