<?php
namespace MSPGuild\Services;

use MSPGuild\Core\Database;
use MSPGuild\Email\EmailTemplates;

/**
 * Password Reset Service
 *
 * Flow:
 *  - request(): create token + email user (if account exists), return generic success.
 *  - validateToken(): returns user row if token is valid + not expired + not used.
 *  - resetWithToken(): updates password, marks token used, and clears sessions.
 */
class PasswordReset
{
    // 60 minutes feels sane for a portal
    private const EXPIRY_MINUTES = 60;

    // Basic throttle: max N requests per email per window (also per IP)
    private const THROTTLE_WINDOW_MINUTES = 15;
    private const THROTTLE_MAX_PER_EMAIL  = 3;
    private const THROTTLE_MAX_PER_IP     = 10;

    public static function request(string $email, string $ip = '', string $userAgent = ''): bool
    {
        $email = trim($email);
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Always behave the same to avoid email enumeration
            return true;
        }

        $pdo = Database::getConnection();

        // Find user (active)
        $stmt = $pdo->prepare("SELECT id, email, full_name, is_active FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Always return true to keep responses identical
        if (!$user || (int)($user['is_active'] ?? 1) !== 1) {
            return true;
        }

        // Throttle (per email + per IP)
        if (!self::withinThrottle($pdo, (int)$user['id'], $ip)) {
            return true;
        }

        // Create token
        $rawToken  = bin2hex(random_bytes(32)); // 64 chars
        $tokenHash = hash('sha256', $rawToken);

        $expiresAt = (new \DateTimeImmutable('now'))
            ->modify('+' . self::EXPIRY_MINUTES . ' minutes')
            ->format('Y-m-d H:i:s');

        $stmt = $pdo->prepare("
            INSERT INTO password_resets (user_id, token_hash, requested_ip, user_agent, expires_at)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            (int)$user['id'],
            $tokenHash,
            $ip !== '' ? $ip : null,
            $userAgent !== '' ? $userAgent : null,
            $expiresAt
        ]);

        $resetUrl = (defined('SITE_URL') ? SITE_URL : '') . '/reset_password.php?token=' . urlencode($rawToken);

        // Send email
        Mailer::send(EmailTemplates::PASSWORD_RESET, (string)$user['email'], [
            'full_name' => (string)($user['full_name'] ?? ''),
            'reset_url' => $resetUrl
        ]);

        return true;
    }

    public static function validateToken(string $rawToken): ?array
    {
        $rawToken = trim($rawToken);
        if ($rawToken === '' || strlen($rawToken) < 32) {
            return null;
        }

        $tokenHash = hash('sha256', $rawToken);

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT pr.id AS reset_id, pr.user_id, pr.expires_at, pr.used_at,
                   u.email, u.full_name, u.is_active
            FROM password_resets pr
            JOIN users u ON u.id = pr.user_id
            WHERE pr.token_hash = ?
            LIMIT 1
        ");
        $stmt->execute([$tokenHash]);
        $row = $stmt->fetch();

        if (!$row) return null;
        if ((int)($row['is_active'] ?? 1) !== 1) return null;
        if (!empty($row['used_at'])) return null;

        $now = new \DateTimeImmutable('now');
        $exp = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', (string)$row['expires_at']);
        if (!$exp || $exp < $now) return null;

        return $row;
    }

    public static function resetWithToken(string $rawToken, string $newPassword): bool
    {
        $newPassword = (string)$newPassword;
        if (strlen($newPassword) < 8) {
            return false;
        }

        $row = self::validateToken($rawToken);
        if (!$row) return false;

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ? LIMIT 1");
            $stmt->execute([$passwordHash, (int)$row['user_id']]);

            // Mark reset used
            $stmt = $pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ? LIMIT 1");
            $stmt->execute([(int)$row['reset_id']]);

            // Kill existing sessions (force re-login)
            // (sessions table exists in schema.sql)
            $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ?");
            $stmt->execute([(int)$row['user_id']]);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            return false;
        }
    }

    private static function withinThrottle($pdo, int $userId, string $ip): bool
    {
        // Email-based throttle
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM password_resets
            WHERE user_id = ?
              AND created_at >= (NOW() - INTERVAL " . self::THROTTLE_WINDOW_MINUTES . " MINUTE)
        ");
        $stmt->execute([$userId]);
        $countEmail = (int)$stmt->fetchColumn();
        if ($countEmail >= self::THROTTLE_MAX_PER_EMAIL) {
            return false;
        }

        if ($ip !== '') {
            $stmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM password_resets
                WHERE requested_ip = ?
                  AND created_at >= (NOW() - INTERVAL " . self::THROTTLE_WINDOW_MINUTES . " MINUTE)
            ");
            $stmt->execute([$ip]);
            $countIp = (int)$stmt->fetchColumn();
            if ($countIp >= self::THROTTLE_MAX_PER_IP) {
                return false;
            }
        }

        return true;
    }
}
