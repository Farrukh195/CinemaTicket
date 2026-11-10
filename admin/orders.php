<?php
require_once '../config/database.php';require_once '../includes/functions.php';require_admin();
$orders=$pdo->query("SELECT o.*,u.full_name,u.email,COUNT(t.id) ticket_count FROM orders o JOIN users u ON u.id=o.user_id LEFT JOIN tickets t ON t.order_id=o.id GROUP BY o.id ORDER BY o.created_at DESC")->fetchAll();
$pageTitle='Фармоишҳо';$assetPrefix='../';$homePrefix='../';require '../includes/header.php';
?>
<h1>Фармоишҳо</h1><div class="table-wrap"><table><tr><th>№</th><th>Мизоҷ</th><th>Почта</th><th>Чиптаҳо</th><th>Маблағ</th><th>Сана</th><th>Ҳолат</th></tr>
<?php foreach($orders as $o):?><tr><td>#<?=$o['id']?></td><td><?=e($o['full_name'])?></td><td><?=e($o['email'])?></td><td><?=$o['ticket_count']?></td><td><?=number_format((float)$o['total_amount'],2)?> сом.</td><td><?=e($o['created_at'])?></td><td><?=e($o['status'])?></td></tr><?php endforeach;?>
</table></div><?php require '../includes/footer.php'; ?>
