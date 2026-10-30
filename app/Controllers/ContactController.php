<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/ContactMessage.php';

final class ContactController
{
    public function __construct(private PDO $db) {}

    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php#contact-us');
            exit;
        }

        $success = false;
        $error = '';
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            $error = 'Your session expired. Refresh the page and send your message again.';
        } elseif (!is_authenticated()) {
            $error = 'Please sign in before sending a message so we can connect it to your account.';
        } else {
            $user = (new User($this->db))->findActiveById((int) $_SESSION['user_id']);
            if (!$user) {
                $_SESSION = [];
                session_regenerate_id(true);
                $error = 'Your account could not be verified. Please sign in again.';
            } else {
                $name = trim((string) ($_POST['name'] ?? ''));
                $email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
                $topic = trim((string) ($_POST['topic'] ?? ''));
                $message = trim((string) ($_POST['message'] ?? ''));
                $validTopics = ['Plant care advice', 'Order or delivery', 'Product question', 'Returns and exchanges', 'Something else'];

                if (app_length($name) < 2 || app_length($name) > 100 || !$email
                    || !in_array($topic, $validTopics, true) || app_length($message) < 5
                    || app_length($message) > 5000) {
                    $error = 'Please check the form and enter a valid name, email, topic, and message.';
                } else {
                    try {
                        $fullMessage = "From: {$name}\nEmail: {$email}\nTopic: {$topic}\n\n{$message}";
                        (new ContactMessage($this->db))->create((int) $user['user_id'], $fullMessage);
                        $success = true;
                    } catch (PDOException $exception) {
                        error_log('Nabta contact form database error: ' . $exception->getMessage());
                        $error = 'Your message could not be saved right now. Please try again.';
                    }
                }
            }
        }

        require dirname(__DIR__) . '/Views/contact/result.php';
    }
}
