<?php
require_once 'config/database.php'; require_once 'includes/functions.php'; require_login();
$stmt=$pdo->prepare("SELECT DISTINCT o.id,o.created_at,o.total_amount,o.status FROM orders o WHERE o.user_id=? ORDER BY o.created_at DESC");
$stmt->execute([$_SESSION['user']['id']]); $orders=$stmt->fetchAll();
$pageTitle='Чиптаҳои ман'; $assetPrefix=''; $homePrefix=''; require 'includes/header.php';
?>
<h1>Чиптаҳои ман</h1><div class="table-wrap"><table><tr><th>Фармоиш</th><th>Сана</th><th>Маблағ</th><th>Ҳолат</th><th></th></tr>
<?php foreach($orders as $o): ?><tr><td>#<?= (int)$o['id']?></td><td><?=e($o['created_at'])?></td><td><?=number_format((float)$o['total_amount'],2)?> сомонӣ</td><td><?=e($o['status'])?></td><td><a href="ticket.php?order_id=<?=(int)$o['id']?>">Дидани чипта</a></td></tr><?php endforeach; ?>
</table><?php if(!$orders): ?><p class="empty">Шумо ҳоло чипта нахаридаед.</p><?php endif; ?></div>
<?php require 'includes/footer.php'; ?>
