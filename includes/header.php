<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'CinemaTicket';
?>
<!doctype html>
<html lang="tg">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | CinemaTicket</title>
<link rel="stylesheet" href="<?= e($assetPrefix ?? '') ?>assets/css/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="<?= e($homePrefix ?? '') ?>index.php">🎬 Cinema<span>Ticket</span></a>
  <nav>
    <a href="<?= e($homePrefix ?? '') ?>index.php">Асосӣ</a>
    <?php if (is_logged_in()): ?>
      <a href="<?= e($homePrefix ?? '') ?>my_tickets.php">Чиптаҳои ман</a>
      <?php if (is_admin()): ?><a href="<?= e($homePrefix ?? '') ?>admin/index.php">Администратор</a><?php endif; ?>
      <a href="<?= e($homePrefix ?? '') ?>logout.php">Баромад</a>
    <?php else: ?>
      <a href="<?= e($homePrefix ?? '') ?>login.php">Воридшавӣ</a>
      <a class="nav-button" href="<?= e($homePrefix ?? '') ?>register.php">Бақайдгирӣ</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert <?= e($f['type']) ?>"><?= e($f['message']) ?></div>
<?php endif; ?>
