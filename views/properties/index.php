<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Properties', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Properties</h4>
    <a href="?page=properties&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Property
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="properties">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo e($search); ?>" placeholder="Search by name or address...">
            </div>
            <div class="col-md-3">
                <label for="city" class="form-label">City</label>
                <select class="form-select" id="city" name="city">
                    <option value="">All Cities</option>
                    <?php foreach ($cities as $c): ?>
                        <option value="<?php echo e($c); ?>" <?php echo $city === $c ? 'selected' : ''; ?>><?php echo e($c); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-select" id="type" name="type">
                    <option value="">All Types</option>
                    <option value="apartment_building" <?php echo $type === 'apartment_building' ? 'selected' : ''; ?>>Apartment Building</option>
                    <option value="single_house" <?php echo $type === 'single_house' ? 'selected' : ''; ?>>Single House</option>
                    <option value="commercial" <?php echo $type === 'commercial' ? 'selected' : ''; ?>>Commercial</option>
                    <option value="mixed_use" <?php echo $type === 'mixed_use' ? 'selected' : ''; ?>>Mixed Use</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Properties Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>City</th>
                        <th>Type</th>
                        <th>Units</th>
                        <th>Occupancy</th>
                        <th>Manager</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($properties)): ?>
                        <?php foreach ($properties as $prop): ?>
                            <?php
                                $occupancy = $prop['unit_count'] > 0
                                    ? round(($prop['occupied_count'] / $prop['unit_count']) * 100, 1)
                                    : 0;
                            ?>
                            <tr>
                                <td>
                                    <a href="?page=properties&action=show&id=<?php echo $prop['id']; ?>" class="text-decoration-none fw-semibold">
                                        <?php echo e($prop['name']); ?>
                                    </a>
                                </td>
                                <td><?php echo e($prop['city'] ?? 'N/A'); ?></td>
                                <td><?php echo e(ucwords(str_replace('_', ' ', $prop['property_type']))); ?></td>
                                <td><?php echo (int) $prop['unit_count']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px; max-width: 80px;">
                                            <div class="progress-bar <?php echo $occupancy >= 75 ? 'bg-success' : ($occupancy >= 50 ? 'bg-warning' : 'bg-danger'); ?>"
                                                 style="width: <?php echo $occupancy; ?>%"></div>
                                        </div>
                                        <small><?php echo $occupancy; ?>%</small>
                                    </div>
                                </td>
                                <td><?php echo e($prop['manager_name'] ?? 'Unassigned'); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?page=properties&action=show&id=<?php echo $prop['id']; ?>" class="btn btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="?page=properties&action=edit&id=<?php echo $prop['id']; ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="?page=properties&action=delete&id=<?php echo $prop['id']; ?>" class="d-inline delete-form">
                                            <?php echo csrfField(); ?>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Delete"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-item-name="<?php echo e($prop['name']); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No properties found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php
    $baseUrl = '?page=properties&search=' . urlencode($search) . '&city=' . urlencode($city) . '&type=' . urlencode($type);
    echo paginate($totalRecords, $currentPage, RECORDS_PER_PAGE, $baseUrl);
?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
