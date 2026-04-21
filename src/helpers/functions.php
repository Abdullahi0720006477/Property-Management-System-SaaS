<?php
/**
 * Global Helper Functions
 */

/**
 * Sanitize output for HTML display
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency amount
 */
function formatCurrency(float $amount): string
{
    return CURRENCY_SYMBOL . ' ' . number_format($amount, 2);
}

/**
 * Format date for display
 */
function formatDate(?string $date): string
{
    if (empty($date)) return 'N/A';
    return date(DISPLAY_DATE_FORMAT, strtotime($date));
}

/**
 * Format datetime for display
 */
function formatDateTime(?string $datetime): string
{
    if (empty($datetime)) return 'N/A';
    return date(DISPLAY_DATETIME_FORMAT, strtotime($datetime));
}

/**
 * Get status badge HTML
 */
function statusBadge(string $status, string $type = 'default'): string
{
    $classes = [
        'active' => 'bg-success', 'paid' => 'bg-success', 'resolved' => 'bg-success', 'closed' => 'bg-secondary',
        'occupied' => 'bg-success', 'vacant' => 'bg-info', 'pending' => 'bg-warning text-dark',
        'overdue' => 'bg-danger', 'expired' => 'bg-secondary', 'terminated' => 'bg-danger',
        'open' => 'bg-primary', 'in_progress' => 'bg-info', 'maintenance' => 'bg-warning text-dark',
        'reserved' => 'bg-purple', 'partial' => 'bg-warning text-dark',
        'low' => 'bg-success', 'medium' => 'bg-warning text-dark', 'high' => 'bg-danger', 'emergency' => 'bg-danger',
    ];
    $class = $classes[$status] ?? 'bg-secondary';
    $label = ucwords(str_replace('_', ' ', $status));
    return '<span class="badge ' . $class . '">' . e($label) . '</span>';
}

/**
 * Get priority badge
 */
function priorityBadge(string $priority): string
{
    return statusBadge($priority);
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 50): string
{
    if (strlen($text) <= $length) return e($text);
    return e(substr($text, 0, $length)) . '...';
}

/**
 * Get pagination HTML
 */
function paginate(int $totalRecords, mixed $currentPage, int $perPage = RECORDS_PER_PAGE, string $baseUrl = ''): string
{
    $currentPage = max(1, (int) $currentPage);
    $totalPages = (int) ceil($totalRecords / $perPage);
    if ($totalPages <= 1) return '';

    $html = '<nav><ul class="pagination justify-content-center">';

    // Previous
    $prevDisabled = $currentPage <= 1 ? 'disabled' : '';
    $html .= '<li class="page-item ' . $prevDisabled . '"><a class="page-link" href="' . $baseUrl . '&pg=' . ($currentPage - 1) . '">&laquo;</a></li>';

    // Pages
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);

    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&pg=1">1</a></li>';
        if ($start > 2) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $currentPage ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . '&pg=' . $i . '">' . $i . '</a></li>';
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&pg=' . $totalPages . '">' . $totalPages . '</a></li>';
    }

    // Next
    $nextDisabled = $currentPage >= $totalPages ? 'disabled' : '';
    $html .= '<li class="page-item ' . $nextDisabled . '"><a class="page-link" href="' . $baseUrl . '&pg=' . ($currentPage + 1) . '">&raquo;</a></li>';

    $html .= '</ul></nav>';
    return $html;
}

/**
 * Generate breadcrumb navigation HTML
 */
function breadcrumbs(array $crumbs): string {
    $html = '<nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb">';
    $last = array_key_last($crumbs);
    foreach ($crumbs as $i => $crumb) {
        if ($i === $last) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . e($crumb['label']) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . e($crumb['url']) . '">' . e($crumb['label']) . '</a></li>';
        }
    }
    $html .= '</ol></nav>';
    return $html;
}

/**
 * Get the base URL for the application
 */
function baseUrl(string $path = ''): string
{
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Asset URL helper
 */
function asset(string $path): string
{
    return 'assets/' . ltrim($path, '/');
}

/**
 * Upload path URL helper
 */
function uploadUrl(string $path): string
{
    return 'uploads/' . ltrim($path, '/');
}

/**
 * Check if request is POST
 */
function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Get POST data safely
 */
function postData(string $key, $default = ''): string
{
    return trim($_POST[$key] ?? $default);
}

/**
 * Get GET data safely
 */
function getData(string $key, $default = ''): string
{
    return trim($_GET[$key] ?? $default);
}

/**
 * Get current company ID from session
 */
function companyId(): ?int {
    return $_SESSION['company_id'] ?? null;
}

/**
 * Get current company name from session
 */
function companyName(): string {
    return $_SESSION['company_name'] ?? '';
}

/**
 * Get current company subscription plan
 */
function companyPlan(): string {
    return $_SESSION['company_plan'] ?? 'trial';
}

/**
 * Check if current user is a company admin
 */
function isCompanyAdmin(): bool {
    return ($_SESSION['user_role'] ?? '') === 'company_admin';
}

/**
 * Check if current user is a company staff member
 */
function isStaff(): bool {
    return in_array($_SESSION['user_role'] ?? '', ['company_admin', 'manager', 'staff', 'accountant', 'maintenance_tech']);
}

/**
 * Check if current user is a super admin
 */
function isSuperAdmin(): bool {
    return !empty($_SESSION['sa_id']);
}

/**
 * Require company admin privileges or redirect
 */
function requireCompanyAdmin() {
    if (!isCompanyAdmin()) {
        setFlashMessage('error', 'Access denied. Company admin privileges required.');
        redirect('?page=dashboard');
    }
}
