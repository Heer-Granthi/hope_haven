<?php
// auth/logout.php
require_once '../includes/functions.php';
session_destroy();
header("Location: /hope_haven/auth/login.php");
exit();
?>
