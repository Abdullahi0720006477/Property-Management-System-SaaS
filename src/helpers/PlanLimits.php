<?php
require_once __DIR__ . '/../../config/plans.php';

class PlanLimits {
    public static function canAddProperty(int $companyId): bool {
        $company = self::getCompany($companyId);
        $plan = SUBSCRIPTION_PLANS[$company['subscription_plan']] ?? SUBSCRIPTION_PLANS['trial'];
        if ($plan['max_properties'] === -1) return true;
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM properties WHERE company_id = ? AND is_active = 1");
        $stmt->execute([$companyId]);
        return $stmt->fetchColumn() < $plan['max_properties'];
    }

    public static function canAddUnit(int $companyId): bool {
        $company = self::getCompany($companyId);
        $plan = SUBSCRIPTION_PLANS[$company['subscription_plan']] ?? SUBSCRIPTION_PLANS['trial'];
        if ($plan['max_units'] === -1) return true;
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM units WHERE company_id = ?");
        $stmt->execute([$companyId]);
        return $stmt->fetchColumn() < $plan['max_units'];
    }

    public static function canAddUser(int $companyId): bool {
        $company = self::getCompany($companyId);
        $plan = SUBSCRIPTION_PLANS[$company['subscription_plan']] ?? SUBSCRIPTION_PLANS['trial'];
        if ($plan['max_users'] === -1) return true;
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE company_id = ? AND is_active = 1");
        $stmt->execute([$companyId]);
        return $stmt->fetchColumn() < $plan['max_users'];
    }

    public static function isSubscriptionActive(int $companyId): bool {
        $company = self::getCompany($companyId);
        if (!$company) return false;
        if ($company['subscription_status'] !== 'active') return false;
        if ($company['subscription_end'] && $company['subscription_end'] < date('Y-m-d')) return false;
        return true;
    }

    public static function getUsage(int $companyId): array {
        $company = self::getCompany($companyId);
        $plan = SUBSCRIPTION_PLANS[$company['subscription_plan']] ?? SUBSCRIPTION_PLANS['trial'];
        $db = Database::getInstance();

        $propCount = $db->prepare("SELECT COUNT(*) FROM properties WHERE company_id = ? AND is_active = 1");
        $propCount->execute([$companyId]);

        $unitCount = $db->prepare("SELECT COUNT(*) FROM units WHERE company_id = ?");
        $unitCount->execute([$companyId]);

        $userCount = $db->prepare("SELECT COUNT(*) FROM users WHERE company_id = ? AND is_active = 1");
        $userCount->execute([$companyId]);

        return [
            'properties' => ['used' => (int)$propCount->fetchColumn(), 'max' => $plan['max_properties']],
            'units' => ['used' => (int)$unitCount->fetchColumn(), 'max' => $plan['max_units']],
            'users' => ['used' => (int)$userCount->fetchColumn(), 'max' => $plan['max_users']],
            'plan' => $company['subscription_plan'],
            'plan_name' => $plan['name'],
        ];
    }

    public static function hasFeature(int $companyId, string $feature): bool {
        $company = self::getCompany($companyId);
        $plan = SUBSCRIPTION_PLANS[$company['subscription_plan']] ?? SUBSCRIPTION_PLANS['trial'];
        return in_array('all', $plan['features']) || in_array($feature, $plan['features']);
    }

    private static function getCompany(int $companyId): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM companies WHERE id = ?");
        $stmt->execute([$companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
