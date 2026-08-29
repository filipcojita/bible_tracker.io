<?php
require_once __DIR__ . '/../core/db.php';
session_start();

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role']; // ✅ Store the role in session

            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                $update_stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $update_stmt->bind_param("si", $token, $row['id']);
                $update_stmt->execute();
                $update_stmt->close();
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), "/"); // 30 days
            }

            header("Location: /dashboard/dashboard.php");
            exit();
        } else {
            $error = "Parolă incorectă!";
        }
    } else {
        $error = "Utilizator inexistent!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare - Bible Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #101820 0%, #183153 45%, #c8102e 100%);
            font-family: Arial, sans-serif;
        }
        .auth-card {
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(0,0,0,0.25);
        }
        .card-header {
            background: linear-gradient(135deg, #101820 0%, #183153 50%, #c8102e 100%) !important;
            border: 0;
            padding: 1.25rem 1.5rem;
        }
        .btn-success {
            background: linear-gradient(135deg, #183153 0%, #c8102e 100%) !important;
            border: 0;
        }
        .card-footer a {
            color: #183153;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <div class="card auth-card">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Autentifică-te</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/auth/login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nume utilizator</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Parolă</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ține-mă conectat</label>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Autentifică-te</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p>Nu ai un cont? <a href="/auth/register.php">Înregistrează-te aici</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
                            