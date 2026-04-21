<?php
require_once VIEWS_PATH . '/emails/_layout.php';

$appUrl = defined('APP_URL') ? APP_URL : '';

$body = <<<HTML
<p>Dear <strong>{$full_name}</strong>,</p>
<p>Welcome to <strong>{$appName}</strong>! Your account has been created successfully.</p>

<table class="details">
  <tr><td>Email / Login</td><td>{$email}</td></tr>
  <tr><td>Temporary Password</td><td><strong>{$plain_password}</strong></td></tr>
  <tr><td>Role</td><td>{$role}</td></tr>
</table>

<p style="color:#b45309;font-size:13px;">&#9888; Please log in and change your password immediately.</p>

<div style="text-align:center">
  <a href="{$appUrl}" class="btn">Log In Now</a>
</div>

<p style="font-size:13px;color:#888;margin-top:24px;">If you did not expect this email, please contact your property manager.</p>
HTML;

echo emailLayout('Welcome to ' . $appName, $body);
