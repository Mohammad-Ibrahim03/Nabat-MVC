<?php
declare(strict_types=1);
require_once __DIR__ . '/../Models/Product.php';

final class ProductController
{
    public function __construct(private PDO $db) {}

    public function index(): void
    {
        $model = new Product($this->db);
        $filters = [
            'category' => (int) ($_GET['category'] ?? 0),
            'search' => trim((string) ($_GET['q'] ?? '')),
            'sort' => (string) ($_GET['sort'] ?? 'featured'),
        ];
        $products = $model->getAllProducts($filters);
        $categories = $model->getCategories();
        $pageTitle = 'Shop the collection';
        require dirname(__DIR__) . '/Views/products.php';
    }

    public function show(): void
    {
        $model = new Product($this->db);
        $product = $model->findById((int) ($_GET['id'] ?? 0));
        if (!$product) {
            http_response_code(404);
            $pageTitle = 'Product not found';
            require dirname(__DIR__) . '/Views/404.php';
            return;
        }
        $pageTitle = (string) $product['name'];
        require dirname(__DIR__) . '/Views/product.php';
    }
}
