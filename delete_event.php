<?php
session_start();
include 'connect-database.php';
include 'Event.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki akses ke halaman tersebut.";
    header("Location: login.php");
    exit();
}

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    $event = new Event($conn);
    if ($event->deleteEvent($event_id)) {
        $_SESSION['success_message'] = "Acara berhasil dihapus!";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Terjadi kesalahan saat menghapus acara.";
    }
} else {
    $_SESSION['error_message'] = "ID acara tidak ditemukan.";
    header("Location: admin_dashboard.php");
    exit();
}
?>
