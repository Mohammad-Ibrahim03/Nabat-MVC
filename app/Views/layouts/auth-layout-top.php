<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#051f20">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($pageTitle ?? 'Your Nabta account') ?> | Nabta Botanical</title>
  <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="assets/css/style.css?v=10">
  <link rel="stylesheet" href="assets/css/botanical.css?v=10">
  <link rel="stylesheet" href="assets/css/nabta-theme.css?v=24">
  <link rel="stylesheet" href="assets/css/store.css?v=1">
</head>
<body class="auth-page botanical-edition<?= !empty($isRegister) ? ' is-register' : '' ?>">
  <aside class="auth-visual" aria-label="Nabta Botanical introduction">
    <a class="auth-brand" href="index.php" aria-label="Nabta Botanical home">
      <svg viewBox="0 0 36 36" aria-hidden="true"><path d="M28 5C9 4 4 15 10 24c8 7 21-2 18-19Z"/><path d="M8 31 23 12"/></svg>
      NABTA <span class="logo-sub">BOTANICAL</span>
    </a>
    <div class="auth-visual-copy">
      <span class="eyebrow">A LITTLE NATURE, CLOSER TO HOME</span>
      <h1>Make room<br>for <em>growing.</em></h1>
      <p>Your own corner for plant discoveries, thoughtful essentials, and a greener everyday.</p>
    </div>
    <div class="auth-visual-foot">Rooted in the everyday · Jordan / JOD</div>
  </aside>
  <main class="auth-panel">
