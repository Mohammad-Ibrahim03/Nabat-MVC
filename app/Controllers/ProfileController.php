<?php
declare(strict_types=1);
require_once __DIR__ . '/../Models/User.php';

final class ProfileController
{
    public function __construct(private PDO $db) {}

    public function index(): void
    {
        require_authenticated_user();
        $users = new User($this->db);
        $user = $users->findActiveById((int) $_SESSION['user_id']);
        $error = '';
        if (!$user) {
            $_SESSION = [];
            session_regenerate_id(true);
            header('Location: index.php?route=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                http_response_code(419);
                $error = 'Your session expired. Refresh the page and try again.';
            } else {
                $name = trim((string) ($_POST['full_name'] ?? ''));
                $phone = trim((string) ($_POST['phone'] ?? ''));
                $address = trim((string) ($_POST['address'] ?? ''));
                if (app_length($name) < 2 || app_length($name) > 100) {
                    $error = 'Enter a name using 2 to 100 characters.';
                } elseif ($phone !== '' && preg_match('/^[0-9+()\-\s.]{6,30}$/', $phone) !== 1) {
                    $error = 'Enter a valid phone number.';
                } elseif (app_length($address) > 255) {
                    $error = 'Your address is too long.';
                } else {
                    $users->updateProfile((int) $user['user_id'], $name, $phone !== '' ? $phone : null, $address !== '' ? $address : null);
                    $_SESSION['full_name'] = $name;
                    $_SESSION['flash_success'] = 'Your profile was updated.';
                    header('Location: index.php?route=profile');
                    exit;
                }
                $user = array_merge($user, ['full_name' => $name, 'phone' => $phone, 'address' => $address]);
            }
        }
        $pageTitle = 'Your account';
        require dirname(__DIR__) . '/Views/profile.php';
    }
}
