<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_login();
$showId = filter_input(INPUT_GET, 'showtime_id', FILTER_VALIDATE_INT);
$stmt=$pdo->prepare("SELECT s.*,m.title,h.id AS hall_id,h.name AS hall_name FROM showtimes s JOIN movies m ON m.id=s.movie_id JOIN halls h ON h.id=s.hall_id WHERE s.id=? AND s.starts_at>=NOW()");
$stmt->execute([$showId]); $show=$stmt->fetch();
if(!$show) exit('Сеанс ёфт нашуд ё вақти он гузаштааст.');
$stmt=$pdo->prepare("SELECT id,seat_row,seat_number FROM seats WHERE hall_id=? ORDER BY seat_row,seat_number");
$stmt->execute([$show['hall_id']]); $seats=$stmt->fetchAll();
$stmt=$pdo->prepare("SELECT seat_id FROM tickets WHERE showtime_id=? AND status IN ('reserved','paid')");
$stmt->execute([$showId]); $booked=array_column($stmt->fetchAll(),'seat_id');
$pageTitle='Интихоби ҷой'; $assetPrefix=''; $homePrefix='';
require 'includes/header.php';
?>
<div class="section-heading"><div><p class="eyebrow">ҚАДАМИ 1</p><h1>Ҷойи нишастро интихоб кунед</h1><p class="muted"><?=e($show['title'])?> · <?=date('d.m.Y H:i',strtotime($show['starts_at']))?> · <?=e($show['hall_name'])?></p></div></div>
<form action="checkout.php" method="post" id="seatForm">
<input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<input type="hidden" name="showtime_id" value="<?=(int)$showId?>">
<div class="screen">ЭКРАН</div>
<div class="seat-grid">
<?php foreach($seats as $seat): $taken=in_array($seat['id'],$booked); ?>
<label class="seat <?= $taken?'taken':'' ?>">
<input type="checkbox" name="seats[]" value="<?=(int)$seat['id']?>" <?= $taken?'disabled':'' ?>>
<span><?=e($seat['seat_row'])?><?= (int)$seat['seat_number']?></span>
</label>
<?php endforeach; ?>
</div>
<div class="seat-legend"><span>🟩 Холӣ</span><span>🟥 Банд</span><span>🟪 Интихобшуда</span></div>
<div class="checkout-bar"><strong>Нарх: <?=number_format((float)$show['price'],2)?> сомонӣ барои як ҷой</strong><button class="button" type="submit">Идома додан</button></div>
</form>
<?php require 'includes/footer.php'; ?>
