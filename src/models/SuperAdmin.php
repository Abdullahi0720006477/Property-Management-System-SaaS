<?php
class SuperAdmin {
    public static function findById(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM super_admins WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM super_admins WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function getAll(): array {
        $db = Database::getInstance();
        return $db->query("SELECT * FROM super_admins ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateLastLogin(int $id): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE super_admins SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }
}
