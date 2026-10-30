<?php
$googleClientId = google_client_id();
require dirname(__DIR__) . '/layouts/auth-layout-top.php';
?>
<section class="auth-card" aria-labelledby="auth-title">
  <a class="auth-back" href="index.php"><span aria-hidden="true">←</span> Back to Nabta</a>
  <h2 id="auth-title">Welcome back.</h2>
  <p class="auth-subtitle">Sign in to pick up where your growing journey left off.</p>

  <?php if (!empty($_SESSION['auth_notice'])): ?>
    <div class="auth-message" role="status"><?= e($_SESSION['auth_notice']) ?></div>
    <?php unset($_SESSION['auth_notice']); ?>
  <?php endif; ?>
  <?php if ($error !== ''): ?>
    <div class="auth-message error" role="alert"><?= e($error) ?></div>
  <?php endif; ?>

  <form class="auth-form" method="post" action="index.php?route=login" autocomplete="on">
    <?= csrf_field() ?>
    <label for="login-email">Email address
      <input id="login-email" type="email" name="email" autocomplete="email" inputmode="email" placeholder="you@example.com" value="<?= e($_POST['email'] ?? '') ?>" maxlength="150" required>
    </label>
    <label for="login-password">Password
      <span class="auth-input-wrap">
        <input id="login-password" type="password" name="password" autocomplete="current-password" placeholder="Enter your password" required>
        <button class="password-toggle" type="button" data-password-toggle="login-password" aria-label="Show password">Show</button>
      </span>
    </label>
    <button class="btn btn-primary auth-submit" type="submit">Sign in <span aria-hidden="true">↗</span></button>
  </form>

  <?php if ($googleClientId !== ''): ?>
    <div class="auth-divider"><span>or continue with</span></div>
    <div class="google-auth-block" data-google-auth data-csrf-token="<?= e(csrf_token()) ?>">
      <div id="g_id_onload" data-client_id="<?= e($googleClientId) ?>" data-callback="handleGoogleCredential" data-auto_prompt="false"></div>
      <div class="g_id_signin" data-type="standard" data-shape="pill" data-theme="outline" data-text="continue_with" data-size="large" data-width="320"></div>
      <p class="auth-message error" data-google-auth-message hidden></p>
    </div>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="assets/js/google-auth.js?v=2" defer></script>
  <?php endif; ?>

  <p class="auth-switch">New to Nabta? <a href="index.php?route=register">Create an account</a></p>
  <p class="auth-inline-note">For your privacy, we won’t tell you whether an email or password was incorrect.</p>
</section>
<?php require dirname(__DIR__) . '/layouts/auth-layout-bottom.php'; ?>
