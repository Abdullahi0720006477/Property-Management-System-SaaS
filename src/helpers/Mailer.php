<?php
/**
 * Mailer — wraps PHPMailer, configured for Mailpit (dev) or SMTP (prod)
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        require_once ROOT_PATH . '/vendor/autoload.php';

        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host       = $_ENV['MAIL_HOST']       ?? 'localhost';
        $this->mail->Port       = (int)($_ENV['MAIL_PORT'] ?? 1025);
        $this->mail->SMTPAuth   = filter_var($_ENV['MAIL_AUTH'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->mail->Username   = $_ENV['MAIL_USERNAME']   ?? '';
        $this->mail->Password   = $_ENV['MAIL_PASSWORD']   ?? '';
        $this->mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? '';
        $this->mail->CharSet    = 'UTF-8';
        $this->mail->isHTML(true);

        $fromEmail = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@propertymanagement.test';
        $fromName  = $_ENV['MAIL_FROM_NAME']    ?? APP_NAME;
        $this->mail->setFrom($fromEmail, $fromName);
    }

    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, string $plainText = ''): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $toName);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $htmlBody;
            $this->mail->AltBody = $plainText ?: strip_tags($htmlBody);
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mailer error: ' . $this->mail->ErrorInfo);
            return false;
        }
    }

    // ── Convenience senders ────────────────────────────────────────────────

    public static function sendPaymentReceipt(array $payment): bool
    {
        if (empty($payment['tenant_email'])) return false;
        $mailer = new self();
        $html   = self::renderTemplate('payment_receipt', $payment);
        return $mailer->send(
            $payment['tenant_email'],
            $payment['tenant_name'],
            'Payment Receipt #' . str_pad($payment['id'], 6, '0', STR_PAD_LEFT) . ' — ' . APP_NAME,
            $html
        );
    }

    public static function sendOverdueNotice(array $payment): bool
    {
        if (empty($payment['tenant_email'])) return false;
        $mailer = new self();
        $html   = self::renderTemplate('overdue_notice', $payment);
        return $mailer->send(
            $payment['tenant_email'],
            $payment['tenant_name'],
            'Overdue Rent Notice — ' . APP_NAME,
            $html
        );
    }

    public static function sendLeaseExpiryWarning(array $lease, int $daysLeft): bool
    {
        if (empty($lease['tenant_email'])) return false;
        $mailer = new self();
        $html   = self::renderTemplate('lease_expiry', array_merge($lease, ['days_left' => $daysLeft]));
        return $mailer->send(
            $lease['tenant_email'],
            $lease['tenant_name'],
            'Lease Expiry Notice (' . $daysLeft . ' days left) — ' . APP_NAME,
            $html
        );
    }

    public static function sendWelcome(array $user, string $plainPassword): bool
    {
        if (empty($user['email'])) return false;
        $mailer = new self();
        $html   = self::renderTemplate('welcome', array_merge($user, ['plain_password' => $plainPassword]));
        return $mailer->send(
            $user['email'],
            $user['full_name'],
            'Welcome to ' . APP_NAME,
            $html
        );
    }

    // ── Template renderer ─────────────────────────────────────────────────

    private static function renderTemplate(string $name, array $data): string
    {
        $file = VIEWS_PATH . '/emails/' . $name . '.php';
        if (!file_exists($file)) return '<p>No template found for: ' . htmlspecialchars($name) . '</p>';

        extract($data, EXTR_SKIP);
        ob_start();
        include $file;
        return ob_get_clean();
    }
}
