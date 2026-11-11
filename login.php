<?php
require_once 'config/database.php'; require_once 'includes/functions.php';
if(is_logged_in()){header('Location: index.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$email=trim($_POST['email']??'');$password=$_POST['password']??'';
$stmt=$pdo->prepare("SELECT id,full_name,email,password,role FROM users WHERE email=?");$stmt->execute([$email]);$u=$stmt->fetch();
if($u&&password_verify($password,$u['password'])){session_regenerate_id(true);unset($u['password']);$_SESSION['user']=$u;header('Location: index.php');exit;}else{$error='Почтаи электронӣ ё парол нодуруст аст.';}}
$pageTitle='Воридшавӣ';$assetPrefix='';$homePrefix='';require 'includes/header.php';
?>
<div class="auth-card"><p class="eyebrow">ХУШ ОМАДЕД</p><h1>Воридшавӣ</h1><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Почтаи электронӣ<input type="email" name="email" required></label>
<label>Парол<input type="password" name="password" required></label>
<button class="button full">Ворид шудан</button></form><p class="muted">Ҳисоб надоред? <a href="register.php">Бақайдгирӣ</a></p></div>
<?php require 'includes/footer.php'; ?>
