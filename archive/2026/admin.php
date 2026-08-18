<?php
session_start();
include 'db.php';
include 'auth.php';

checkPersistentLogin();

// Only admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all users
$sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

// Attendance leaderboard
$attendance_sql = "
    SELECT u.username, COUNT(a.id) as total_attendance
    FROM users u
    LEFT JOIN attendance a ON u.id = a.user_id
    GROUP BY u.id
    ORDER BY total_attendance DESC
";
$attendance_result = $conn->query($attendance_sql);

// Fetch submissions for selected user
$submissions_result = null;
$user_id = null;

if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = (int) $_GET['user_id'];

    $submissions_sql = "SELECT s.id, u.username, s.date, s.passage, s.reflection, s.submitted_at 
                        FROM submissions s
                        JOIN users u ON s.user_id = u.id
                        WHERE s.user_id = ?
                        ORDER BY s.submitted_at DESC";

    $stmt = $conn->prepare($submissions_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $submissions_result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>

<div class="container mt-5">

    <h2 class="text-center mb-4">📊 Panou de administrare</h2>

    <!-- Quick Links -->
    <div class="mb-4 text-center">
        <a href="sermon_admin.php" class="btn btn-info btn-lg me-2">
            <i class="bi bi-file-earmark-text"></i> Statistici Sermon
        </a>
        <button class="btn btn-light btn-lg" disabled>
            <i class="bi bi-book"></i> Meditații Biblice
        </button>
    </div>

    <!-- USER SELECT -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">👥 Selectează utilizatorul</h5>
        </div>
        <div class="card-body">
            <form method="get">
                <div class="mb-3">
                    <label for="user_id" class="form-label">Alege un utilizator:</label>
                    <select name="user_id" id="user_id" class="form-control">
                        <option value="">Selectează un utilizator</option>

                        <?php while ($row = $result->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>" <?= ($user_id == $row['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['username']) ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Vizualizează Submisiuni</button>
            </form>
        </div>
    </div>

    <!-- SUBMISSIONS TABLE -->
    <?php if ($submissions_result !== null): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">📖 Submisiuni utilizator</h5>
            </div>

            <div class="card-body">

                <?php if ($submissions_result->num_rows > 0): ?>

                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Utilizator</th>
                                <th>Data</th>
                                <th>Pasaj</th>
                                <th>Reflecție</th>
                                <th>Trimis la</th>
                                <th>Acțiuni</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php while ($row = $submissions_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= $row['date'] ?></td>
                                <td><?= htmlspecialchars($row['passage']) ?></td>
                                <td><?= htmlspecialchars(substr($row['reflection'], 0, 50)) ?>...</td>
                                <td><?= $row['submitted_at'] ?></td>
                                <td>
                                    <a href="view_submission.php?id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-primary"
                                       target="_blank">
                                        👁️ Vezi
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>

                    </table>

                <?php else: ?>
                    <p>Nu există submisiuni pentru acest utilizator.</p>
                <?php endif; ?>

            </div>
        </div>
    <?php endif; ?>

    <!-- ATTENDANCE -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">📊 Situația prezențelor</h5>
        </div>

        <div class="card-body">

            <?php if ($attendance_result->num_rows > 0): ?>

                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Utilizator</th>
                            <th>Număr prezențe</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php 
                        $rank = 1;
                        while ($row = $attendance_result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $rank++ ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><strong><?= $row['total_attendance'] ?></strong></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>

            <?php else: ?>
                <p>Nu există date de prezență.</p>
            <?php endif; ?>

        </div>
    </div>

    <!-- NAVIGATION -->
    <div class="mt-4 text-center">
        <a href="dashboard.php" class="btn btn-secondary">⬅ Înapoi la Dashboard</a>
        <a href="logout.php" class="btn btn-danger">🔴 Deconectează-te</a>
    </div>

</div>

</body>
</html>