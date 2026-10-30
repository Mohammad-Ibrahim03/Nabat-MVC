<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#051f20">
  <meta name="description"
    content="Discover the ideas behind Nabta Botanical — a thoughtful home for plants, growing essentials, and everyday greener living.">
  <title>About Nabta | Nabta Botanical</title>
  <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="assets/css/style.css?v=10">
  <link rel="stylesheet" href="assets/css/botanical.css?v=10">
  <link rel="stylesheet" href="assets/css/alive.css?v=12">
  <link rel="stylesheet" href="assets/css/nabta-theme.css?v=24">
</head>

<body class="about-page index-page botanical-edition">
  <a class="skip" href="#main">Skip to content</a>
  <header class="header">
    <div class="container header-row">
      <a class="logo" href="index.php" aria-label="Nabta home"><svg class="leaf-logo" viewBox="0 0 36 36"
          aria-hidden="true">
          <path d="M28 5C9 4 4 15 10 24c8 7 21-2 18-19Z" />
          <path d="M8 31 23 12" />
        </svg>NABTA<span class="logo-sub">BOTANICAL</span></a>
      <nav class="main-nav" aria-label="Main navigation"><a href="index.php?route=products">Shop</a><a href="index.php?route=assistant">Find
          your plant</a><a href="index.php?route=about" aria-current="page">About us</a><a
          href="index.php?route=products#collections">Collections</a></nav>
      <div class="header-actions">
        <?php if (is_authenticated()): ?>
          <a class="account-link" href="index.php?route=profile">Hi,
            <?= e(explode(' ', (string) ($_SESSION['full_name'] ?? 'there'))[0]) ?></a>
          <form class="logout-form" action="index.php?route=logout" method="post"><?= csrf_field() ?><button class="account-link"
              type="submit">Sign out</button></form>
        <?php else: ?>
          <a class="account-link" href="index.php?route=login">Sign in</a><a class="account-link register-link"
            href="index.php?route=register">Register</a>
        <?php endif; ?>
        <a class="bag-link" href="index.php?route=cart" aria-label="Shopping basket"><svg class="icon" viewBox="0 0 24 24"
            aria-hidden="true">
            <path d="M5 7h14l1 14H4L5 7Z" />
            <path d="M9 8V6a3 3 0 0 1 6 0v2" />
          </svg></a>
        <details class="mobile-nav">
          <summary aria-label="Open navigation">☰</summary>
          <nav><a href="index.php?route=products">Shop</a><a href="index.php?route=assistant">Find your plant</a><a href="index.php?route=about"
              aria-current="page">About us</a><a
              href="index.php?route=products#collections">Collections</a><?php if (is_authenticated()): ?><a href="index.php?route=profile">My
                account</a>
              <form class="logout-form" action="index.php?route=logout" method="post"><?= csrf_field() ?><button type="submit">Sign
                  out</button></form><?php else: ?><a href="index.php?route=login">Sign in</a><a href="index.php?route=register">Create an
                account</a><?php endif; ?>
          </nav>
        </details>
      </div>
    </div>
  </header>
  <main id="main" class="about-page-main">
    <section class="about-hero" aria-labelledby="about-title">
      <div class="about-hero-copy">
        <span class="eyebrow">OUR ROOTS / NABTA BOTANICAL</span>
        <h1 id="about-title">A little more nature.<br><em>A lot more life.</em></h1>
        <p>We believe the smallest green corner can change how a space feels. Nabta brings plants, thoughtful objects,
          and the essentials to help everyday life grow a little wilder.</p>
        <div class="cta-row"><a class="btn" href="index.php?route=products">Explore the collection <span
              aria-hidden="true">↗</span></a><a class="link" href="index.php?route=assistant">Find your plant ↗</a></div>
        <span class="about-hero-note">GROWN FOR REAL LIFE · ROOTED IN JORDAN</span>
      </div>
    </section>
    <section class="about-story screen" aria-labelledby="about-story-title">
      <div class="about-story-image"><img src="assets/img/editorial.jpg"
          alt="A calm, leafy corner with botanical growing essentials" loading="lazy" width="1536" height="1024"></div>
      <div class="about-story-copy"><span class="eyebrow">THE IDEA IS SIMPLE</span>
        <h2 id="about-story-title">Make room for<br><em>something living.</em></h2>
        <p>Nabta was imagined for people who want to bring more nature into their routines, whether that means their
          first easy-care plant, a thoughtful gift, or the start of a home jungle.</p>
        <p>We put plant discoveries, useful growing essentials, and practical guidance in one welcoming place, so
          getting started feels less complicated and more personal.</p>
      </div>
    </section>
    <section class="about-values screen" aria-labelledby="about-values-title">
      <div class="section-top">
        <div><span class="eyebrow">WHAT WE GROW BY</span>
          <h2 id="about-values-title">Good things grow<br><em>with intention.</em></h2>
        </div>
        <p>Thoughtful choices, helpful guidance, and space for every kind of grower.</p>
      </div>
      <div class="about-values-grid">
        <article class="about-value"><span>01 / DISCOVER</span>
          <h3>Find your kind of green.</h3>
          <p>From a single leaf to a fuller jungle, explore plants and pieces that make sense for your space and your
            pace.</p>
        </article>
        <article class="about-value"><span>02 / LEARN</span>
          <h3>Care with confidence.</h3>
          <p>Practical plant-care guidance helps you understand what your plants need, without making growing feel like
            a science exam.</p>
        </article>
        <article class="about-value"><span>03 / GROW</span>
          <h3>Let small things add up.</h3>
          <p>Build a greener everyday one considered choice at a time, with useful essentials close at hand.</p>
        </article>
      </div>
    </section>
    <section class="about-cta screen"><span class="eyebrow">YOUR NEXT CHAPTER CAN BE GREEN</span>
      <h2>Start with one leaf.<br><em>See where it grows.</em></h2><a class="btn" href="index.php?route=products">Shop the
        collection ↗</a>
    </section>
  </main>
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div><a class="logo" href="index.php" aria-label="Nabta home"><svg class="leaf-logo" viewBox="0 0 36 36"
              aria-hidden="true">
              <path d="M28 5C9 4 4 15 10 24c8 7 21-2 18-19Z" />
              <path d="M8 31 23 12" />
            </svg>NABTA<span class="logo-sub">BOTANICAL</span></a>
          <p>A little nature, closer to home.<br>Plants, thoughtful objects, and everyday growing essentials.</p>
        </div>
        <div>
          <div class="footer-title">GROW YOUR SPACE</div>
          <nav class="footer-links"><a href="index.php?route=products">Shop the collection</a><a href="index.php?route=assistant">Plant
              assistant</a><a href="index.php?route=about">About Nabta</a></nav>
        </div>
        <div>
          <div class="footer-title">YOUR NABTA</div>
          <nav class="footer-links"><a href="index.php?route=profile">My account</a><a href="index.php?route=orders">My orders</a></nav>
        </div>
        <div>
          <div class="footer-title">ROOTED IN THE EVERYDAY</div>
          <p>For windowsills, balconies, and the small corners that make a home.</p><a class="link" href="index.php#faq"
            style="margin-top:16px">A few helpful answers ↗</a>
        </div>
      </div>
      <div class="footer-bottom"><span>© 2026 Nabta Botanical · Storefront concept</span><span>Jordan / JOD</span></div>
    </div>
  </footer>
  <script src="assets/js/nabta-theme.js" defer></script>
</body>

</html>