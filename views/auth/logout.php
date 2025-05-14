<?php
session_start();
$_SESSION['logout_success'] = "Berhasil logout!";
session_destroy();
header("Location: login.php");
exit;
