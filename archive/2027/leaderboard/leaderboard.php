<?php
session_start();
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/auth.php';

checkPersistentLogin();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit();
}

$currentUserId = $_SESSION['user_id'];

$sql = "
    SELECT users.id, users.username, COUNT(submissions.date) AS total_submissions
    FROM submissions
    JOIN users ON submissions.user_id = users.id
    GROUP BY users.id
    ORDER BY total_submissions DESC
    LIMIT 10
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Clasament - Bible Tracker</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="styles.css">

<style>
    .leaderboard-card {
        border: 2px solid #007bff;
        border-radius: 15px;
    }
    .leaderboard-table thead {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }
    .gold {
        background: linear-gradient(90deg, #fff8dc, #ffd70033) !important;
    }
    .silver {
        background: linear-gradient(90deg, #f8f9fa, #c0c0c033) !important;
    }
    .bronze {
        background: linear-gradient(90deg, #fdf6ec, #cd7f3233) !important;
    }
    .current-user {
        border-left: 6px solid #007bff;
        background-color: #e7f3ff !important;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>

</head>

<body>

<?php $activePage = 'leaderboard'; include __DIR__ . '/../dashboard/navbar.php'; ?>

<div class="container mt-4">

<div class="card shadow leaderboard-card">
<div class="card-body">

<h2 class="mb-4 text-center text-primary">🏆 Clasament</h2>

<table class="table leaderboard-table align-middle text-center">
<thead class="table-dark">
<tr>
<th>Rang</th>
<th>Utilizator</th>
<th>Puncte (meditații)</th>
</tr>
</thead>

<tbody>

<?php
$rank = 1;

while ($row = $result->fetch_assoc()):

$rowClass = "";

/* Top 3 medals */
if ($rank == 1) $rowClass = "gold";
elseif ($rank == 2) $rowClass = "silver";
elseif ($rank == 3) $rowClass = "bronze";

/* Logged user highlight */
if ($row['id'] == $currentUserId) {
    $rowClass .= " current-user";
}
?>

<tr class="<?= $rowClass ?>">
<td class="rank">
    <?php if ($rank == 1) echo '🥇'; elseif ($rank == 2) echo '🥈'; elseif ($rank == 3) echo '🥉'; else echo $rank; ?>
</td>
<td class="username"><?= htmlspecialchars($row['username']) ?></td>
<td><strong><?= $row['total_submissions'] ?></strong></td>
</tr>

<?php
$rank++;
endwhile;
?>

</tbody>
</table>

<div class="text-center mt-4 d-flex justify-content-center gap-3">

<a href="/dashboard/dashboard.php" class="btn btn-primary btn-lg"><i class="bi bi-send"></i> Trimite un răspuns</a>

<a href="/public/index.php" class="btn btn-outline-secondary btn-lg"><i class="bi bi-house"></i> Acasă</a>

</div>

</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>