<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_login();
$orderId=filter_input(INPUT_GET,'order_id',FILTER_VALIDATE_INT);
$sql="SELECT o.*,u.full_name,m.title,s.starts_at,h.name hall_name,se.seat_row,se.seat_number,t.code,t.price
FROM orders o JOIN users u ON u.id=o.user_id JOIN tickets t ON t.order_id=o.id
JOIN showtimes s ON s.id=t.showtime_id JOIN movies m ON m.id=s.movie_id
JOIN halls h ON h.id=s.hall_id JOIN seats se ON se.id=t.seat_id
WHERE o.id=? AND o.user_id=? ORDER BY se.seat_row,se.seat_number";
$stmt=$pdo->prepare($sql); $stmt->execute([$orderId,$_SESSION['user']['id']]); $tickets=$stmt->fetchAll();
if(!$tickets) exit('Чипта ёфт нашуд.');
$pageTitle='Чиптаи электронӣ'; $assetPrefix=''; $homePrefix='';
require 'includes/header.php';
?>
<div class="ticket-page"><p class="eyebrow">ФАРМОИШИ ШУМО</p><h1>Чиптаи электронӣ</h1>
<?php foreach($tickets as $t): ?>
<div class="ticket">
  <div class="ticket-main"><div class="ticket-brand">🎬 CinemaTicket</div><h2><?=e($t['title'])?></h2>
  <p><?=date('d.m.Y',strtotime($t['starts_at']))?> · <?=date('H:i',strtotime($t['starts_at']))?></p>
  <p>Толор: <?=e($t['hall_name'])?> · Ҷой: <?=e($t['seat_row'])?><?= (int)$t['seat_number']?></p>
  <p>Мизоҷ: <?=e($t['full_name'])?></p></div>
  <div class="ticket-code"><div class="fake-qr"><?=e(substr($t['code'],0,4))?><br><?=e(substr($t['code'],4))?></div><strong><?=e($t['code'])?></strong><b><?=number_format((float)$t['price'],2)?> сомонӣ</b></div>
</div>
<?php endforeach; ?>
<button class="button" onclick="window.print()">Чоп кардани чипта</button>
</div>
<?php require 'includes/footer.php'; ?>
