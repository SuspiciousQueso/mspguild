<?php
namespace MSPGuild\Email;

/**
 * Central place to build email subjects + bodies.
 * Each template returns: ['subject' => string, 'html' => string, 'text' => string]
 */
class EmailTemplates
{
    public const REGISTRATION    = 'registration';
    public const PASSWORD_RESET  = 'password_reset';
    public const TICKET_CREATED  = 'ticket_created';
    public const TICKET_UPDATED  = 'ticket_updated';

    /**
     * @param string $type One of the template constants.
     * @param array $data Variables used in templates.
     */
    public static function build(string $type, array $data = []): array
    {
        $siteName = $data['site_name'] ?? (defined('SITE_NAME') ? SITE_NAME : 'MSPGuild');
        $supportEmail = $data['support_email'] ?? (defined('SUPPORT_EMAIL') ? SUPPORT_EMAIL : (defined('MAIL_FROM_EMAIL') ? MAIL_FROM_EMAIL : ''));

        // Normalize common fields
        $fullName = trim((string)($data['full_name'] ?? ''));
        $firstName = $fullName !== '' ? preg_split('/\s+/', $fullName)[0] : 'there';

        switch ($type) {
            case self::REGISTRATION: {
                $loginUrl = (string)($data['login_url'] ?? (defined('SITE_URL') ? SITE_URL . '/login.php' : ''));
                $subject  = "Welcome to {$siteName} — you're in";
                $htmlBody = self::wrapHtml($siteName, "Welcome, {$firstName}!", "
                    <p>Your account is ready. You can log in anytime using the link below.</p>
                    <p style=\"margin:18px 0;\"><a href=\"" . htmlspecialchars($loginUrl) . "\" style=\"display:inline-block;padding:10px 14px;border-radius:10px;background:#10b981;color:#0b1220;text-decoration:none;font-weight:600;\">Log in</a></p>
                    <p>If you didn't create this account, reply to this email or contact us at <a href=\"mailto:" . htmlspecialchars($supportEmail) . "\">" . htmlspecialchars($supportEmail) . "</a>.</p>
                ");
                $textBody = "Welcome, {$firstName}!\n\nYour account is ready. Log in here: {$loginUrl}\n\nIf you didn't create this account, contact {$supportEmail}.";
                return ['subject' => $subject, 'html' => $htmlBody, 'text' => $textBody];
            }

            case self::PASSWORD_RESET: {
                $resetUrl = (string)($data['reset_url'] ?? '');
                $subject  = "{$siteName} password reset";
                $htmlBody = self::wrapHtml($siteName, "Password reset", "
                    <p>We received a request to reset your password.</p>
                    <p style=\"margin:18px 0;\"><a href=\"" . htmlspecialchars($resetUrl) . "\" style=\"display:inline-block;padding:10px 14px;border-radius:10px;background:#60a5fa;color:#0b1220;text-decoration:none;font-weight:600;\">Reset password</a></p>
                    <p>If you didn't request this, you can ignore this email.</p>
                ");
                $textBody = "We received a request to reset your password.\n\nReset link: {$resetUrl}\n\nIf you didn't request this, ignore this email.";
                return ['subject' => $subject, 'html' => $htmlBody, 'text' => $textBody];
            }

            case self::TICKET_CREATED: {
                $ticketNumber = (string)($data['ticket_number'] ?? ('#' . ($data['ticket_id'] ?? '')));
                $ticketSubject = (string)($data['ticket_subject'] ?? 'Support Ticket');
                $ticketUrl = (string)($data['ticket_url'] ?? '');
                $priority = (string)($data['priority'] ?? 'medium');

                $subject = "[{$siteName}] Ticket created {$ticketNumber} — {$ticketSubject}";
                $htmlBody = self::wrapHtml($siteName, "Ticket created: {$ticketNumber}", "
                    <p><strong>Subject:</strong> " . htmlspecialchars($ticketSubject) . "</p>
                    <p><strong>Priority:</strong> " . htmlspecialchars($priority) . "</p>
                    <p style=\"margin:18px 0;\"><a href=\"" . htmlspecialchars($ticketUrl) . "\" style=\"display:inline-block;padding:10px 14px;border-radius:10px;background:#f59e0b;color:#0b1220;text-decoration:none;font-weight:600;\">View ticket</a></p>
                    <p>We'll follow up as soon as we can.</p>
                ");
                $textBody = "Ticket created: {$ticketNumber}\nSubject: {$ticketSubject}\nPriority: {$priority}\nView: {$ticketUrl}";
                return ['subject' => $subject, 'html' => $htmlBody, 'text' => $textBody];
            }

            case self::TICKET_UPDATED: {
                $ticketNumber = (string)($data['ticket_number'] ?? ('#' . ($data['ticket_id'] ?? '')));
                $ticketSubject = (string)($data['ticket_subject'] ?? 'Support Ticket');
                $ticketUrl = (string)($data['ticket_url'] ?? '');
                $comment = trim((string)($data['comment'] ?? ''));
                $commentShort = $comment !== '' ? mb_substr($comment, 0, 700) : '';

                $subject = "[{$siteName}] Ticket updated {$ticketNumber} — {$ticketSubject}";
                $htmlComment = $commentShort !== '' ? ("<div style=\"margin-top:12px;padding:12px;border-radius:12px;background:#0f172a;color:#e5e7eb;\"><div style=\"font-size:12px;color:#94a3b8;margin-bottom:6px;\">Latest update</div><div style=\"white-space:pre-wrap;\">" . htmlspecialchars($commentShort) . "</div></div>") : '';

                $htmlBody = self::wrapHtml($siteName, "Ticket updated: {$ticketNumber}", "
                    <p><strong>Subject:</strong> " . htmlspecialchars($ticketSubject) . "</p>
                    {$htmlComment}
                    <p style=\"margin:18px 0;\"><a href=\"" . htmlspecialchars($ticketUrl) . "\" style=\"display:inline-block;padding:10px 14px;border-radius:10px;background:#a78bfa;color:#0b1220;text-decoration:none;font-weight:600;\">View ticket</a></p>
                ");
                $textBody = "Ticket updated: {$ticketNumber}\nSubject: {$ticketSubject}\n\nLatest update:\n{$commentShort}\n\nView: {$ticketUrl}";
                return ['subject' => $subject, 'html' => $htmlBody, 'text' => $textBody];
            }

            default:
                return [
                    'subject' => "{$siteName} notification",
                    'html'    => self::wrapHtml($siteName, 'Notification', '<p>Notification</p>'),
                    'text'    => 'Notification',
                ];
        }
    }

    private static function wrapHtml(string $siteName, string $headline, string $contentHtml): string
    {
        $siteNameEsc = htmlspecialchars($siteName);
        $headlineEsc = htmlspecialchars($headline);

        return "<!doctype html>
<html>
<head>
<meta charset=\"utf-8\" />
<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />
</head>
<body style=\"margin:0;background:#0b1220;font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;\">
  <div style=\"max-width:640px;margin:0 auto;padding:24px;\">
    <div style=\"padding:18px 18px 10px;border-radius:18px;background:#0f172a;border:1px solid #1f2937;\">
      <div style=\"font-size:12px;color:#94a3b8;letter-spacing:.12em;text-transform:uppercase;\">{$siteNameEsc}</div>
      <div style=\"margin-top:8px;font-size:20px;font-weight:700;color:#e5e7eb;\">{$headlineEsc}</div>
      <div style=\"margin-top:14px;font-size:14px;line-height:1.55;color:#cbd5e1;\">
        {$contentHtml}
      </div>
      <div style=\"margin-top:18px;padding-top:14px;border-top:1px solid #1f2937;font-size:12px;color:#64748b;\">
        This is an automated message. If you need help, reply to this email.
      </div>
    </div>
  </div>
</body>
</html>";
    }
}
