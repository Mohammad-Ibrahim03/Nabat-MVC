<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/User.php';

final class AuthController
{
    private User $users;

    public function __construct(PDO $db)
    {
        $this->users = new User($db);
    }

    public function login(): void
    {
        if (is_authenticated()) {
            header('Location: index.php');
            exit;
        }

        $error = '';
        $pageTitle = 'Sign in';
        $isRegister = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                http_response_code(419);
                $error = 'Your session expired. Refresh this page and try again.';
            } else {
                $rawEmail = trim((string) ($_POST['email'] ?? ''));
                $password = (string) ($_POST['password'] ?? '');
                $email = filter_var($rawEmail, FILTER_VALIDATE_EMAIL);
                $attempts = (int) ($_SESSION['_login_attempts'] ?? 0);
                $windowStart = (int) ($_SESSION['_login_window_start'] ?? time());

                if (time() - $windowStart > 600) {
                    $attempts = 0;
                    $windowStart = time();
                }
                $_SESSION['_login_window_start'] = $windowStart;

                // A simple per-session throttle is supplemental protection; production should also rate-limit at the server/proxy.
                if ($attempts >= 8) {
                    $error = 'We couldn’t sign you in with those details. Check your email and password, then try again later.';
                } else {
                    $user = $email ? $this->users->findByEmail(app_lower((string) $email)) : null;
                    if ($user && $user['status'] === 'active' && $password !== '' && password_verify($password, (string) $user['password'])) {
                        if (password_needs_rehash((string) $user['password'], PASSWORD_DEFAULT)) {
                            $this->users->updatePasswordHash((int) $user['user_id'], password_hash($password, PASSWORD_DEFAULT));
                        }
                        session_regenerate_id(true);
                        unset($_SESSION['_login_attempts'], $_SESSION['_login_window_start']);
                        $_SESSION['user_id'] = (int) $user['user_id'];
                        $_SESSION['full_name'] = (string) $user['full_name'];
                        $_SESSION['user_email'] = (string) $user['email'];
                        $_SESSION['role'] = (string) $user['role'];
                        $_SESSION['auth_time'] = time();
                        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
                        header('Location: index.php');
                        exit;
                    }

                    $_SESSION['_login_attempts'] = $attempts + 1;
                    // Deliberately identical for unknown emails, wrong passwords, and inactive accounts.
                    $error = 'We couldn’t sign you in with those details. Check your email and password.';
                }
            }
        }

        require dirname(__DIR__) . '/Views/auth/login.php';
    }

    public function register(): void
    {
        if (is_authenticated()) {
            header('Location: index.php');
            exit;
        }

        $error = '';
        $pageTitle = 'Create account';
        $isRegister = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                http_response_code(419);
                $error = 'Your session expired. Refresh this page and try again.';
            } else {
                $name = trim((string) ($_POST['full_name'] ?? ''));
                $rawEmail = app_lower(trim((string) ($_POST['email'] ?? '')));
                $email = filter_var($rawEmail, FILTER_VALIDATE_EMAIL);
                $phone = trim((string) ($_POST['phone'] ?? ''));
                $password = (string) ($_POST['password'] ?? '');
                $confirm = (string) ($_POST['confirm_password'] ?? '');

                if (app_length($name) < 2 || app_length($name) > 100) {
                    $error = 'Enter your name using 2 to 100 characters.';
                } elseif (!$email || app_length($rawEmail) > 150) {
                    $error = 'Enter a valid email address.';
                } elseif (app_length($phone) > 30 || ($phone !== '' && preg_match('/^[0-9+()\-\s.]{6,30}$/', $phone) !== 1)) {
                    $error = 'Enter a valid phone number, or leave the field empty.';
                } elseif (!password_policy_passes($password)) {
                    $error = 'Your password must have at least 8 characters, a letter, a number, and a symbol.';
                } elseif (!hash_equals($password, $confirm)) {
                    $error = 'The passwords do not match. Please check both fields.';
                } else {
                    try {
                        if ($this->users->findByEmail($rawEmail)) {
                            $error = 'An account could not be created with those details. Try signing in or use another email.';
                        } else {
                            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                            $userId = $this->users->create($name, $rawEmail, $phone, $passwordHash);
                            session_regenerate_id(true);
                            $_SESSION['user_id'] = $userId;
                            $_SESSION['full_name'] = $name;
                            $_SESSION['user_email'] = $rawEmail;
                            $_SESSION['role'] = 'customer';
                            $_SESSION['auth_time'] = time();
                            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
                            header('Location: index.php');
                            exit;
                        }
                    } catch (PDOException $exception) {
                        // Handle concurrent duplicate submissions without leaking database details.
                        if ($exception->getCode() === '23000') {
                            $error = 'An account could not be created with those details. Try signing in or use another email.';
                        } else {
                            error_log('Nabta registration database error: ' . $exception->getMessage());
                            $error = 'We couldn’t create your account right now. Please try again.';
                        }
                    }
                }
            }
        }

        require dirname(__DIR__) . '/Views/auth/register.php';
    }
}
