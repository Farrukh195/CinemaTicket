<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id=? AND is_active=1");
$stmt->execute([$id]);
$movie = $stmt->fetch();
if (!$movie) { http_response_code(404); exit('Филм ёфт нашуд.'); }
$stmt = $pdo->prepare("SELECT s.*, h.name AS hall_name FROM showtimes s JOIN halls h ON h.id=s.hall_id WHERE s.movie_id=? AND s.starts_at>=NOW() ORDER BY s.starts_at");
$stmt->execute([$id]); $shows = $stmt->fetchAll();
$pageTitle = $movie['title']; $assetPrefix=''; $homePrefix='';
require 'includes/header.php';
?>
<div class="detail-layout">
  <div class="detail-poster"><?php if($movie['poster']): ?><img src="<?=e($movie['poster'])?>" alt="<?=e($movie['title'])?>"><?php else: ?>🎞️<?php endif; ?></div>
  <div class="detail-content">
    <p class="eyebrow">МАЪЛУМОТ ДАР БОРАИ ФИЛМ</p><h1><?=e($movie['title'])?></h1>
    <p class="muted"><?=e($movie['genre'])?> · <?= (int)$movie['duration']?> дақиқа · Рейтинг: <?=e($movie['rating'] ?: '—')?></p>
    <p><?=nl2br(e($movie['description']))?></p>
    <h2>Интихоби сана ва вақт</h2>
    <div class="show-list">
    <?php foreach($shows as $show): ?>
      <a class="show-option" href="seats.php?showtime_id=<?=(int)$show['id']?>">
        <strong><?=date('d.m.Y H:i', strtotime($show['starts_at']))?></strong>
        <span><?=e($show['hall_name'])?> · <?=number_format((float)$show['price'],2)?> сомонӣ</span>
      </a>
    <?php endforeach; ?>
    <?php if(!$shows): ?><p class="empty">Барои ин филм ҳоло сеанс нест.</p><?php endif; ?>
    </div>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
