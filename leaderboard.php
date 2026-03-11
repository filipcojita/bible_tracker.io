<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

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
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Leaderboard - Bible Tracker</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/bible_tracker/styles.css">

</head>

<body>

<div class="container mt-5">

<div class="card shadow">
<div class="card-body">

<h2 class="mb-4 text-center">🏆 Leaderboard</h2>

<table class="table leaderboard-table align-middle text-center">
<thead class="table-dark">
<tr>
<th>Rank</th>
<th>User</th>
<th>Submissions</th>
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
<td class="rank"><?= $rank ?></td>
<td class="username"><?= htmlspecialchars($row['username']) ?></td>
<td><?= $row['total_submissions'] ?></td>
</tr>

<?php
$rank++;
endwhile;
?>

</tbody>
</table>

<div class="text-center mt-4">

<a href="dashboard.php" class="btn btn-primary">Trimite un raspuns</a>

<a href="index.php" class="btn btn-secondary">ACASĂ</a>

<a href="logout.php" class="btn btn-danger">Deconectează-te</a>

</div>

</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>