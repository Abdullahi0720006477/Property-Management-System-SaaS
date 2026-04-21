<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Create Announcement</h4>
    <a href="superadmin.php?page=announcements" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Announcements</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="superadmin.php?page=announcements&action=create">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Title</label>
                    <input type="text" name="title" class="form-control" required placeholder="Announcement title...">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Type</label>
                    <select name="type" class="form-select">
                        <option value="info">Info</option>
                        <option value="warning">Warning</option>
                        <option value="feature">Feature</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Message</label>
                    <textarea name="message" class="form-control" rows="4" required placeholder="Announcement message..."></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Target Audience</label>
                    <select name="target" class="form-select">
                        <option value="all">All Companies</option>
                        <option value="starter">Starter Plan</option>
                        <option value="professional">Professional Plan</option>
                        <option value="enterprise">Enterprise Plan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Start Date</label>
                    <input type="date" name="starts_at" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">End Date</label>
                    <input type="date" name="ends_at" class="form-control">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-megaphone me-1"></i>Publish Announcement</button>
            </div>
        </form>
    </div>
</div>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
