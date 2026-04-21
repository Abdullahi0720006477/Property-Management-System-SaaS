<?php
/**
 * File Upload Handler
 */
class FileUpload
{
    private array $errors = [];

    /**
     * Upload a file
     * @param array $file $_FILES['field_name']
     * @param string $directory Subdirectory within uploads/
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Max file size in bytes
     * @return string|false Filename on success, false on failure
     */
    public function upload(array $file, string $directory = '', array $allowedTypes = [], int $maxSize = MAX_FILE_SIZE): string|false
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadError($file['error']);
            return false;
        }

        // Check file size
        if ($file['size'] > $maxSize) {
            $this->errors[] = 'File size exceeds the maximum allowed (' . ($maxSize / 1024 / 1024) . 'MB).';
            return false;
        }

        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
            $this->errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes);
            return false;
        }

        // Create directory if needed
        $uploadDir = UPLOAD_PATH . '/' . trim($directory, '/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('file_', true) . '.' . strtolower($extension);
        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->errors[] = 'Failed to move uploaded file.';
            return false;
        }

        // Return relative path from uploads/
        return trim($directory . '/' . $filename, '/');
    }

    /**
     * Delete an uploaded file
     */
    public function delete(string $relativePath): bool
    {
        $fullPath = UPLOAD_PATH . '/' . $relativePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return $this->errors[0] ?? '';
    }

    private function getUploadError(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File is too large.',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            default => 'Unknown upload error.',
        };
    }
}
