<?php
session_start();
include 'connect-database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki akses ke halaman ini.";
    header("Location: login.php");
    exit();
}

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Ambil data acara berdasarkan event_id
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    // Jika acara ada, ambil data acara terus tampilin di form
    if ($event) {
        $name = $event['name'];
        $date = $event['date'];
        $location = $event['location'];
    } else {
        $_SESSION['error_message'] = "Acara tidak ditemukan.";
        header("Location: admin_dashboard.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "ID acara tidak valid.";
    header("Location: admin_dashboard.php");
    exit();
}

// Proses update data acara jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $location = $_POST['location'];

    // Update data acara di database
    $stmt = $conn->prepare("UPDATE events SET name = ?, date = ?, location = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $date, $location, $event_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Acara berhasil diperbarui!";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Terjadi kesalahan saat memperbarui acara.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Acara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Edit Acara</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                        <?php endif; ?>

                        <!-- Form edit acara -->
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Acara</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo isset($date) ? $date : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Lokasi</label>
                                <input type="text" class="form-control" id="location" name="location" value="<?php echo isset($location) ? $location : ''; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update</button>
                        </form>

                        
                        <a href="admin_dashboard.php" class="btn btn-secondary w-100 mt-3">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
