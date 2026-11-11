<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_login();
if($_SERVER['REQUEST_METHOD']!=='POST') { header('Location: index.php'); exit; }
verify_csrf();
$showId=(int)($_POST['showtime_id']??0);
$seatIds=array_values(array_unique(array_filter(array_map('intval',$_POST['seats']??[]))));
if(!$seatIds){ set_flash('Лутфан ҷой интихоб кунед.','error'); header("Location: seats.php?showtime_id=$showId"); exit; }
try {
  $pdo->beginTransaction();
  $stmt=$pdo->prepare("SELECT s.id,s.price,s.starts_at,s.movie_id,m.title FROM showtimes s JOIN movies m ON m.id=s.movie_id WHERE s.id=? AND s.starts_at>=NOW() FOR UPDATE");
  $stmt->execute([$showId]); $show=$stmt->fetch();
  if(!$show) throw new Exception('Сеанс дастрас нест.');
  $placeholders=implode(',',array_fill(0,count($seatIds),'?'));
  $params=array_merge([$showId],$seatIds);
  $stmt=$pdo->prepare("SELECT seat_id FROM tickets WHERE showtime_id=? AND seat_id IN ($placeholders) AND status IN ('reserved','paid') FOR UPDATE");
  $stmt->execute($params);
  if($stmt->fetch()) throw new Exception('Яке аз ҷойҳои интихобшуда аллакай банд шудааст. Ҷойи дигар интихоб кунед.');
  $stmt=$pdo->prepare("SELECT id FROM seats WHERE id IN ($placeholders)");
  $stmt->execute($seatIds);
  if(count($stmt->fetchAll())!==count($seatIds)) throw new Exception('Ҷойи нишасти нодуруст интихоб шудааст.');
  $total=count($seatIds)*(float)$show['price'];
  $stmt=$pdo->prepare("INSERT INTO orders(user_id,total_amount,status) VALUES(?,?,'confirmed')");
  $stmt->execute([$_SESSION['user']['id'],$total]); $orderId=(int)$pdo->lastInsertId();
  $insert=$pdo->prepare("INSERT INTO tickets(order_id,showtime_id,seat_id,code,price,status) VALUES(?,?,?,?,?,'paid')");
  foreach($seatIds as $seatId) $insert->execute([$orderId,$showId,$seatId,strtoupper(bin2hex(random_bytes(4))),(float)$show['price']]);
  $pdo->commit();
  header("Location: ticket.php?order_id=$orderId"); exit;
} catch(Throwable $e) {
  if($pdo->inTransaction()) $pdo->rollBack();
  set_flash($e->getMessage(),'error'); header("Location: seats.php?showtime_id=$showId"); exit;
}
