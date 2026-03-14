<?php
session_start();
include 'auth.php';

checkPersistentLogin();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner QR - Bible Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <style>
        body {
            background: #111;
            color: white;
            text-align: center;
            font-family: Arial;
        }
        #preview {
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
        }
        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Scanează Codul QR pentru Prezență</h2>
    <video id="preview"></video>
    <p id="status">Inițializare cameră...</p>
    <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Înapoi la Dashboard</a>
</div>

<script>
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });

    scanner.addListener('scan', function (content) {
        // When QR code is scanned, redirect to attendance.php with the token
        window.location.href = content;
    });

    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]); // Start with the first camera
            document.getElementById('status').textContent = 'Cameră activă. Scanează codul QR.';
        } else {
            document.getElementById('status').textContent = 'Nu s-a găsit nicio cameră.';
        }
    }).catch(function (e) {
        document.getElementById('status').textContent = 'Eroare la accesarea camerei: ' + e;
    });
</script>

</body>
</html>