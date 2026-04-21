<?php
/**
 * Cron Job: Auto-generate monthly rent payments for all active leases.
 *
 * This script creates a "paid" payment record marked as auto-paid
 * for every active lease that has no payment yet for the current month.
 * It then emails a receipt to each tenant.
 *
 * Schedule (Laragon Cronical or Windows Task Scheduler):
 *   php C:\laragon\www\propertyManagement\cron\auto_generate_rent.php
 *   Recommended: Run on the 1st of each month at 07:00
 *
 * The due day for rent can be set in .env: RENT_DUE_DAY=5
 */

define('CRON_CONTEXT', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/helpers/functions.php';
require_once __DIR__ . '/../src/helpers/Mailer.php';
require_once __DIR__ . '/../src/models/Payment.php';

$paymentModel = new Payment();

echo date('[Y-m-d H:i:s]') . " Starting auto rent generation for " . date('F Y') . "...\n";

$created = $paymentModel->autoGenerateMonthlyRents();

if (empty($created)) {
    echo date('[Y-m-d H:i:s]') . " No new payments needed — all active leases already have a record this month.\n";
    exit(0);
}

foreach ($created as $item) {
    echo date('[Y-m-d H:i:s]')
        . " AUTO-PAID: Payment #{$item['payment_id']}"
        . " | Tenant: {$item['tenant_name']}"
        . " | Unit: {$item['unit_number']} ({$item['property_name']})"
        . " | Ref: {$item['reference_number']}\n";

    // Send receipt email
    if (!empty($item['tenant_email'])) {
        $receiptData = array_merge($item, [
            'id'             => $item['payment_id'],
            'amount'         => $item['monthly_rent'],
            'payment_date'   => date('Y-m-d'),
            'payment_method' => 'auto',
            'is_auto_paid'   => 1,
            'auto_paid_at'   => date('Y-m-d H:i:s'),
            'reference_number' => $item['reference_number'],
        ]);
        if (Mailer::sendPaymentReceipt($receiptData)) {
            echo date('[Y-m-d H:i:s]') . "   Receipt emailed to {$item['tenant_email']}.\n";
        }
    }
}

echo date('[Y-m-d H:i:s]') . " Done. Created " . count($created) . " auto-payment(s).\n";
