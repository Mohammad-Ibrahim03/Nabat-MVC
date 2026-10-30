<?php
declare(strict_types=1);
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Core/Store.php';

final class CartController
{
    public function __construct(private PDO $db) {}

    public function handlePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            $_SESSION['flash_error'] = 'Your session expired. Please try again.';
            header('Location: index.php?route=cart');
            exit;
        }

        $action = (string) ($_POST['action'] ?? 'add');
        $id = (int) ($_POST['pro_id'] ?? 0);
        $basket = $_SESSION['cart'] ?? [];
        if (!is_array($basket)) $basket = [];

        if ($action === 'remove') {
            unset($basket[$id]);
        } elseif ($action === 'update') {
            $quantity = (int) ($_POST['quantity'] ?? 1);
            if ($quantity < 1) {
                unset($basket[$id]);
            } else {
                $product = (new Product($this->db))->findById($id);
                if (!$product) {
                    unset($basket[$id]);
                } else {
                    $basket[$id] = min(99, (int) $product['quantity'], max(1, $quantity));
                }
            }
        } else {
            $product = (new Product($this->db))->findById($id);
            if ($product && (int) $product['quantity'] > 0) {
                $current = (int) ($basket[$id] ?? 0);
                $basket[$id] = min((int) $product['quantity'], 99, $current + max(1, (int) ($_POST['quantity'] ?? 1)));
                $_SESSION['flash_success'] = 'Added to your basket.';
            } else {
                $_SESSION['flash_error'] = 'This product is currently unavailable.';
            }
        }
        $_SESSION['cart'] = $basket;
        header('Location: index.php?route=cart');
        exit;
    }

    public function index(): void
    {
        $items = store_cart_items($this->db);
        $subtotal = store_cart_subtotal($items);
        $pageTitle = 'Your basket';
        require dirname(__DIR__) . '/Views/cart.php';
    }
}
