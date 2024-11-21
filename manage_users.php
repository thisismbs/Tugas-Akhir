<?php
session_start();
include 'connect-database.php';
include 'admin.php';

// Memastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki akses ke halaman ini.";
    header("Location: login.php");
    exit();
}

$admin = new Admin($conn);
$users = $admin->getAllUsers(); // Fungsi untuk mengambil semua data pengguna

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">     
        
        <!-- Card untuk Daftar Pengguna -->
        <div class="card">
            <div class="card-header">
                <h4>Daftar Pengguna</h4>
                <a href="add_user.php" class="btn btn-primary mb-2"> + Tambah Pegguna</a>
                <a href="admin_dashboard.php" class="btn btn-secondary mb-2">Kembali</a>
            </div>
            <div class="card-body">
                
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['role']; ?></td>
                                <td>
                                    <a href="edit_user.php?user_id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_user.php?user_id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
