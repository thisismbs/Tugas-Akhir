<?php
session_start();
include 'connect-database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = $_GET['event_id'];  

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];  
    $proof = null;

    
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["proof"]["name"]);
        move_uploaded_file($_FILES["proof"]["tmp_name"], $target_file);
        $proof = $target_file; 
    }

    $stmt = $conn->prepare("INSERT INTO attendance (user_id, event_id, status, proof) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $event_id, $status, $proof);
    $stmt->execute();

    header("Location: user_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Konfirmasi Kehadiran untuk Acara: <?php echo $event['name']; ?></h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="status" class="form-label">Status Kehadiran</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="hadir">Hadir</option>
                    <option value="tidak hadir">Tidak Hadir</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="proof" class="form-label">Upload Bukti (Foto)</label>
                <input type="file" name="proof" id="proof" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Konfirmasi Kehadiran</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
