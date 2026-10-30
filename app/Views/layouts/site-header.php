<?php $bagCount = store_cart_count(); ?>
<header class="store-header">
  <div class="store-header-inner">
    <a class="store-logo" href="index.php" aria-label="Nabta Botanical home"><span class="store-logo-mark">✳</span><span>NABTA<small>BOTANICAL</small></span></a>
    <nav class="store-nav" aria-label="Main navigation">
      <a href="index.php?route=products">Shop</a>
      <a href="index.php?route=assistant">Find your plant</a>
      <a href="index.php?route=about">Our story</a>
    </nav>
    <div class="store-actions">
      <?php if (is_authenticated()): ?>
        <a class="store-account" href="index.php?route=profile">Hi, <?= e(explode(' ', (string) ($_SESSION['full_name'] ?? 'there'))[0]) ?></a>
        <form action="index.php?route=logout" method="post" class="store-inline-form"><?= csrf_field() ?><button class="store-quiet-button" type="submit">Sign out</button></form>
      <?php else: ?>
        <a class="store-account" href="index.php?route=login">Sign in</a>
      <?php endif; ?>
      <a class="store-bag" href="index.php?route=cart" aria-label="Shopping basket">Bag <span><?= (int) $bagCount ?></span></a>
      <details class="store-mobile-nav"><summary aria-label="Open navigation">☰</summary><nav>
        <a href="index.php?route=products">Shop</a><a href="index.php?route=assistant">Find your plant</a><a href="index.php?route=about">Our story</a>
        <?php if (is_authenticated()): ?><a href="index.php?route=profile">My account</a><a href="index.php?route=orders">My orders</a><?php else: ?><a href="index.php?route=login">Sign in</a><?php endif; ?>
        <a href="index.php?route=cart">Basket (<?= (int) $bagCount ?>)</a>
      </nav></details>
    </div>
  </div>
</header>
