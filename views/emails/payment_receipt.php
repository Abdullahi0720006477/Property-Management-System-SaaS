<?php
require_once VIEWS_PATH . '/emails/_layout.php';

$isAuto     = !empty($is_auto_paid) && $is_auto_paid;
$refNumber  = !empty($reference_number) ? htmlspecialchars($reference_number) : 'N/A';
$method     = ucwords(str_replace('_', ' ', $payment_method ?? 'cash'));
$receiptNo  = str_pad($id, 6, '0', STR_PAD_LEFT);
$currency   = defined('CURRENCY_SYMBOL') ? CURRENCY_SYMBOL : 'KSh';

$autoBadge = $isAuto
    ? '<span class="badge-auto">&#9889; Paid Automatically</span>'
    : '<span class="badge-success">&#10003; Paid</span>';

$body = <<<HTML
<p>Dear <strong>{$tenant_name}</strong>,</p>
<p>Your rent payment has been received. Below is your official receipt.</p>

<div class="amount-box">
  <div class="amount">{$currency} {$amount}</div>
  <div style="margin-top:8px">{$autoBadge}</div>
  <div style="font-size:13px;color:#555;margin-top:6px">Receipt #{$receiptNo}</div>
</div>

<table class="details">
  <tr><td>Property</td><td>{$property_name}</td></tr>
  <tr><td>Unit</td><td>{$unit_number}</td></tr>
  <tr><td>Payment Date</td><td>{$payment_date}</td></tr>
  <tr><td>Due Date</td><td>{$due_date}</td></tr>
  <tr><td>Payment Method</td><td>{$method}</td></tr>
  <tr><td>Reference / Transaction ID</td><td>{$refNumber}</td></tr>
HTML;

if ($isAuto && !empty($auto_paid_at)) {
    $body .= "<tr><td>Auto-Processed At</td><td>{$auto_paid_at}</td></tr>";
}

$body .= <<<HTML
</table>

<p style="font-size:13px;color:#666;">This is a computer-generated receipt and does not require a signature.</p>
HTML;

echo emailLayout('Payment Receipt #' . $receiptNo, $body);
