<?php
session_start();
include 'connect-database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki akses ke halaman ini.";
    header("Location: login.php");
    exit();
}

$attendance_id = $_GET['attendance_id'];
$status = $_GET['status'];
$event_id = $_GET['event_id'];

if (empty($attendance_id) || empty($status) || empty($event_id)) {
    $_SESSION['error_message'] = "Data tidak lengkap.";
    header("Location: attendance.php?event_id=$event_id");
    exit();
}

$query = "UPDATE attendance SET proof_status = '$status' WHERE id = $attendance_id";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: attendance.php?event_id=$event_id");
    exit();
} else {
    $_SESSION['error_message'] = "Terjadi kesalahan saat memperbarui status bukti.";
    header("Location: attendance.php?event_id=$event_id");
    exit();
}
?>
