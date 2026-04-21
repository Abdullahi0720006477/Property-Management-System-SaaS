<?php
require_once VIEWS_PATH . '/emails/_layout.php';

$urgency = $days_left <= 7 ? 'badge-danger' : 'badge-auto';

$body = <<<HTML
<p>Dear <strong>{$tenant_name}</strong>,</p>
<p>This is a courtesy notice that your lease agreement is expiring soon.</p>

<div class="amount-box" style="background:#eff6ff;border-color:#93c5fd;">
  <div style="font-size:28px;font-weight:bold;color:#1e40af;">{$days_left} Days Left</div>
  <div style="margin-top:8px"><span class="{$urgency}">&#128197; Lease Expiring</span></div>
</div>

<table class="details">
  <tr><td>Property</td><td>{$property_name}</td></tr>
  <tr><td>Unit</td><td>{$unit_number}</td></tr>
  <tr><td>Lease Start</td><td>{$start_date}</td></tr>
  <tr><td>Lease End</td><td>{$end_date}</td></tr>
  <tr><td>Monthly Rent</td><td>{$monthly_rent}</td></tr>
</table>

<p>Please contact your property manager <strong>before the expiry date</strong> to discuss renewal options.</p>

<p style="font-size:13px;color:#888;margin-top:24px;">This is an automated notice sent by {$appName}.</p>
HTML;

echo emailLayout('Lease Expiry Notice — ' . $days_left . ' Days Left', $body);
