<?php
require_once '../config/database.php';require_once '../includes/functions.php';require_admin();
$users=$pdo->query("SELECT id,full_name,email,phone,role,created_at FROM users ORDER BY created_at DESC")->fetchAll();
$pageTitle='Истифодабарандагон';$assetPrefix='../';$homePrefix='../';require '../includes/header.php';
?>
<h1>Истифодабарандагон</h1><div class="table-wrap"><table><tr><th>Ному насаб</th><th>Email</th><th>Телефон</th><th>Нақш</th><th>Санаи бақайдгирӣ</th></tr><?php foreach($users as $u):?><tr><td><?=e($u['full_name'])?></td><td><?=e($u['email'])?></td><td><?=e($u['phone'])?></td><td><?=e($u['role'])?></td><td><?=e($u['created_at'])?></td></tr><?php endforeach;?></table></div>
<?php require '../includes/footer.php'; ?>
