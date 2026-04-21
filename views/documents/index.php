<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Documents', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-folder me-2"></i>Documents</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-upload me-1"></i>Upload Document
    </button>
</div>

<!-- Category Filter Badges -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="?page=documents" class="badge text-decoration-none <?= empty($category) ? 'bg-primary' : 'bg-secondary' ?>" style="font-size:0.8rem;padding:0.4rem 0.75rem;">
        All (<?= array_sum($categoryCounts ?? []) ?>)
    </a>
    <?php
    $categoryLabels = [
        'lease'        => 'Leases',
        'id_copy'      => 'ID Copies',
        'receipt'      => 'Receipts',
        'utility_bill' => 'Utility Bills',
        'other'        => 'Other',
    ];
    foreach ($categoryLabels as $catKey => $catLabel):
        $cnt = $categoryCounts[$catKey] ?? 0;
    ?>
    <a href="?page=documents&category=<?= e($catKey) ?>"
       class="badge text-decoration-none <?= $category === $catKey ? 'bg-primary' : 'bg-secondary' ?>"
       style="font-size:0.8rem;padding:0.4rem 0.75rem;">
        <?= e($catLabel) ?> (<?= $cnt ?>)
    </a>
    <?php endforeach; ?>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="page" value="documents">
            <?php if ($category): ?>
            <input type="hidden" name="category" value="<?= e($category) ?>">
            <?php endif; ?>
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search documents by name..." value="<?= e($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</div>

<!-- Documents Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($documents)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-folder2-open" style="font-size:3rem;"></i>
            <p class="mt-2 mb-0">No documents found.</p>
            <p class="small">Upload your first document using the button above.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Uploaded By</th>
                        <th>Size</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td>
                            <a href="?page=documents&action=download&id=<?= $doc['id'] ?>" class="text-decoration-none">
                                <?php
                                $iconMap = [
                                    'application/pdf' => 'bi-file-earmark-pdf text-danger',
                                    'image/jpeg'      => 'bi-file-earmark-image text-info',
                                    'image/png'       => 'bi-file-earmark-image text-info',
                                    'image/gif'       => 'bi-file-earmark-image text-info',
                                    'image/webp'      => 'bi-file-earmark-image text-info',
                                    'text/plain'      => 'bi-file-earmark-text text-secondary',
                                    'text/csv'        => 'bi-file-earmark-spreadsheet text-success',
                                ];
                                $icon = $iconMap[$doc['mime_type'] ?? ''] ?? 'bi-file-earmark text-secondary';
                                if (str_contains($doc['mime_type'] ?? '', 'word')) $icon = 'bi-file-earmark-word text-primary';
                                if (str_contains($doc['mime_type'] ?? '', 'sheet') || str_contains($doc['mime_type'] ?? '', 'excel')) $icon = 'bi-file-earmark-excel text-success';
                                ?>
                                <i class="bi <?= $icon ?> me-1"></i>
                                <?= e($doc['name']) ?>
                            </a>
                        </td>
                        <td>
                            <?php
                            $catBadgeColors = [
                                'lease'        => 'bg-primary',
                                'id_copy'      => 'bg-info',
                                'receipt'      => 'bg-success',
                                'utility_bill' => 'bg-warning text-dark',
                                'other'        => 'bg-secondary',
                            ];
                            $badgeClass = $catBadgeColors[$doc['category']] ?? 'bg-secondary';
                            $catDisplay = $categoryLabels[$doc['category']] ?? ucfirst($doc['category']);
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= e($catDisplay) ?></span>
                        </td>
                        <td>
                            <?php if ($doc['uploader_first']): ?>
                                <?= e($doc['uploader_first'] . ' ' . $doc['uploader_last']) ?>
                            <?php else: ?>
                                <span class="text-muted">--</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $size = $doc['file_size'];
                            if ($size >= 1048576) {
                                echo round($size / 1048576, 1) . ' MB';
                            } elseif ($size >= 1024) {
                                echo round($size / 1024, 1) . ' KB';
                            } else {
                                echo $size . ' B';
                            }
                            ?>
                        </td>
                        <td><?= formatDate($doc['created_at']) ?></td>
                        <td class="text-end">
                            <a href="?page=documents&action=download&id=<?= $doc['id'] ?>" class="btn btn-sm btn-outline-primary" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            <form method="POST" action="?page=documents&action=delete&id=<?= $doc['id'] ?>" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this document?');">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalRecords > RECORDS_PER_PAGE): ?>
<div class="mt-3">
    <?= paginate($totalRecords, $currentPage, RECORDS_PER_PAGE, '?page=documents' . ($category ? '&category=' . urlencode($category) : '') . ($search ? '&search=' . urlencode($search) : '')) ?>
</div>
<?php endif; ?>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="?page=documents&action=upload" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel"><i class="bi bi-upload me-2"></i>Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document" class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="document" name="document" required>
                        <div class="form-text">Max 10MB. Allowed: Images, PDF, Word, Excel, CSV, Text.</div>
                    </div>
                    <div class="mb-3">
                        <label for="doc_name" class="form-label">Document Name</label>
                        <input type="text" class="form-control" id="doc_name" name="doc_name" placeholder="Leave blank to use filename">
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="other">Other</option>
                            <option value="lease">Lease</option>
                            <option value="id_copy">ID Copy</option>
                            <option value="receipt">Receipt</option>
                            <option value="utility_bill">Utility Bill</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="entity_type" class="form-label">Related To (optional)</label>
                            <select class="form-select" id="entity_type" name="entity_type">
                                <option value="">-- None --</option>
                                <option value="tenant">Tenant</option>
                                <option value="property">Property</option>
                                <option value="unit">Unit</option>
                                <option value="lease">Lease</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="entity_id" class="form-label">Entity ID</label>
                            <input type="number" class="form-control" id="entity_id" name="entity_id" placeholder="ID number">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
