<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Models/Product.php';

/** Resolve the guest/session basket against current, active database products. */
function store_cart_items(PDO $db): array
{
    $model = new Product($db);
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $basket = $_SESSION['cart'];
    $items = [];
    foreach ($basket as $rawId => $rawQty) {
        $id = (int) $rawId;
        $qty = max(1, min(99, (int) $rawQty));
        $product = $model->findById($id);
        if (!$product || (int) $product['quantity'] < 1) {
            unset($_SESSION['cart'][$id]);
            continue;
        }
        $qty = min($qty, (int) $product['quantity']);
        $_SESSION['cart'][$id] = $qty;
        $product['cart_quantity'] = $qty;
        $product['line_total'] = (float) $product['price'] * $qty;
        $items[] = $product;
    }
    return $items;
}

function store_cart_subtotal(array $items): float
{
    return array_reduce($items, static fn(float $sum, array $item): float => $sum + (float) $item['line_total'], 0.0);
}

function store_cart_count(): int
{
    $basket = $_SESSION['cart'] ?? [];
    return is_array($basket) ? array_sum(array_map('intval', $basket)) : 0;
}

function store_product_image(array $product): string
{
    return Product::imageFor($product);
}
