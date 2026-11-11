<?php
require_once '../config/database.php';require_once '../includes/functions.php';require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$id=(int)($_POST['id']??0);$stmt=$pdo->prepare("UPDATE movies SET is_active=0 WHERE id=?");$stmt->execute([$id]);set_flash('Филм аз рӯйхати фаъол хориҷ шуд.');header('Location: movies.php');exit;}
$movies=$pdo->query("SELECT * FROM movies ORDER BY created_at DESC")->fetchAll();
$pageTitle='Филмҳо';$assetPrefix='../';$homePrefix='../';require '../includes/header.php';
?>
<div class="section-heading"><h1>Идоракунии филмҳо</h1><a class="button" href="add_movie.php">+ Иловаи филм</a></div>
<div class="table-wrap"><table><tr><th>Ном</th><th>Жанр</th><th>Давомнокӣ</th><th>Ҳолат</th><th>Амал</th></tr>
<?php foreach($movies as $m):?><tr><td><?=e($m['title'])?></td><td><?=e($m['genre'])?></td><td><?=$m['duration']?> дақ.</td><td><?=$m['is_active']?'Фаъол':'Ғайрифаъол'?></td><td><form method="post" onsubmit="return confirm('Филмро ғайрифаъол мекунед?')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$m['id']?>"><button class="small-button">Ғайрифаъол</button></form></td></tr><?php endforeach;?>
</table></div>
<?php require '../includes/footer.php'; ?>
