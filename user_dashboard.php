<?php
session_start();
include 'connect-database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];  

// daftar acara yang sudah dihadiri oleh user
$stmt_attended = $conn->prepare("
    SELECT events.name, events.date, events.location, attendance.proof_status 
    FROM attendance 
    JOIN events ON attendance.event_id = events.id 
    WHERE attendance.user_id = ?");
$stmt_attended->bind_param("i", $user_id);
$stmt_attended->execute();
$attended_events = $stmt_attended->get_result();

// daftar acara yang belum dihadiri oleh user
$stmt_available = $conn->prepare("
    SELECT * FROM events 
    WHERE id NOT IN (
        SELECT event_id FROM attendance WHERE user_id = ?)");
$stmt_available->bind_param("i", $user_id);
$stmt_available->execute();
$available_events = $stmt_available->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>User Dashboard</h1>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        
        <!-- Daftar Acara yang Sudah Dikonfirmasi -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Daftar Acara yang Sudah Dikonfirmasi</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Acara</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($attended_events->num_rows > 0): ?>
                            <?php while ($event = $attended_events->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $event['name']; ?></td>
                                <td><?php echo $event['date']; ?></td>
                                <td><?php echo $event['location']; ?></td>
                                <td>
                                    <?php
                                        $status = $event['proof_status'];
                                        if ($status === 'pending') {
                                            echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                        } elseif ($status === 'approved') {
                                            echo "<span class='badge bg-success'>Disetujui</span>";
                                        } elseif ($status === 'rejected') {
                                            echo "<span class='badge bg-danger'>Ditolak</span>";
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada acara yang dikonfirmasi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daftar Acara yang Belum Dikonfirmasi -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Daftar Acara yang Belum Dikonfirmasi</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Acara</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($available_events->num_rows > 0): ?>
                            <?php while ($event = $available_events->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $event['name']; ?></td>
                                <td><?php echo $event['date']; ?></td>
                                <td><?php echo $event['location']; ?></td>
                                <td>
                                    <a href="confirm_attendance.php?event_id=<?php echo $event['id']; ?>" class="btn btn-success">Konfirmasi Kehadiran</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada acara yang belum dihadiri.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
