<?php
require_once '../config/database.php';require_once '../includes/functions.php';require_admin();$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$title=trim($_POST['title']??'');$genre=trim($_POST['genre']??'');$duration=(int)($_POST['duration']??0);$rating=trim($_POST['rating']??'');$poster=trim($_POST['poster']??'');$description=trim($_POST['description']??'');
if($title===''||$genre===''||$duration<1){$error='Майдонҳои ҳатмиро дуруст пур кунед.';}else{$stmt=$pdo->prepare("INSERT INTO movies(title,genre,duration,rating,poster,description) VALUES(?,?,?,?,?,?)");$stmt->execute([$title,$genre,$duration,$rating,$poster,$description]);set_flash('Филм илова шуд.');header('Location: movies.php');exit;}}
$pageTitle='Иловаи филм';$assetPrefix='../';$homePrefix='../';require '../includes/header.php';
?>
<div class="auth-card wide"><h1>Иловаи филми нав</h1><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Номи филм<input name="title" required></label><label>Жанр<input name="genre" required placeholder="Драма, Комедия..."></label>
<label>Давомнокӣ (дақиқа)<input type="number" name="duration" min="1" required></label><label>Рейтинг<input name="rating" placeholder="8.5"></label>
<label>Суроғаи расми постер<input name="poster" type="url" placeholder="https://..."></label><label>Тавсиф<textarea name="description" rows="5"></textarea></label>
<button class="button">Сабт кардан</button></form></div>
<?php require '../includes/footer.php'; ?>
