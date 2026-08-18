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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- QR Scanner -->
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>

    <style>
        body {
            background: #111;
            color: white;
            text-align: center;
            font-family: Arial;
        }

        .container {
            margin-top: 50px;
        }

        #reader {
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
        }

        .btn {
            border-radius: 20px;
            padding: 10px 25px;
            margin: 5px;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>📷 Scanează Codul QR pentru Prezență</h2>

    <div id="reader"></div>

    <p id="status">Apasă butonul pentru a deschide camera.</p>

    <button id="startCamera" class="btn btn-primary">Deschide Cameră</button>
    <button id="switchCamera" class="btn btn-warning" style="display:none;">🔄 Schimbă Camera</button>

    <br><br>

    <a href="dashboard.php" class="btn btn-secondary">Înapoi la Dashboard</a>

</div>

<script>

let html5QrCode;
let cameras = [];
let currentCameraIndex = 0;

const config = {
    fps: 10,
    qrbox: 250
};

// START CAMERA
document.getElementById('startCamera').addEventListener('click', function () {

    document.getElementById('status').textContent = 'Se pornește camera...';

    html5QrCode = new Html5Qrcode("reader");

    Html5Qrcode.getCameras().then(devices => {

        if (devices && devices.length) {

            cameras = devices;

            // Try to use back camera first
            currentCameraIndex = 0;

            for (let i = 0; i < cameras.length; i++) {
                let label = cameras[i].label.toLowerCase();
                if (label.includes("back") || label.includes("rear")) {
                    currentCameraIndex = i;
                    break;
                }
            }

            startScanner(cameras[currentCameraIndex].id);

            document.getElementById('startCamera').style.display = 'none';
            document.getElementById('switchCamera').style.display = 'inline-block';

        } else {
            document.getElementById('status').textContent = 'Nu există camere disponibile.';
        }

    }).catch(err => {
        document.getElementById('status').textContent = 'Eroare: ' + err;
    });

});

// FUNCTION TO START SCANNER
function startScanner(cameraId) {

    html5QrCode.start(
        cameraId,
        config,
        qrCodeMessage => {

            document.getElementById('status').textContent = 'Cod detectat!';

            html5QrCode.stop().then(() => {

                if (qrCodeMessage.includes("attendance.php")) {
                    window.location.href = qrCodeMessage;
                } else {
                    alert("Cod QR invalid!");
                    location.reload();
                }

            });

        },
        errorMessage => {
            // ignore errors
        }
    );

    document.getElementById('status').textContent = 'Cameră activă. Scanează codul QR.';
}

// SWITCH CAMERA
document.getElementById('switchCamera').addEventListener('click', function () {

    if (!cameras.length) return;

    currentCameraIndex = (currentCameraIndex + 1) % cameras.length;

    html5QrCode.stop().then(() => {
        startScanner(cameras[currentCameraIndex].id);
    });

});

</script>

</body>
</html>