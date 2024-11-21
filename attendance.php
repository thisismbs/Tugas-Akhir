<?php
session_start();
include 'connect-database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki akses ke halaman ini.";
    header("Location: login.php");
    exit();
}

$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : null;

if ($event_id === null) {
    $_SESSION['error_message'] = "Event tidak ditemukan.";
    header("Location: admin_dashboard.php");
    exit();
}

// Query data kehadiran
$attendance_query = "SELECT attendance.id, users.username AS user_name, attendance.status, attendance.proof, attendance.proof_status 
                     FROM attendance 
                     JOIN users ON attendance.user_id = users.id 
                     WHERE attendance.event_id = $event_id";

// Query statistik kehadiran
$stats_query = "SELECT 
                    SUM(CASE WHEN attendance.proof_status = 'approved' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN attendance.proof_status = 'rejected' THEN 1 ELSE 0 END) AS rejected,
                    COUNT(*) AS total
                FROM attendance
                WHERE event_id = $event_id";

// Eksekusi query statistik
$stats_result = mysqli_query($conn, $stats_query);
if (!$stats_result) {
    die('Query Error: ' . mysqli_error($conn));
}

$stats = mysqli_fetch_assoc($stats_result);

// Eksekusi query data kehadiran
$attendance_result = mysqli_query($conn, $attendance_query);
if (!$attendance_result) {
    die('Query Error: ' . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kehadiran Acara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .printable-area, .printable-area * {
                visibility: visible;
            }
            .printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
        .card-group {
            display: flex;
            justify-content: space-between;
        }
        .card {
            width: 100%;
            margin-bottom: 20px;
        }
        .card-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-footer {
            display: flex;
            justify-content: flex-end;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Card Utama -->
        <div class="card">
            <div class="card-header text-center">
                <h4>Data Konfirmasi</h4>
            </div>
            <div class="card-body">
                <a href="admin_dashboard.php" class="btn btn-secondary">Kembali</a>

                <button class="btn btn-primary" onclick="window.print()">Cetak Halaman</button>
            </div>

            <!-- Statistik Kehadiran-->
            <div class="card-body">
                <div class="card-group">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Kehadiran</h5>
                            <p class="card-text"><?php echo $stats['total']; ?> Orang</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Disetujui</h5>
                            <p class="card-text"><?php echo $stats['approved']; ?> Orang</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Ditolak</h5>
                            <p class="card-text"><?php echo $stats['rejected']; ?> Orang</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Kehadiran -->
            <div class="card-body printable-area">
                <table class="table table-bordered mt-2">
                    <thead>
                        <tr>
                            <th>Nama Pengguna</th>
                            <th>Status Kehadiran</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($attendance = mysqli_fetch_assoc($attendance_result)):
                        ?>
                            <tr>
                                <td><?php echo $attendance['user_name']; ?></td>
                                <td><?php echo ucfirst($attendance['status']); ?></td>
                                <td>
                                    <?php if ($attendance['proof']): ?>
                                        <a href="view_proof.php?proof_url=<?php echo urlencode($attendance['proof']); ?>" target="_blank" class="btn btn-link">Lihat Bukti</a>
                                    <?php else: ?>
                                        Tidak ada bukti.
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($attendance['proof_status'] === 'pending'): ?>
                                        <!-- Menambahkan event_id pada URL -->
                                        <a href="approve_proof.php?attendance_id=<?php echo $attendance['id']; ?>&status=approved&event_id=<?php echo $event_id; ?>" class="btn btn-success">Setujui</a>
                                        <a href="approve_proof.php?attendance_id=<?php echo $attendance['id']; ?>&status=rejected&event_id=<?php echo $event_id; ?>" class="btn btn-danger">Tolak</a>
                                    <?php elseif ($attendance['proof_status'] === 'approved'): ?>
                                        <span class="badge bg-success">Bukti Disetujui</span>
                                    <?php elseif ($attendance['proof_status'] === 'rejected'): ?>
                                        <span class="badge bg-danger">Bukti Ditolak</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-center">
                <small>Data konfirmasi Kehadiran Acara</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// menutup koneksi database
mysqli_free_result($attendance_result);
mysqli_free_result($stats_result);
mysqli_close($conn);
?>
