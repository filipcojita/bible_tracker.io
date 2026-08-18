<?php
session_start();
include 'db.php';
include 'auth.php';

checkPersistentLogin();

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid submission ID.");
}

$id = $_GET['id'];

// Fetch submission
$sql = "SELECT s.*, u.username 
        FROM submissions s
        JOIN users u ON s.user_id = u.id
        WHERE s.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Submission not found.");
}

$submission = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualizare Submisiune</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">📖 Submisiune #<?= $submission['id'] ?></h4>
        </div>

        <div class="card-body">
            <p><strong>Utilizator:</strong> <?= htmlspecialchars($submission['username']) ?></p>
            <p><strong>Data:</strong> <?= $submission['date'] ?></p>
            <p><strong>Pasaj Biblic:</strong> <?= htmlspecialchars($submission['passage']) ?></p>

            <hr>

            <h5>Reflecție</h5>
            <p style="white-space: pre-line;">
                <?= htmlspecialchars($submission['reflection']) ?>
            </p>

            <hr>

            <p><small>Trimis la: <?= $submission['submitted_at'] ?></small></p>
        </div>
    </div>

    <div class="mt-4 text-center">
        <a href="admin.php" class="btn btn-secondary">⬅ Înapoi la Admin</a>
    </div>
</div>

</body>
</html>
