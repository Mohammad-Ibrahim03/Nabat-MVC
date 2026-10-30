<?php
$googleClientId = google_client_id();
require dirname(__DIR__) . '/layouts/auth-layout-top.php';
?>
<section class="auth-card auth-card-register" aria-labelledby="auth-title">
  <a class="auth-back" href="index.php"><span aria-hidden="true">←</span> Back to Nabta</a>
  <h2 id="auth-title">Grow with Nabta.</h2>
  <p class="auth-subtitle">Create your account for a more personal plant-shopping experience. Thoughtfully designed, secure by default.</p>

  <?php if ($error !== ''): ?>
    <div class="auth-message error" role="alert"><?= e($error) ?></div>
  <?php endif; ?>
  <form class="auth-form" method="post" action="index.php?route=register" autocomplete="on">
    <?= csrf_field() ?>
    <div class="register-grid">
      <label for="register-name">Full name
        <input id="register-name" type="text" name="full_name" autocomplete="name" placeholder="Your name" value="<?= e($_POST['full_name'] ?? '') ?>" minlength="2" maxlength="100" required>
      </label>
      <label for="register-email">Email address
        <input id="register-email" type="email" name="email" autocomplete="email" inputmode="email" placeholder="you@example.com" value="<?= e($_POST['email'] ?? '') ?>" maxlength="150" required>
      </label>
      <label for="register-phone" class="phone-field">
        <span class="field-label-row">Phone <span class="optional-tag">(optional)</span></span>
        <input id="register-phone" type="tel" name="phone" autocomplete="tel" inputmode="tel" placeholder="07X XXX XXXX" value="<?= e($_POST['phone'] ?? '') ?>" maxlength="30">
      </label>
      <label for="register-password" class="password-field">Create password
        <span class="auth-input-wrap">
          <input id="register-password" data-password-strength type="password" name="password" autocomplete="new-password" placeholder="Build a strong password" minlength="8" required aria-describedby="password-rules">
          <button class="password-toggle" type="button" data-password-toggle="register-password" aria-label="Show password">Show</button>
        </span>
        <div class="password-rules" id="password-rules" data-password-rules aria-live="polite" hidden>
          <p class="password-rules-title">Your password should include:</p>
          <ul class="password-rules-list">
            <li data-rule="length" aria-checked="false">At least 8 characters</li>
            <li data-rule="letter" aria-checked="false">At least one letter</li>
            <li data-rule="number" aria-checked="false">At least one number</li>
            <li data-rule="symbol" aria-checked="false">At least one symbol</li>
          </ul>
          <div class="password-progress" role="progressbar" aria-label="Password requirements met" aria-valuemin="0" aria-valuemax="4"><span data-password-meter></span></div>
        </div>
      </label>
      <label for="register-confirm" class="register-confirm">Confirm password
        <span class="auth-input-wrap">
          <input id="register-confirm" data-confirm-password type="password" name="confirm_password" autocomplete="new-password" placeholder="Enter the same password again" minlength="8" required>
          <button class="password-toggle" type="button" data-password-toggle="register-confirm" aria-label="Show confirmation password">Show</button>
        </span>
      </label>
      <p class="auth-inline-note">Your password is stored as a secure hash. We never save it as readable text.</p>
      <button class="btn btn-primary auth-submit" type="submit">Create my account <span aria-hidden="true">↗</span></button>
    </div>
  </form>

  <?php if ($googleClientId !== ''): ?>
    <div class="auth-divider"><span>or continue with</span></div>
    <div class="google-auth-block" data-google-auth data-csrf-token="<?= e(csrf_token()) ?>">
      <div id="g_id_onload" data-client_id="<?= e($googleClientId) ?>" data-callback="handleGoogleCredential" data-auto_prompt="false"></div>
      <div class="g_id_signin" data-type="standard" data-shape="pill" data-theme="outline" data-text="signup_with" data-size="large" data-width="320"></div>
      <p class="auth-message error" data-google-auth-message hidden></p>
    </div>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="assets/js/google-auth.js?v=2" defer></script>
  <?php endif; ?>

  <p class="auth-switch">Already part of Nabta? <a href="index.php?route=login">Sign in</a></p>
</section>
<?php require dirname(__DIR__) . '/layouts/auth-layout-bottom.php'; ?>
