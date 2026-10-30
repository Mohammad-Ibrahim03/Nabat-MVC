<?php
declare(strict_types=1);

final class Product
{
    public function __construct(private PDO $db) {}

    public function getAllProducts(array $filters = []): array
    {
        $sql = "SELECT p.pro_id, p.cat_id, p.name, p.description, p.price, p.quantity, p.count_selling, p.status,
                       c.name AS category_name,
                       (SELECT pi.image_url FROM product_images pi WHERE pi.pro_id = p.pro_id ORDER BY pi.is_primary DESC, pi.image_id ASC LIMIT 1) AS image_url
                FROM product p LEFT JOIN categories c ON c.cat_id = p.cat_id
                WHERE p.status = 'active'";
        $params = [];
        if (!empty($filters['category'])) {
            $sql .= ' AND p.cat_id = :category';
            $params['category'] = (int) $filters['category'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (p.name LIKE :search_name OR p.description LIKE :search_description OR c.name LIKE :search_category)';
            $term = '%' . trim((string) $filters['search']) . '%';
            $params['search_name'] = $term;
            $params['search_description'] = $term;
            $params['search_category'] = $term;
        }
        $sort = (string) ($filters['sort'] ?? 'featured');
        $sql .= match ($sort) {
            'price-low' => ' ORDER BY p.price ASC',
            'price-high' => ' ORDER BY p.price DESC',
            'name' => ' ORDER BY p.name ASC',
            'popular' => ' ORDER BY p.count_selling DESC, p.pro_id DESC',
            default => ' ORDER BY p.pro_id DESC',
        };
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT p.pro_id, p.cat_id, p.name, p.description, p.price, p.quantity, p.count_selling, p.status,
                    c.name AS category_name,
                    (SELECT pi.image_url FROM product_images pi WHERE pi.pro_id = p.pro_id ORDER BY pi.is_primary DESC, pi.image_id ASC LIMIT 1) AS image_url
                    FROM product p LEFT JOIN categories c ON c.cat_id = p.cat_id
                    WHERE p.pro_id = :id AND p.status = 'active' LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getCategories(): array
    {
        return $this->db->query('SELECT cat_id, name, description FROM categories ORDER BY name')->fetchAll();
    }

    public static function imageFor(array $product): string
    {
        $img = trim((string) ($product['image_url'] ?? ''));
        if ($img !== '' && (str_starts_with($img, 'https://') || str_starts_with($img, 'http://'))) return $img;
        $map = [1 => 'plant.jpg', 2 => 'garden-sky.jpg', 3 => 'plant.jpg', 4 => 'pot.jpg', 5 => 'soil.jpg', 6 => 'care.jpg', 7 => 'tools.jpg', 8 => 'garden-sky.jpg', 9 => 'care.jpg', 10 => 'decor.jpg'];
        return 'assets/img/' . ($map[(int) ($product['cat_id'] ?? 0)] ?? 'editorial.jpg');
    }
}
