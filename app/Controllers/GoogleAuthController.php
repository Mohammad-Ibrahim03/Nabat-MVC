<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/User.php';

final class GoogleAuthController
{
    private User $users;

    public function __construct(PDO $db)
    {
        $this->users = new User($db);
    }

    public function authenticate(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, private');
        header('X-Content-Type-Options: nosniff');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(405, ['ok' => false, 'message' => 'Use the Google sign-up button on the registration page.']);
        }
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $this->respond(419, ['ok' => false, 'message' => 'Your session expired. Refresh this page and try again.']);
        }

        $clientId = google_client_id();
        if ($clientId === '') {
            $this->respond(503, ['ok' => false, 'message' => 'Google sign-up is not connected yet.']);
        }

        $credential = trim((string) ($_POST['credential'] ?? ''));
        if ($credential === '' || strlen($credential) > 12000) {
            $this->respond(400, ['ok' => false, 'message' => 'Google did not return a valid sign-up credential. Please try again.']);
        }

        $claims = $this->verifyWithGoogle($credential);
        if (!$claims
            || !in_array((string) ($claims['iss'] ?? ''), ['accounts.google.com', 'https://accounts.google.com'], true)
            || !hash_equals($clientId, (string) ($claims['aud'] ?? ''))
            || (isset($claims['azp']) && !hash_equals($clientId, (string) $claims['azp']))
            || (int) ($claims['exp'] ?? 0) <= time()
            || trim((string) ($claims['sub'] ?? '')) === '') {
            $this->respond(401, ['ok' => false, 'message' => 'Google sign-up could not be verified. Please try again.']);
        }

        $email = app_lower(trim((string) ($claims['email'] ?? '')));
        $verified = ($claims['email_verified'] ?? false) === true
            || (string) ($claims['email_verified'] ?? '') === 'true';
        if (!$verified || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respond(403, ['ok' => false, 'message' => 'Use a Google account with a verified email address.']);
        }

        // Google is authoritative for Gmail addresses and for verified Workspace domains.
        $emailDomain = strtolower((string) substr(strrchr($email, '@') ?: '', 1));
        $hostedDomain = strtolower(trim((string) ($claims['hd'] ?? '')));
        $googleControlsEmail = str_ends_with($email, '@gmail.com')
            || ($hostedDomain !== '' && hash_equals($hostedDomain, $emailDomain));
        if (!$googleControlsEmail) {
            $this->respond(403, ['ok' => false, 'message' => 'For security, use a Gmail address or a verified Google Workspace account.']);
        }

        $googleSub = (string) $claims['sub'];
        $user = $this->users->findByGoogleSub($googleSub);

        if (!$user) {
            $user = $this->users->findByEmail($email);
            if ($user) {
                if (!empty($user['google_sub']) && !hash_equals((string) $user['google_sub'], $googleSub)) {
                    $this->respond(409, ['ok' => false, 'message' => 'This email is already linked to a different Google account. Sign in using your existing method.']);
                }
                if ($user['status'] !== 'active') {
                    $this->respond(403, ['ok' => false, 'message' => 'This account is currently unavailable. Please contact the site administrator.']);
                }
                if (empty($user['google_sub'])) {
                    try {
                        $this->users->linkGoogleSub((int) $user['user_id'], $googleSub);
                        $user = $this->users->findByEmail($email);
                        if (!$user || !hash_equals((string) ($user['google_sub'] ?? ''), $googleSub)) {
                            $this->respond(409, ['ok' => false, 'message' => 'This Google account is already linked to another user.']);
                        }
                    } catch (PDOException $exception) {
                        if ($exception->getCode() !== '23000') {
                            throw $exception;
                        }
                        $user = $this->users->findByGoogleSub($googleSub);
                        if (!$user) {
                            $this->respond(409, ['ok' => false, 'message' => 'This Google account is already linked to another user.']);
                        }
                    }
                }
            } else {
                $name = trim((string) ($claims['name'] ?? ''));
                $name = preg_replace('/\s+/u', ' ', $name) ?: '';
                if ($name === '') {
                    $name = (string) strstr($email, '@', true);
                }
                if (app_length($name) > 100) {
                    $name = function_exists('mb_substr') ? mb_substr($name, 0, 100, 'UTF-8') : substr($name, 0, 100);
                }

                try {
                    $this->users->createGoogleAccount($name, $email, $googleSub);
                    $user = $this->users->findByGoogleSub($googleSub);
                } catch (PDOException $exception) {
                    // Recover gracefully if two requests tried to create the same account at once.
                    if ($exception->getCode() !== '23000') {
                        throw $exception;
                    }
                    $user = $this->users->findByGoogleSub($googleSub) ?? $this->users->findByEmail($email);
                    if ($user && empty($user['google_sub'])) {
                        $this->users->linkGoogleSub((int) $user['user_id'], $googleSub);
                        $user['google_sub'] = $googleSub;
                    }
                }
            }
        }

        if (!$user || $user['status'] !== 'active') {
            $this->respond(403, ['ok' => false, 'message' => 'This account is currently unavailable. Please contact the site administrator.']);
        }
        if (!empty($user['google_sub']) && !hash_equals((string) $user['google_sub'], $googleSub)) {
            $this->respond(409, ['ok' => false, 'message' => 'This email is already linked to a different Google account.']);
        }

        session_regenerate_id(true);
        unset($_SESSION['_login_attempts'], $_SESSION['_login_window_start']);
        $_SESSION['user_id'] = (int) $user['user_id'];
        $_SESSION['full_name'] = (string) $user['full_name'];
        $_SESSION['user_email'] = (string) $user['email'];
        $_SESSION['role'] = (string) $user['role'];
        $_SESSION['auth_time'] = time();
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));

        $this->respond(200, ['ok' => true, 'redirect' => 'index.php']);
    }

    /**
     * Uses Google's tokeninfo endpoint to verify the token signature and claims.
     * This is convenient for a local MVP; Google's production guidance recommends
     * validating with the Google API Client Library instead of relying on tokeninfo.
     */
    private function verifyWithGoogle(string $credential): ?array
    {
        $body = http_build_query(['id_token' => $credential], '', '&', PHP_QUERY_RFC3986);
        $status = 0;
        $response = false;

        if (function_exists('curl_init')) {
            $ch = curl_init('https://oauth2.googleapis.com/tokeninfo');
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded', 'Accept: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 4,
                CURLOPT_TIMEOUT => 8,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);
            $response = curl_exec($ch);
            if ($response !== false) {
                $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            }
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\nAccept: application/json\r\n",
                    'content' => $body,
                    'timeout' => 8,
                    'ignore_errors' => true,
                ],
                'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
            ]);
            $response = @file_get_contents('https://oauth2.googleapis.com/tokeninfo', false, $context);
            foreach (($http_response_header ?? []) as $header) {
                if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
                    $status = (int) $matches[1];
                }
            }
        }

        if ($response === false || $status !== 200) {
            return null;
        }
        $claims = json_decode((string) $response, true);
        return is_array($claims) ? $claims : null;
    }

    private function respond(int $status, array $payload): never
    {
        http_response_code($status);
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
