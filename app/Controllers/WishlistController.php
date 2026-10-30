<?php
declare(strict_types=1);
require_once __DIR__ . '/../Models/Product.php';

final class WishlistController
{
    public function __construct(private PDO $db) {}

    public function handlePost(): void
    {
        require_authenticated_user();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            header('Location: index.php?route=wishlist');
            exit;
        }
        $id = (int) ($_POST['pro_id'] ?? 0);
        $check = $this->db->prepare('SELECT wishlist_id FROM wishlist WHERE user_id = :user_id AND pro_id = :pro_id LIMIT 1');
        $check->execute(['user_id' => (int) $_SESSION['user_id'], 'pro_id' => $id]);
        $existing = $check->fetchColumn();
        if ($existing) {
            $delete = $this->db->prepare('DELETE FROM wishlist WHERE wishlist_id = :id AND user_id = :user_id');
            $delete->execute(['id' => (int) $existing, 'user_id' => (int) $_SESSION['user_id']]);
        } else {
            $product = (new Product($this->db))->findById($id);
            if ($product) {
                $insert = $this->db->prepare('INSERT INTO wishlist (user_id, pro_id) VALUES (:user_id, :pro_id)');
                $insert->execute(['user_id' => (int) $_SESSION['user_id'], 'pro_id' => $id]);
            }
        }
        $returnTo = (string) ($_POST['return_to'] ?? 'index.php?route=wishlist');
        if (!preg_match('/^index\.php\?route=(wishlist|product)(?:&id=\d+)?$/', $returnTo)) {
            $returnTo = 'index.php?route=wishlist';
        }
        header('Location: ' . $returnTo);
        exit;
    }

    public function index(): void
    {
        require_authenticated_user();
        $stmt = $this->db->prepare("SELECT p.pro_id, p.cat_id, p.name, p.description, p.price, p.quantity, p.status, c.name AS category_name, (SELECT pi.image_url FROM product_images pi WHERE pi.pro_id = p.pro_id ORDER BY pi.is_primary DESC LIMIT 1) AS image_url FROM wishlist w JOIN product p ON p.pro_id = w.pro_id LEFT JOIN categories c ON c.cat_id = p.cat_id WHERE w.user_id = :user_id AND p.status = 'active' ORDER BY w.wishlist_id DESC");
        $stmt->execute(['user_id' => (int) $_SESSION['user_id']]);
        $products = $stmt->fetchAll();
        $pageTitle = 'Your wishlist';
        require dirname(__DIR__) . '/Views/wishlist.php';
    }
}
