<?php
namespace MSPGuild\Services;

use MSPGuild\Email\EmailTemplates;

/**
 * One function to rule them all: Mailer::send($type, $to, $data)
 *
 * Supports:
 *  - MAIL_DRIVER=mail (default): PHP mail()
 *  - MAIL_DRIVER=smtp: PHPMailer if installed via composer
 */
class Mailer
{
    /**
     * @param string $type EmailTemplates::* constant string
     * @param string|array $to Email address or array of emails
     * @param array $data Variables for templates
     */
    public static function send(string $type, string|array $to, array $data = []): bool
    {
        $data['site_name'] = $data['site_name'] ?? (defined('SITE_NAME') ? SITE_NAME : 'MSPGuild');
        $data['support_email'] = $data['support_email'] ?? (defined('SUPPORT_EMAIL') ? SUPPORT_EMAIL : (defined('MAIL_FROM_EMAIL') ? MAIL_FROM_EMAIL : ''));

        $tpl = EmailTemplates::build($type, $data);

        $toList = is_array($to) ? $to : [$to];
        $okAll = true;

        foreach ($toList as $recipient) {
            $recipient = trim((string)$recipient);
            if ($recipient === '') continue;

            $ok = self::sendRaw(
                $recipient,
                (string)$tpl['subject'],
                (string)$tpl['html'],
                (string)$tpl['text']
            );

            if (!$ok) $okAll = false;
        }

        return $okAll;
    }

    private static function sendRaw(string $to, string $subject, string $html, string $text): bool
    {
        $fromEmail = defined('MAIL_FROM_EMAIL') ? MAIL_FROM_EMAIL : (defined('SUPPORT_EMAIL') ? SUPPORT_EMAIL : 'support@example.com');
        $fromName  = defined('MAIL_FROM_NAME')  ? MAIL_FROM_NAME  : (defined('SITE_NAME') ? SITE_NAME : 'MSPGuild');

        $driver = defined('MAIL_DRIVER') ? MAIL_DRIVER : 'mail';

        // SMTP via PHPMailer if available
        if ($driver === 'smtp' && class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->CharSet = 'UTF-8';

                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->Port = SMTP_PORT;

                $mail->SMTPAuth = SMTP_AUTH;
                if (SMTP_AUTH) {
                    $mail->Username = SMTP_USER;
                    $mail->Password = SMTP_PASS;
                }

                if (SMTP_SECURE !== '') {
                    $mail->SMTPSecure = SMTP_SECURE;
                }

                $mail->setFrom($fromEmail, $fromName);
                $mail->addAddress($to);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $html;
                $mail->AltBody = $text;

                return $mail->send();
            } catch (\Throwable $e) {
                error_log('[Mailer] SMTP send failed: ' . $e->getMessage());
                return false;
            }
        }

        // Fallback: PHP mail() (best effort). Many VPS need a local MTA or relay.
        $boundary = 'bnd_' . bin2hex(random_bytes(12));
        $headers  = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'From: ' . self::formatFrom($fromName, $fromEmail);
        $headers[] = 'Reply-To: ' . $fromEmail;
        $headers[] = 'Content-Type: multipart/alternative; boundary=' . $boundary;

        $body  = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $body .= quoted_printable_encode($text) . "\r\n\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $body .= quoted_printable_encode($html) . "\r\n\r\n";
        $body .= "--{$boundary}--\r\n";

        $headersStr = implode("\r\n", $headers);

        // Note: mail() returns bool but doesn't guarantee delivery.
        return @mail($to, $subject, $body, $headersStr);
    }

    private static function formatFrom(string $name, string $email): string
    {
        $name = trim($name);
        if ($name === '') return $email;

        // Escape quotes
        $safeName = str_replace('"', "'", $name);
        return '"' . $safeName . '" <' . $email . '>';
    }
}
