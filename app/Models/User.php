<?php
declare(strict_types=1);

final class User
{
    public function __construct(private PDO $db) {}

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT user_id, full_name, email, password, phone, address, role, status, google_sub FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => app_lower(trim($email))]);
        return $stmt->fetch() ?: null;
    }

    public function findByGoogleSub(string $googleSub): ?array
    {
        $stmt = $this->db->prepare('SELECT user_id, full_name, email, password, phone, address, role, status, google_sub FROM users WHERE google_sub = :sub LIMIT 1');
        $stmt->execute(['sub' => $googleSub]);
        return $stmt->fetch() ?: null;
    }

    public function findActiveById(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT user_id, full_name, email, phone, address, role, status FROM users WHERE user_id = :id AND status = 'active' LIMIT 1");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $fullName, string $email, ?string $phone, string $passwordHash): int
    {
        $stmt = $this->db->prepare("INSERT INTO users (full_name, email, password, phone, role, status) VALUES (:name, :email, :password, :phone, 'customer', 'active')");
        $stmt->execute([
            'name' => $fullName,
            'email' => app_lower(trim($email)),
            'password' => $passwordHash,
            'phone' => $phone !== '' ? $phone : null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updatePasswordHash(int $userId, string $passwordHash): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password = :password WHERE user_id = :id');
        $stmt->execute(['password' => $passwordHash, 'id' => $userId]);
    }

    public function linkGoogleSub(int $userId, string $googleSub): void
    {
        $stmt = $this->db->prepare('UPDATE users SET google_sub = :sub WHERE user_id = :id');
        $stmt->execute(['sub' => $googleSub, 'id' => $userId]);
    }

    public function createGoogleAccount(string $name, string $email, string $googleSub): int
    {
        $stmt = $this->db->prepare("INSERT INTO users (full_name, email, password, google_sub, role, status) VALUES (:name, :email, :password, :sub, 'customer', 'active')");
        $stmt->execute([
            'name' => $name,
            'email' => app_lower(trim($email)),
            'password' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
            'sub' => $googleSub,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $userId, string $name, ?string $phone, ?string $address): void
    {
        $stmt = $this->db->prepare('UPDATE users SET full_name = :name, phone = :phone, address = :address WHERE user_id = :id');
        $stmt->execute(['name' => $name, 'phone' => $phone, 'address' => $address, 'id' => $userId]);
    }
}
