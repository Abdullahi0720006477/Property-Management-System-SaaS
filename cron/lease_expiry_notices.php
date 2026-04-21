<?php
/**
 * Cron Job: Send lease expiry warning emails.
 *
 * Sends warnings at 30 days and 7 days before expiry.
 * Schedule: Run daily at 09:00
 *   php C:\laragon\www\propertyManagement\cron\lease_expiry_notices.php
 */

define('CRON_CONTEXT', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/helpers/functions.php';
require_once __DIR__ . '/../src/helpers/Mailer.php';

$db = Database::getInstance();

$warningDays = [30, 7];
$sent = 0;

foreach ($warningDays as $days) {
    $targetDate = date('Y-m-d', strtotime("+{$days} days"));
    $stmt = $db->prepare(
        "SELECT l.*, u.unit_number, p.name as property_name,
                CONCAT(t.first_name,' ',t.last_name) as tenant_name, t.email as tenant_email
         FROM leases l
         JOIN units u ON l.unit_id = u.id
         JOIN properties p ON u.property_id = p.id
         JOIN tenants t ON l.tenant_id = t.id
         WHERE l.lease_status = 'active'
           AND DATE(l.end_date) = ?
           AND t.email IS NOT NULL AND t.email != ''"
    );
    $stmt->execute([$targetDate]);
    $leases = $stmt->fetchAll();

    foreach ($leases as $lease) {
        if (Mailer::sendLeaseExpiryWarning($lease, $days)) {
            $sent++;
            echo date('[Y-m-d H:i:s]') . " Expiry notice ({$days} days) sent to {$lease['tenant_email']} (Lease #{$lease['id']}).\n";
        }
    }
}

echo date('[Y-m-d H:i:s]') . " Sent {$sent} lease expiry notice(s). Done.\n";
