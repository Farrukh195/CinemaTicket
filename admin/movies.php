<?php
require_once '../config/database.php';require_once '../includes/functions.php';require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$id=(int)($_POST['id']??0);$stmt=$pdo->prepare("UPDATE movies SET is_active=0 WHERE id=?");$stmt->execute([$id]);set_flash('Филм аз рӯйхати фаъол хориҷ шуд.');header('Location: movies.php');exit;}
$movies=$pdo->query("SELECT * FROM movies ORDER BY created_at DESC")->fetchAll();
$pageTitle='Филмҳо';$assetPrefix='../';$homePrefix='../';require '../includes/header.php';
?>
<div class="section-heading"><h1>Идоракунии филмҳо</h1><a class="button" href="add_movie.php">+ Иловаи филм</a></div>
<div class="table-wrap"><table><tr><th>Расм</th><th>Ном</th><th>Жанр</th><th>Давомнокӣ</th><th>Ҳолат</th><th>Амал</th></tr>
<?php foreach($movies as $m):
  $src = '';
  if (!empty($m['poster'])) $src = preg_match('#^https?://#i', $m['poster']) ? $m['poster'] : '../' . $m['poster'];
?><tr>
<td><?php if($src): ?><img src="<?=e($src)?>" alt="" style="width:48px;height:68px;object-fit:cover;border-radius:6px;display:block;"><?php else: ?><span class="muted">—</span><?php endif; ?></td>
<td><?=e($m['title'])?></td><td><?=e($m['genre'])?></td><td><?=$m['duration']?> дақ.</td><td><?=$m['is_active']?'Фаъол':'Ғайрифаъол'?></td>
<td style="white-space:nowrap;">
<a class="small-button" href="edit_movie.php?id=<?=(int)$m['id']?>">Таҳрир / Расм</a>
<?php if($m['is_active']): ?>
<form method="post" style="display:inline" onsubmit="return confirm('Филмро ғайрифаъол мекунед?')"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$m['id']?>"><button class="small-button">Ғайрифаъол</button></form>
<?php endif; ?>
</td></tr><?php endforeach;?>
</table></div>
<?php require '../includes/footer.php'; ?>
