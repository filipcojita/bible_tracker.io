<?php
include 'db.php';
include 'auth.php';
session_start();

checkPersistentLogin();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $guess1 = trim($_POST['guess1']);
    $guess2 = trim($_POST['guess2']);
    $guess3 = trim($_POST['guess3']);

    // Create table if not exists
    $create_table = "CREATE TABLE IF NOT EXISTS camp_guesses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        guess1 VARCHAR(255),
        guess2 VARCHAR(255),
        guess3 VARCHAR(255),
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $conn->query($create_table);

    // Check if user already submitted
    $check = $conn->prepare("SELECT id FROM camp_guesses WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update existing
        $stmt = $conn->prepare("UPDATE camp_guesses SET guess1 = ?, guess2 = ?, guess3 = ?, submitted_at = CURRENT_TIMESTAMP WHERE user_id = ?");
        $stmt->bind_param("sssi", $guess1, $guess2, $guess3, $user_id);
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO camp_guesses (user_id, guess1, guess2, guess3) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $guess1, $guess2, $guess3);
    }

    if ($stmt->execute()) {
        $message = "Ghicirile tale au fost trimise cu succes!";
    } else {
        $message = "Eroare la trimitere: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghiciri Tabără - Bible Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success text-white text-center">
                    <h3><?php echo isset($message) ? $message : "Trimite Ghicirile"; ?></h3>
                </div>
                <div class="card-body text-center">
                    <p>Mulțumim pentru ghiciri! Vom dezvălui locația mai târziu.</p>
                    <a href="index.php" class="btn btn-primary">Înapoi la Acasă</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>