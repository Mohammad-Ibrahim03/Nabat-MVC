<?php
$pageTitle = $success ? 'Message received' : 'Contact our team';
$isRegister = false;
require dirname(__DIR__) . '/layouts/auth-layout-top.php';
?>
    <section class="auth-card auth-result-card" aria-labelledby="contact-result-title">
      <a class="auth-back" href="index.php#contact-us"><span aria-hidden="true">←</span> Back to Contact Us</a>
      <h2 id="contact-result-title"><?= $success ? 'Message received.' : 'Let’s get this sorted.' ?></h2>
      <p class="auth-subtitle"><?= $success ? 'Thank you for reaching out. Your message has been saved, and the Nabta team can follow up with you.' : e($error) ?></p>
      <?php if (!$success && !is_authenticated()): ?>
        <a class="btn btn-primary auth-submit" href="index.php?route=login">Sign in to continue <span aria-hidden="true">↗</span></a>
      <?php else: ?>
        <a class="btn btn-primary auth-submit" href="index.php#contact-us">Return to Nabta <span aria-hidden="true">↗</span></a>
      <?php endif; ?>
    </section>
<?php require dirname(__DIR__) . '/layouts/auth-layout-bottom.php'; ?>
