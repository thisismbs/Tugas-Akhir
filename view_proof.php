<?php
session_start();
include 'connect-database.php';

$proof_url = isset($_GET['proof_url']) ? urldecode($_GET['proof_url']) : null;

if ($proof_url === null || !file_exists($proof_url)) {
    $_SESSION['error_message'] = "Bukti tidak ditemukan.";
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Bukti Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .proof-image {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Lihat Bukti Kehadiran</h3>
                    </div>
                    <div class="card-body text-center">
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <img src="<?php echo $proof_url; ?>" alt="Bukti Kehadiran" class="proof-image">
                        </div>
                        <a href="admin_dashboard.php" class="btn btn-secondary w-100 mt-3">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
