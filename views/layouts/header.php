<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /restoran_ramen/views/auth/login.php");
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Kasir</title>
    <link rel="icon" type="image/png" href="/restoran_ramen/public/img/logoweb.png">

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SB Admin & FontAwesome -->
    <link href="/restoran_ramen/sb-admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="/restoran_ramen/sb-admin/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/restoran_ramen/sb-admin/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Font override -->
    <style>
        body, .navbar, .sidebar, .card, .form-control, .btn {
            font-family: 'Poppins', sans-serif !important;
        }
    </style>
</head>
<body id="page-top">
<div id="wrapper">
