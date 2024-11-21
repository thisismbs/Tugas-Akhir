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
    $user = $admin->getUserById($user_id);
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        if ($admin->updateUser($user_id, $username, $password, $role)) {
            $_SESSION['success_message'] = "Pengguna berhasil diperbarui!";
            header("Location: manage_users.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui pengguna.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mt-2">Edit Pengguna</h5>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" value="<?php echo $user['password']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                            <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                        </select>
                    </div>
                    <a href="manage_users.php" class="btn btn-secondary btn-sm">Kembali</a>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>