<?php
session_start();
include 'connect-database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki akses ke halaman tersebut.";
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM events");
$stmt->execute();
$events = $stmt->get_result();

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    $attendance_stmt = $conn->prepare("SELECT attendance.id, users.username AS user_name, attendance.status, attendance.proof, attendance.proof_status 
                                       FROM attendance 
                                       JOIN users ON attendance.user_id = users.id 
                                       WHERE attendance.event_id = ?");
    $attendance_stmt->bind_param("i", $event_id);
    $attendance_stmt->execute();
    $attendance_data = $attendance_stmt->get_result();
} else {
    
    $attendance_data = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Dashboard Admin</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                        <?php endif; ?>
                        
                        <!--  Daftar Acara -->
                        <a href="add_event.php" class="btn btn-success mb-3">Tambah Acara Baru</a>
                        <a href="manage_users.php" class="btn btn-primary mb-3">Kelola Pengguna</a>
                        <a href="logout.php" class="btn btn-danger btn-sm float-end">Logout</a>
                        <h4>Daftar Acara</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Acara</th>
                                    <th>Tanggal</th>
                                    <th>Lokasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($event = $events->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $event['name']; ?></td>
                                        <td><?php echo $event['date']; ?></td>
                                        <td><?php echo $event['location']; ?></td>
                                        <td>
                                            <a href="edit_event.php?event_id=<?php echo $event['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="delete_event.php?event_id=<?php echo $event['id']; ?>" class="btn btn-danger btn-sm">Hapus</a>
                                            <a href="attendance.php?event_id=<?php echo $event['id']; ?>" class="btn btn-info btn-sm">Lihat Kehadiran</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
