<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
$pageTitle = 'Филмҳо';
$assetPrefix = '';
$homePrefix = '';
$stmt = $pdo->query("SELECT m.*, MIN(s.starts_at) AS next_show FROM movies m LEFT JOIN showtimes s ON s.movie_id=m.id AND s.starts_at>=NOW() WHERE m.is_active=1 GROUP BY m.id ORDER BY m.created_at DESC");
$movies = $stmt->fetchAll();
require 'includes/header.php';
?>
<section class="hero">
  <div>
    <p class="eyebrow">ҶАҲОНИ КИНО ДАР ЯК ҶО</p>
    <h1>Филми дӯстдоштаатонро<br><span>интихоб кунед</span></h1>
    <p>Филм, вақт ва ҷойи нишастро интихоб карда, чиптаи худро онлайн фармоиш диҳед.</p>
    <a class="button" href="#movies">Дидани филмҳо</a>
  </div>
  <div class="hero-art">🎬</div>
</section>
<section id="movies">
  <div class="section-heading"><div><p class="eyebrow">НАМОИШҲО</p><h2>Филмҳои дастрас</h2></div></div>
  <div class="movie-grid">
  <?php foreach ($movies as $movie): ?>
    <article class="movie-card">
      <div class="poster">
        <?php if ($movie['poster']): ?><img src="<?= e($movie['poster']) ?>" alt="<?= e($movie['title']) ?>">
        <?php else: ?><div class="poster-placeholder">🎞️</div><?php endif; ?>
        <span class="rating">★ <?= e($movie['rating'] ?: '—') ?></span>
      </div>
      <div class="movie-info">
        <h3><?= e($movie['title']) ?></h3>
        <p><?= e($movie['genre']) ?> · <?= (int)$movie['duration'] ?> дақ.</p>
        <p class="muted"><?= e(mb_strimwidth($movie['description'] ?? '', 0, 95, '…')) ?></p>
        <a class="button full" href="movie.php?id=<?= (int)$movie['id'] ?>">Интихоби сеанс</a>
      </div>
    </article>
  <?php endforeach; ?>
  <?php if (!$movies): ?><div class="empty">Ҳоло филм илова нашудааст. Администратор метавонад аз панели идоракунӣ филм илова кунад.</div><?php endif; ?>
  </div>
</section>
<?php require 'includes/footer.php'; ?>
