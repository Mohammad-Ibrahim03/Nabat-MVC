<?php
declare(strict_types=1);

final class ContactMessage
{
    public function __construct(private PDO $db) {}

    public function create(int $userId, string $message): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO contact_us (user_id, message, status) VALUES (:user_id, :message, 'new')"
        );
        $stmt->execute(['user_id' => $userId, 'message' => $message]);
    }
}
