<?php require __DIR__ . '/layouts/site-header.php'; ?>
<main class="store-page">
  <section class="store-page-hero"><p class="store-eyebrow">THE NABTA COLLECTION</p><h1>Bring a little <em>green</em> home.</h1><p>Plants, thoughtful objects and essentials to help your space grow.</p></section>
  <section class="store-content">
    <?php if (!empty($_SESSION['flash_success'])): ?><div class="store-alert success"><?= e($_SESSION['flash_success']) ?></div><?php unset($_SESSION['flash_success']); endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?><div class="store-alert error"><?= e($_SESSION['flash_error']) ?></div><?php unset($_SESSION['flash_error']); endif; ?>
    <form class="store-filter-bar" method="get" action="index.php">
      <input type="hidden" name="route" value="products">
      <label class="store-search"><span>Search the store</span><input type="search" name="q" value="<?= e($filters['search']) ?>" placeholder="Try Monstera, soil, pots…"></label>
      <label><span>Category</span><select name="category"><option value="0">All categories</option><?php foreach ($categories as $category): ?><option value="<?= (int) $category['cat_id'] ?>" <?= (int) $filters['category'] === (int) $category['cat_id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option><?php endforeach; ?></select></label>
      <label><span>Sort by</span><select name="sort"><option value="featured" <?= $filters['sort'] === 'featured' ? 'selected' : '' ?>>Featured</option><option value="popular" <?= $filters['sort'] === 'popular' ? 'selected' : '' ?>>Most popular</option><option value="price-low" <?= $filters['sort'] === 'price-low' ? 'selected' : '' ?>>Price: low to high</option><option value="price-high" <?= $filters['sort'] === 'price-high' ? 'selected' : '' ?>>Price: high to low</option><option value="name" <?= $filters['sort'] === 'name' ? 'selected' : '' ?>>Name</option></select></label>
      <button class="store-button" type="submit">Apply</button>
    </form>
    <div class="store-result-line"><span><?= count($products) ?> items to explore</span><a href="index.php?route=wishlist">View wishlist ↗</a></div>
    <?php if ($products): ?>
      <div class="store-product-grid">
        <?php foreach ($products as $product): ?>
          <article class="store-product-card">
            <a class="store-product-image" href="index.php?route=product&id=<?= (int) $product['pro_id'] ?>"><img src="<?= e(store_product_image($product)) ?>" alt="<?= e($product['name']) ?>" loading="lazy"><span><?= e($product['category_name'] ?? 'Nabta pick') ?></span></a>
            <div class="store-product-info"><div><h2><a href="index.php?route=product&id=<?= (int) $product['pro_id'] ?>"><?= e($product['name']) ?></a></h2><p><?= e($product['description'] ?? '') ?></p></div><div class="store-product-bottom"><strong><?= number_format((float) $product['price'], 2) ?> <small>JOD</small></strong><form method="post" action="index.php?route=cart"><?= csrf_field() ?><input type="hidden" name="action" value="add"><input type="hidden" name="pro_id" value="<?= (int) $product['pro_id'] ?>"><button aria-label="Add <?= e($product['name']) ?> to basket" type="submit">+</button></form></div></div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?><div class="store-empty"><span>✳</span><h2>No plants found just yet.</h2><p>Try another search or clear your category filter.</p><a class="store-button" href="index.php?route=products">See all products</a></div><?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/layouts/site-footer.php'; ?>
