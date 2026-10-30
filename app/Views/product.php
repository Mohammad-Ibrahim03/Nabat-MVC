<?php require __DIR__ . '/layouts/site-header.php'; ?>
<main class="store-page store-detail-page"><div class="store-content">
  <p class="store-breadcrumb"><a href="index.php">Home</a> / <a href="index.php?route=products">Shop</a> / <?= e($product['name']) ?></p>
  <section class="store-detail-grid">
    <div class="store-detail-image"><img src="<?= e(store_product_image($product)) ?>" alt="<?= e($product['name']) ?>"></div>
    <div class="store-detail-copy"><p class="store-eyebrow"><?= e($product['category_name'] ?? 'Nabta botanical') ?></p><h1><?= e($product['name']) ?></h1><p class="store-detail-price"><?= number_format((float) $product['price'], 2) ?> <small>JOD</small></p><p class="store-detail-description"><?= e($product['description'] ?? 'A thoughtful addition to your growing space.') ?></p><p class="store-stock <?= (int) $product['quantity'] > 0 ? 'in-stock' : 'out-stock' ?>"><?= (int) $product['quantity'] > 0 ? 'In stock · ' . (int) $product['quantity'] . ' available' : 'Currently unavailable' ?></p>
      <?php if ((int) $product['quantity'] > 0): ?><form class="store-add-form" method="post" action="index.php?route=cart"><?= csrf_field() ?><input type="hidden" name="action" value="add"><input type="hidden" name="pro_id" value="<?= (int) $product['pro_id'] ?>"><label>Quantity<input name="quantity" type="number" min="1" max="<?= min(99, (int) $product['quantity']) ?>" value="1"></label><button class="store-button" type="submit">Add to basket ↗</button></form><?php endif; ?>
      <form method="post" action="index.php?route=wishlist-action" class="store-wishlist-form"><?= csrf_field() ?><input type="hidden" name="pro_id" value="<?= (int) $product['pro_id'] ?>"><input type="hidden" name="return_to" value="index.php?route=product&id=<?= (int) $product['pro_id'] ?>"><button class="store-text-button" type="submit">♡ Save to wishlist</button></form>
      <p class="store-detail-note">Carefully packed. Delivered across Jordan.</p>
    </div>
  </section>
</div></main>
<?php require __DIR__ . '/layouts/site-footer.php'; ?>
