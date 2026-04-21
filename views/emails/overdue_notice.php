<?php
require_once VIEWS_PATH . '/emails/_layout.php';

$currency  = defined('CURRENCY_SYMBOL') ? CURRENCY_SYMBOL : 'KSh';
$appUrl    = defined('APP_URL') ? APP_URL : '';
$daysLate  = !empty($due_date) ? (int) floor((time() - strtotime($due_date)) / 86400) : 0;

$body = <<<HTML
<p>Dear <strong>{$tenant_name}</strong>,</p>
<p>This is a reminder that you have an <strong>overdue rent payment</strong> that requires your immediate attention.</p>

<div class="alert-box">
  <div class="alert-amount">{$currency} {$amount}</div>
  <div style="margin-top:8px"><span class="badge-danger">&#9888; Overdue — {$daysLate} day(s) late</span></div>
  <div style="font-size:13px;color:#666;margin-top:6px">Due Date: {$due_date}</div>
</div>

<table class="details">
  <tr><td>Property</td><td>{$property_name}</td></tr>
  <tr><td>Unit</td><td>{$unit_number}</td></tr>
  <tr><td>Original Due Date</td><td>{$due_date}</td></tr>
  <tr><td>Amount Due</td><td><strong>{$currency} {$amount}</strong></td></tr>
</table>

<p>Please settle this payment as soon as possible to avoid additional late fees. Contact your property manager if you need assistance.</p>

<p style="font-size:13px;color:#888;margin-top:24px;">This is an automated reminder sent by {$appName}.</p>
HTML;

echo emailLayout('Overdue Rent Notice', $body);
