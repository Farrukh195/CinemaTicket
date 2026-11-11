<?php
require_once 'config/database.php'; require_once 'includes/functions.php';
if(is_logged_in()){header('Location: index.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){ verify_csrf();
  $name=trim($_POST['full_name']??''); $email=trim($_POST['email']??''); $phone=trim($_POST['phone']??''); $password=$_POST['password']??'';
  if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<6){$error='Маълумотро дуруст пур кунед. Парол бояд на кам аз 6 аломат бошад.';}
  else {try{$stmt=$pdo->prepare("INSERT INTO users(full_name,email,phone,password,role) VALUES(?,?,?,?, 'customer')");$stmt->execute([$name,$email,$phone,password_hash($password,PASSWORD_DEFAULT)]);set_flash('Бақайдгирӣ бомуваффақият анҷом ёфт. Акнун ворид шавед.');header('Location: login.php');exit;}catch(PDOException $e){$error='Ин почтаи электронӣ аллакай истифода шудааст.';}}
}
$pageTitle='Бақайдгирӣ';$assetPrefix='';$homePrefix='';require 'includes/header.php';
?>
<div class="auth-card"><p class="eyebrow">ҲИСОБИ НАВ</p><h1>Бақайдгирӣ</h1><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Ному насаб<input name="full_name" required maxlength="120"></label>
<label>Почтаи электронӣ<input type="email" name="email" required></label>
<label>Телефон<input name="phone"></label>
<label>Парол<input type="password" name="password" required minlength="6"></label>
<button class="button full">Сабти ном</button></form><p class="muted">Ҳисоб доред? <a href="login.php">Ворид шавед</a></p></div>
<?php require 'includes/footer.php'; ?>
