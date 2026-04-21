<?php
/**
 * Email base layout helper — wraps content in a professional HTML shell.
 * Usage: echo emailLayout('Subject', $innerHtml);
 */
function emailLayout(string $title, string $body): string
{
    $appName = defined('APP_NAME') ? APP_NAME : 'Property Management';
    $year    = date('Y');
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{$title}</title>
<style>
  body{margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#333}
  .wrapper{max-width:620px;margin:30px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08)}
  .header{background:#1a3c5e;padding:28px 32px;text-align:center}
  .header h1{margin:0;color:#fff;font-size:22px;letter-spacing:.5px}
  .header p{margin:4px 0 0;color:#a8c4e0;font-size:13px}
  .body{padding:32px}
  .footer{background:#f4f6f9;padding:20px 32px;text-align:center;font-size:12px;color:#888;border-top:1px solid #e2e8f0}
  .btn{display:inline-block;padding:12px 28px;background:#1a3c5e;color:#fff !important;text-decoration:none;border-radius:6px;font-weight:bold;font-size:14px;margin:16px 0}
  .badge-success{display:inline-block;padding:4px 12px;background:#d1fae5;color:#065f46;border-radius:20px;font-size:13px;font-weight:bold}
  .badge-danger{display:inline-block;padding:4px 12px;background:#fee2e2;color:#991b1b;border-radius:20px;font-size:13px;font-weight:bold}
  .badge-auto{display:inline-block;padding:4px 12px;background:#dbeafe;color:#1e40af;border-radius:20px;font-size:13px;font-weight:bold}
  table.details{width:100%;border-collapse:collapse;margin:20px 0}
  table.details td{padding:10px 0;border-bottom:1px solid #f1f5f9;font-size:14px}
  table.details td:first-child{color:#666;width:45%}
  table.details td:last-child{font-weight:600}
  .amount-box{background:#f0fdf4;border:2px solid #86efac;border-radius:8px;padding:20px;text-align:center;margin:20px 0}
  .amount-box .amount{font-size:32px;font-weight:bold;color:#166534}
  .alert-box{background:#fef2f2;border:2px solid #fca5a5;border-radius:8px;padding:20px;margin:20px 0}
  .alert-box .alert-amount{font-size:28px;font-weight:bold;color:#991b1b}
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>{$appName}</h1>
    <p>Property Management System</p>
  </div>
  <div class="body">
    {$body}
  </div>
  <div class="footer">
    &copy; {$year} {$appName}. This is an automated message, please do not reply.<br>
    If you have questions, contact your property manager.
  </div>
</div>
</body>
</html>
HTML;
}
