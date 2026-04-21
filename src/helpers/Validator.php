<?php
/**
 * Input Validation Class
 */
class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = "$label is required.";
        }
        return $this;
    }

    public function email(string $field, string $label = 'Email'): self
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "$label is not a valid email address.";
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field] = "$label must be at least $min characters.";
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field] = "$label must not exceed $max characters.";
        }
        return $this;
    }

    public function numeric(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = "$label must be a number.";
        }
        return $this;
    }

    public function date(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field])) {
            $d = DateTime::createFromFormat('Y-m-d', $this->data[$field]);
            if (!$d || $d->format('Y-m-d') !== $this->data[$field]) {
                $this->errors[$field] = "$label must be a valid date (YYYY-MM-DD).";
            }
        }
        return $this;
    }

    public function phone(string $field, string $label = 'Phone'): self
    {
        if (!empty($this->data[$field]) && !preg_match('/^[\+]?[0-9\s\-\(\)]{7,20}$/', $this->data[$field])) {
            $this->errors[$field] = "$label is not a valid phone number.";
        }
        return $this;
    }

    public function match(string $field1, string $field2, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field1));
        if (($this->data[$field1] ?? '') !== ($this->data[$field2] ?? '')) {
            $this->errors[$field2] = "$label fields do not match.";
        }
        return $this;
    }

    public function in(string $field, array $values, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && !in_array($this->data[$field], $values)) {
            $this->errors[$field] = "$label has an invalid value.";
        }
        return $this;
    }

    public function unique(string $field, string $table, string $column, ?int $excludeId = null, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field])) {
            $db = Database::getInstance();
            $sql = "SELECT COUNT(*) FROM $table WHERE $column = ?";
            $params = [$this->data[$field]];
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            if ($stmt->fetchColumn() > 0) {
                $this->errors[$field] = "$label is already taken.";
            }
        }
        return $this;
    }

    /**
     * Validate a positive number (greater than 0)
     */
    public function positive(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field])) {
            if (!is_numeric($this->data[$field]) || (float) $this->data[$field] <= 0) {
                $this->errors[$field] = "$label must be a positive number.";
            }
        }
        return $this;
    }

    /**
     * Validate date is after another date field
     */
    public function after(string $field, string $afterField, string $label = '', string $afterLabel = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $afterLabel = $afterLabel ?: ucfirst(str_replace('_', ' ', $afterField));
        if (!empty($this->data[$field]) && !empty($this->data[$afterField])) {
            if (strtotime($this->data[$field]) <= strtotime($this->data[$afterField])) {
                $this->errors[$field] = "$label must be after $afterLabel.";
            }
        }
        return $this;
    }

    /**
     * Validate file size from $_FILES
     */
    public function maxFileSize(string $field, int $maxBytes = MAX_FILE_SIZE, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            if ($_FILES[$field]['size'] > $maxBytes) {
                $sizeMB = round($maxBytes / 1024 / 1024, 1);
                $this->errors[$field] = "$label must not exceed {$sizeMB}MB.";
            }
        }
        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return reset($this->errors) ?: '';
    }
}
