<?php
/**
 * Document Manager Controller
 * Upload, list, download, and delete company documents
 */
requireAuth();
requireRole('company_admin', 'manager', 'staff', 'accountant', 'maintenance_tech');

$db = Database::getInstance();
$cid = companyId();

// Ensure documents table exists
$db->exec("
    CREATE TABLE IF NOT EXISTS documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        category ENUM('lease','id_copy','receipt','utility_bill','other') DEFAULT 'other',
        entity_type VARCHAR(50) DEFAULT NULL,
        entity_id INT DEFAULT NULL,
        uploaded_by INT DEFAULT NULL,
        file_size INT DEFAULT 0,
        mime_type VARCHAR(100) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
        INDEX idx_doc_company (company_id),
        INDEX idx_doc_entity (entity_type, entity_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

$action = $action ?? 'index';

switch ($action) {
    case 'upload':
        if (!isPost()) {
            redirect('?page=documents');
        }

        if (!validateCsrfToken()) {
            setFlashMessage('error', 'Invalid security token. Please try again.');
            redirect('?page=documents');
        }

        if (empty($_FILES['document']['name'])) {
            setFlashMessage('error', 'Please select a file to upload.');
            redirect('?page=documents');
        }

        $category   = postData('category') ?: 'other';
        $entityType = postData('entity_type') ?: null;
        $entityId   = postData('entity_id') ? (int) postData('entity_id') : null;
        $docName    = postData('doc_name') ?: $_FILES['document']['name'];

        // Allowed MIME types: images + PDF + common document types
        $allowedTypes = array_merge(
            ALLOWED_IMAGE_TYPES,
            ALLOWED_DOC_TYPES,
            [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
                'text/csv',
            ]
        );

        $uploader = new FileUpload();
        $filePath = $uploader->upload($_FILES['document'], 'documents', $allowedTypes, 10 * 1024 * 1024); // 10MB limit

        if ($filePath === false) {
            setFlashMessage('error', $uploader->firstError());
            redirect('?page=documents');
        }

        $stmt = $db->prepare("
            INSERT INTO documents (company_id, name, file_path, category, entity_type, entity_id, uploaded_by, file_size, mime_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, UPLOAD_PATH . '/' . $filePath);
        finfo_close($finfo);

        $stmt->execute([
            $cid,
            $docName,
            $filePath,
            $category,
            $entityType,
            $entityId,
            currentUserId(),
            $_FILES['document']['size'],
            $mimeType,
        ]);

        setFlashMessage('success', 'Document uploaded successfully.');
        redirect('?page=documents');
        break;

    case 'download':
        $docId = (int) ($id ?? 0);
        $stmt = $db->prepare("SELECT * FROM documents WHERE id = ? AND company_id = ?");
        $stmt->execute([$docId, $cid]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc) {
            setFlashMessage('error', 'Document not found.');
            redirect('?page=documents');
        }

        $fullPath = UPLOAD_PATH . '/' . $doc['file_path'];
        if (!file_exists($fullPath)) {
            setFlashMessage('error', 'File not found on server.');
            redirect('?page=documents');
        }

        header('Content-Type: ' . ($doc['mime_type'] ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . basename($doc['name']) . '"');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;

    case 'delete':
        if (!isPost()) {
            redirect('?page=documents');
        }

        if (!validateCsrfToken()) {
            setFlashMessage('error', 'Invalid security token. Please try again.');
            redirect('?page=documents');
        }

        $docId = (int) ($id ?? 0);
        $stmt = $db->prepare("SELECT * FROM documents WHERE id = ? AND company_id = ?");
        $stmt->execute([$docId, $cid]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc) {
            setFlashMessage('error', 'Document not found.');
            redirect('?page=documents');
        }

        // Delete physical file
        $uploader = new FileUpload();
        $uploader->delete($doc['file_path']);

        // Delete database record
        $stmt = $db->prepare("DELETE FROM documents WHERE id = ? AND company_id = ?");
        $stmt->execute([$docId, $cid]);

        setFlashMessage('success', 'Document deleted successfully.');
        redirect('?page=documents');
        break;

    case 'index':
    default:
        $category = getData('category');
        $search   = getData('search');
        $currentPage = max(1, (int) getData('pg', '1'));
        $offset = ($currentPage - 1) * RECORDS_PER_PAGE;

        // Build query with optional filters
        $where  = "WHERE d.company_id = ?";
        $params = [$cid];

        if ($category) {
            $where .= " AND d.category = ?";
            $params[] = $category;
        }

        if ($search) {
            $where .= " AND d.name LIKE ?";
            $params[] = '%' . $search . '%';
        }

        // Total count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM documents d $where");
        $countStmt->execute($params);
        $totalRecords = (int) $countStmt->fetchColumn();

        // Fetch documents
        $sql = "
            SELECT d.*, u.first_name AS uploader_first, u.last_name AS uploader_last
            FROM documents d
            LEFT JOIN users u ON d.uploaded_by = u.id
            $where
            ORDER BY d.created_at DESC
            LIMIT " . (int) RECORDS_PER_PAGE . " OFFSET " . (int) $offset;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Category counts for badges
        $catStmt = $db->prepare("
            SELECT category, COUNT(*) AS cnt
            FROM documents
            WHERE company_id = ?
            GROUP BY category
        ");
        $catStmt->execute([$cid]);
        $categoryCounts = [];
        foreach ($catStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $categoryCounts[$row['category']] = $row['cnt'];
        }

        $pageTitle = 'Documents';
        require_once VIEWS_PATH . '/documents/index.php';
        break;
}
