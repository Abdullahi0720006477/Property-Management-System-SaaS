<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | <?php echo defined('APP_NAME') ? APP_NAME : 'Property Management System'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="text-center">
            <div class="mb-4">
                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 5rem;"></i>
            </div>
            <h1 class="display-1 fw-bold text-muted">500</h1>
            <h3 class="mb-3">Something Went Wrong</h3>
            <p class="text-muted mb-4">We encountered an internal error. Please try again later or contact the administrator.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="?page=dashboard" class="btn btn-primary">
                    <i class="bi bi-house me-1"></i> Go to Dashboard
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Go Back
                </a>
            </div>
        </div>
    </div>
</body>
</html>
