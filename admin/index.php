<?php
require_once '../config/database.php';require_once '../includes/functions.php';require_admin();
$movies=(int)$pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
$users=(int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$orders=(int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$revenue=(float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status='confirmed'")->fetchColumn();
$pageTitle='Панели администратор';$assetPrefix='../';$homePrefix='../';require '../includes/header.php';
?>
<p class="eyebrow">ИДОРАКУНӢ</p><h1>Панели администратор</h1>
<div class="stats"><div class="stat"><span>Филмҳо</span><strong><?=$movies?></strong></div><div class="stat"><span>Истифодабарандагон</span><strong><?=$users?></strong></div><div class="stat"><span>Фармоишҳо</span><strong><?=$orders?></strong></div><div class="stat"><span>Даромад</span><strong><?=number_format($revenue,2)?> сом.</strong></div></div>
<div class="admin-links"><a href="movies.php">🎞️ Идоракунии филмҳо</a><a href="showtimes.php">🕒 Идоракунии сеансҳо</a><a href="orders.php">🎟️ Фармоишҳо</a><a href="users.php">👤 Истифодабарандагон</a></div>
<?php require '../includes/footer.php'; ?>
