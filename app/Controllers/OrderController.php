<?php
declare(strict_types=1);
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Core/Store.php';

final class OrderController
{
    public function __construct(private PDO $db) {}

    public function checkout(): void
    {
        require_authenticated_user();
        $items = store_cart_items($this->db);
        $subtotal = store_cart_subtotal($items);
        $deliveryFee = $items ? 3.00 : 0.00;
        $user = (new User($this->db))->findActiveById((int) $_SESSION['user_id']);
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                http_response_code(419);
                $error = 'Your session expired. Refresh checkout and try again.';
            } elseif (!$items) {
                $error = 'Your basket is empty. Add something before checkout.';
            } else {
                $address = trim((string) ($_POST['address'] ?? ''));
                $payment = (string) ($_POST['payment_method'] ?? 'cash_on_delivery');
                if (app_length($address) < 5 || app_length($address) > 255) {
                    $error = 'Enter a complete delivery address (5–255 characters).';
                } elseif ($payment !== 'cash_on_delivery') {
                    $error = 'Only cash on delivery is available in this project version.';
                } else {
                    try {
                        $this->db->beginTransaction();
                        $freshItems = [];
                        $freshSubtotal = 0.0;
                        $stockQuery = $this->db->prepare("SELECT pro_id, name, price, quantity FROM product WHERE pro_id = :id AND status = 'active' FOR UPDATE");
                        foreach ($items as $item) {
                            $stockQuery->execute(['id' => (int) $item['pro_id']]);
                            $fresh = $stockQuery->fetch();
                            $quantity = (int) $item['cart_quantity'];
                            if (!$fresh || (int) $fresh['quantity'] < $quantity) {
                                throw new RuntimeException('Some products are no longer available in the requested quantity. Please review your basket.');
                            }
                            $fresh['cart_quantity'] = $quantity;
                            $fresh['line_total'] = (float) $fresh['price'] * $quantity;
                            $freshItems[] = $fresh;
                            $freshSubtotal += $fresh['line_total'];
                        }
                        $total = $freshSubtotal + $deliveryFee;
                        $stmt = $this->db->prepare("INSERT INTO cart (user_id, delivery_fee, delivery_address_at_order, payment_method, total_price, status) VALUES (:user_id, :delivery_fee, :address, :payment, :total, 'pending')");
                        $stmt->execute([
                            'user_id' => (int) $_SESSION['user_id'],
                            'delivery_fee' => $deliveryFee,
                            'address' => $address,
                            'payment' => $payment,
                            'total' => $total,
                        ]);
                        $cartId = (int) $this->db->lastInsertId();
                        $itemInsert = $this->db->prepare('INSERT INTO cart_items (cart_id, pro_id, quantity, unit_price_at_purchase) VALUES (:cart_id, :pro_id, :quantity, :price)');
                        $stockUpdate = $this->db->prepare('UPDATE product SET quantity = quantity - :quantity_deduct, count_selling = count_selling + :quantity_sold WHERE pro_id = :id');
                        foreach ($freshItems as $fresh) {
                            $itemInsert->execute(['cart_id' => $cartId, 'pro_id' => (int) $fresh['pro_id'], 'quantity' => (int) $fresh['cart_quantity'], 'price' => (float) $fresh['price']]);
                            $stockUpdate->execute(['quantity_deduct' => (int) $fresh['cart_quantity'], 'quantity_sold' => (int) $fresh['cart_quantity'], 'id' => (int) $fresh['pro_id']]);
                        }
                        $this->db->commit();
                        $_SESSION['cart'] = [];
                        $_SESSION['last_order_id'] = $cartId;
                        header('Location: index.php?route=orders&placed=' . $cartId);
                        exit;
                    } catch (Throwable $exception) {
                        if ($this->db->inTransaction()) $this->db->rollBack();
                        if ($exception instanceof RuntimeException) {
                            $error = $exception->getMessage();
                        } else {
                            error_log('Nabta checkout error: ' . $exception->getMessage());
                            $error = 'We could not place your order right now. Please try again.';
                        }
                    }
                }
            }
        }

        $pageTitle = 'Checkout';
        require dirname(__DIR__) . '/Views/checkout.php';
    }

    public function history(): void
    {
        require_authenticated_user();
        $stmt = $this->db->prepare('SELECT cart_id, created_at, total_price, status, delivery_address_at_order, payment_method FROM cart WHERE user_id = :user_id ORDER BY created_at DESC, cart_id DESC');
        $stmt->execute(['user_id' => (int) $_SESSION['user_id']]);
        $orders = $stmt->fetchAll();
        $detailStmt = $this->db->prepare('SELECT ci.quantity, ci.unit_price_at_purchase, p.name FROM cart_items ci LEFT JOIN product p ON p.pro_id = ci.pro_id WHERE ci.cart_id = :cart_id');
        foreach ($orders as &$order) {
            $detailStmt->execute(['cart_id' => (int) $order['cart_id']]);
            $order['items'] = $detailStmt->fetchAll();
        }
        unset($order);
        $pageTitle = 'Your orders';
        require dirname(__DIR__) . '/Views/orders.php';
    }
}
