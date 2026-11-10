<?php
require_once 'includes/functions.php';
$_SESSION=[]; session_destroy();
header('Location: index.php'); exit;
