<?php
require_once '../config/database.php';require_once '../includes/functions.php';require_admin();$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$movie=(int)$_POST['movie_id'];$hall=(int)$_POST['hall_id'];$starts=str_replace('T',' ',$_POST['starts_at']??'');$price=(float)$_POST['price'];
try{$stmt=$pdo->prepare("INSERT INTO showtimes(movie_id,hall_id,starts_at,price) VALUES(?,?,?,?)");$stmt->execute([$movie,$hall,$starts,$price]);set_flash('Сеанс илова шуд.');header('Location: showtimes.php');exit;}catch(Throwable $e){$error='Сеанс илова нашуд. Маълумотро санҷед.';}}
$movies=$pdo->query("SELECT id,title FROM movies WHERE is_active=1 ORDER BY title")->fetchAll();$halls=$pdo->query("SELECT id,name FROM halls")->fetchAll();
$shows=$pdo->query("SELECT s.*,m.title,h.name hall_name FROM showtimes s JOIN movies m ON m.id=s.movie_id JOIN halls h ON h.id=s.hall_id ORDER BY s.starts_at DESC")->fetchAll();
$pageTitle='Сеансҳо';$assetPrefix='../';$homePrefix='../';require '../includes/header.php';
?>
<h1>Идоракунии сеансҳо</h1><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<div class="auth-card wide"><h2>Иловаи сеанс</h2><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Филм<select name="movie_id" required><?php foreach($movies as $m):?><option value="<?=$m['id']?>"><?=e($m['title'])?></option><?php endforeach;?></select></label>
<label>Толор<select name="hall_id" required><?php foreach($halls as $h):?><option value="<?=$h['id']?>"><?=e($h['name'])?></option><?php endforeach;?></select></label>
<label>Сана ва вақт<input type="datetime-local" name="starts_at" required></label><label>Нархи чипта (сомонӣ)<input type="number" name="price" step="0.01" min="0" required></label><button class="button">Илова кардан</button></form></div>
<div class="table-wrap"><table><tr><th>Филм</th><th>Толор</th><th>Сана ва вақт</th><th>Нарх</th></tr><?php foreach($shows as $s):?><tr><td><?=e($s['title'])?></td><td><?=e($s['hall_name'])?></td><td><?=e($s['starts_at'])?></td><td><?=number_format((float)$s['price'],2)?> сом.</td></tr><?php endforeach;?></table></div>
<?php require '../includes/footer.php'; ?>
