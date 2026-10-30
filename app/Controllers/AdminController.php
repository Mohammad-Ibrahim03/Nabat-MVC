<?php
declare(strict_types=1);

final class AdminController
{
    public function __construct(private PDO $db) {}

    public function dashboard(): void
    {
        require_role($this->db, ['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                http_response_code(419);
                $_SESSION['flash_error'] = 'Your session expired. Try the update again.';
            } else {
                $orderId = (int) ($_POST['cart_id'] ?? 0);
                $newStatus = (string) ($_POST['status'] ?? '');
                if ($orderId <= 0 || !in_array($newStatus, ['pending', 'processing', 'completed', 'cancelled'], true)) {
                    $_SESSION['flash_error'] = 'The order update was not valid.';
                } else {
                    try {
                        $this->db->beginTransaction();
                        $orderQuery = $this->db->prepare('SELECT cart_id, status FROM cart WHERE cart_id = :id FOR UPDATE');
                        $orderQuery->execute(['id' => $orderId]);
                        $order = $orderQuery->fetch();
                        if (!$order) {
                            throw new RuntimeException('That order could not be found.');
                        }
                        $oldStatus = (string) $order['status'];
                        if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                            throw new RuntimeException('A cancelled order cannot be reopened in this version.');
                        }
                        if ($oldStatus === 'completed' && $newStatus !== 'completed') {
                            throw new RuntimeException('A completed order cannot be changed in this version.');
                        }
                        if (in_array($oldStatus, ['pending', 'processing'], true) && $newStatus === 'cancelled') {
                            // Restore only real product lines; legacy collection-only lines have no product stock.
                            $itemsQuery = $this->db->prepare('SELECT pro_id, quantity FROM cart_items WHERE cart_id = :cart_id AND pro_id IS NOT NULL');
                            $itemsQuery->execute(['cart_id' => $orderId]);
                            $restoreStock = $this->db->prepare('UPDATE product SET quantity = quantity + :quantity_add, count_selling = GREATEST(0, count_selling - :quantity_sold) WHERE pro_id = :pro_id');
                            foreach ($itemsQuery->fetchAll() as $item) {
                                $restoreStock->execute([
                                    'quantity_add' => (int) $item['quantity'],
                                    'quantity_sold' => (int) $item['quantity'],
                                    'pro_id' => (int) $item['pro_id'],
                                ]);
                            }
                        }
                        $update = $this->db->prepare('UPDATE cart SET status = :status WHERE cart_id = :id');
                        $update->execute(['status' => $newStatus, 'id' => $orderId]);
                        $this->db->commit();
                        $_SESSION['flash_success'] = 'Order status updated.';
                    } catch (Throwable $exception) {
                        if ($this->db->inTransaction()) $this->db->rollBack();
                        $_SESSION['flash_error'] = $exception instanceof RuntimeException
                            ? $exception->getMessage()
                            : 'The order could not be updated. Please try again.';
                        if (!$exception instanceof RuntimeException) {
                            error_log('Nabta order status update error: ' . $exception->getMessage());
                        }
                    }
                }
            }
            header('Location: index.php?route=admin');
            exit;
        }

        $stats = [
            'products' => (int) $this->db->query("SELECT COUNT(*) FROM product WHERE status = 'active'")->fetchColumn(),
            'customers' => (int) $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'customer' AND status = 'active'")->fetchColumn(),
            'orders' => (int) $this->db->query('SELECT COUNT(*) FROM cart')->fetchColumn(),
            'revenue' => (float) $this->db->query("SELECT COALESCE(SUM(total_price), 0) FROM cart WHERE status <> 'cancelled'")->fetchColumn(),
        ];
        $orders = $this->db->query('SELECT c.cart_id, c.created_at, c.total_price, c.status, u.full_name, u.email FROM cart c JOIN users u ON u.user_id = c.user_id ORDER BY c.created_at DESC, c.cart_id DESC LIMIT 12')->fetchAll();
        $pageTitle = 'Store management';
        require dirname(__DIR__) . '/Views/admin/dashboard.php';
    }
}
