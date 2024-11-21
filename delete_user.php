<?php
session_start();
include 'connect-database.php';
include 'admin.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki akses ke halaman ini.";
    header("Location: login.php");
    exit();
}

$admin = new Admin($conn);

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    if ($admin->deleteUser($user_id)) {
        $_SESSION['success_message'] = "Pengguna berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus pengguna.";
    }
    header("Location: manage_users.php");
    exit();
}
