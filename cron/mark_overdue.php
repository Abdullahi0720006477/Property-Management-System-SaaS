<?php
/**
 * Cron Job: Mark overdue payments and send email reminders.
 *
 * Schedule (Laragon Cronical or Windows Task Scheduler):
 *   php C:\laragon\www\propertyManagement\cron\mark_overdue.php
 *   Recommended: Run daily at 08:00
 */

define('CRON_CONTEXT', true);
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/helpers/functions.php';
require_once __DIR__ . '/../src/helpers/Mailer.php';
require_once __DIR__ . '/../src/models/Payment.php';

$paymentModel = new Payment();

// 1. Mark pending → overdue
$marked = $paymentModel->markOverduePayments();
echo date('[Y-m-d H:i:s]') . " Marked {$marked} payment(s) as overdue.\n";

// 2. Send email reminders to tenants with overdue balances
$overdueList = $paymentModel->getOverdueWithEmail();
$sent = 0;
foreach ($overdueList as $payment) {
    if (Mailer::sendOverdueNotice($payment)) {
        $sent++;
        echo date('[Y-m-d H:i:s]') . " Overdue notice sent to {$payment['tenant_email']} (Payment #{$payment['id']}).\n";
    }
}

echo date('[Y-m-d H:i:s]') . " Sent {$sent} overdue notice email(s). Done.\n";
