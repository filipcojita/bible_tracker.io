<?php
session_start();
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/auth.php';

checkPersistentLogin();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /auth/login.php");
    exit();
}

// Fetch all users
$sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

// Fetch sermon submissions statistics based on selected user (if any)
$submissions_result = null;
$user_stats = null;

if (isset($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    
    // Get user info
    $userSql = "SELECT id, username, email FROM users WHERE id = ?";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bind_param("i", $user_id);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    
    if ($userResult->num_rows > 0) {
        $user_stats = $userResult->fetch_assoc();
    }
    
    // Get all sermon submissions for this user with statistics
    $submissionsSql = "SELECT 
                            ss.id,
                            ss.sermon_date,
                            ss.file_name,
                            ss.word_count,
                            ss.line_count,
                            ss.file_size,
                            ss.submitted_at,
                            ROUND(ss.file_size / 1024, 2) as file_size_kb,
                            DATE_FORMAT(ss.submitted_at, '%d.%m.%Y %H:%i') as submitted_formatted
                       FROM sermon_submissions ss
                       WHERE ss.user_id = ?
                       ORDER BY ss.sermon_date DESC";
    $submissionsStmt = $conn->prepare($submissionsSql);
    $submissionsStmt->bind_param("i", $user_id);
    $submissionsStmt->execute();
    $submissions_result = $submissionsStmt->get_result();
}

// Calculate overall statistics
$overallStatsSql = "SELECT 
                        COUNT(DISTINCT user_id) as total_users_submitted,
                        COUNT(*) as total_submissions,
                        AVG(word_count) as avg_word_count,
                        MAX(word_count) as max_word_count,
                        MIN(word_count) as min_word_count,
                        AVG(line_count) as avg_line_count,
                        SUM(file_size) as total_file_size
                    FROM sermon_submissions";
$overallStats = $conn->query($overallStatsSql)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sermon Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(180deg, #f7f1e8 0%, #eef3f7 100%);
        }
        
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(16,24,32,0.08);
            border-left: 4px solid #c8102e;
        }
        
        .stats-card h5 {
            color: #183153;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .stats-card .value {
            font-size: 1.8rem;
            font-weight: bold;
            color: #183153;
        }
        
        .nav-back {
            margin-bottom: 20px;
        }
        
        .admin-header {
            background: linear-gradient(135deg, rgba(16,24,32,0.96), rgba(24,49,83,0.96));
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(16,24,32,0.12);
        }
        
        .submissions-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(16,24,32,0.08);
        }
        
        .table th {
            background: linear-gradient(135deg, #101820 0%, #183153 50%, #c8102e 100%);
            color: white;
            border: none;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .user-select-form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .form-select {
            max-width: 300px;
        }
        
        .btn-download {
            padding: 5px 10px;
            font-size: 0.9rem;
        }
        
        .text-muted-small {
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark camp-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="/dashboard/dashboard.php">Bible Tracker - Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="/admin/admin.php"><i class="bi bi-gear"></i> Panou General</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard/dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Deconectează-te</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4" style="padding: 0 20px;">
    <div class="admin-header">
        <h2 class="mb-4">📊 Statistici Submiteri Sermon</h2>
        
        <!-- Overall Statistics -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <h5>👥 Utilizatori cu Submiteri</h5>
                    <div class="value"><?= $overallStats['total_users_submitted'] ?? 0 ?></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <h5>📝 Total Submiteri</h5>
                    <div class="value"><?= $overallStats['total_submissions'] ?? 0 ?></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <h5>📄 Medie Cuvinte</h5>
                    <div class="value"><?= round($overallStats['avg_word_count'] ?? 0) ?></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <h5>💾 Total Spațiu</h5>
                    <div class="value"><?= round(($overallStats['total_file_size'] ?? 0) / (1024 * 1024), 2) ?> MB</div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Selection Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">👥 Selectează Utilizatorul</h5>
        </div>
        <div class="card-body">
            <form method="get" class="user-select-form">
                <select name="user_id" id="user_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Selectează un utilizator...</option>
                    <?php 
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()): 
                    ?>
                        <option value="<?= $row['id'] ?>" <?= isset($user_id) && $user_id == $row['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['username']) ?> (<?= htmlspecialchars($row['email']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>
    </div>

    <!-- User Statistics and Submissions -->
    <?php if ($user_stats && $submissions_result): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">📊 Statistici pentru <?= htmlspecialchars($user_stats['username']) ?></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-2 col-sm-6">
                        <div class="stats-card mb-0">
                            <h5>📝 Submiteri</h5>
                            <div class="value"><?= $submissions_result->num_rows ?></div>
                        </div>
                    </div>
                    <?php
                    // Calculate user-specific statistics
                    $userStats = [
                        'total_words' => 0,
                        'total_lines' => 0,
                        'max_words' => 0,
                        'min_words' => PHP_INT_MAX,
                        'total_size' => 0
                    ];
                    $submissions_result->data_seek(0);
                    while ($row = $submissions_result->fetch_assoc()) {
                        $userStats['total_words'] += $row['word_count'];
                        $userStats['total_lines'] += $row['line_count'];
                        if ($row['word_count'] > $userStats['max_words']) {
                            $userStats['max_words'] = $row['word_count'];
                        }
                        if ($row['word_count'] < $userStats['min_words'] && $row['word_count'] > 0) {
                            $userStats['min_words'] = $row['word_count'];
                        }
                        $userStats['total_size'] += $row['file_size'];
                    }
                    $submissions_result->data_seek(0);
                    ?>
                    <div class="col-md-2 col-sm-6">
                        <div class="stats-card mb-0">
                            <h5>📝 Total Cuvinte</h5>
                            <div class="value"><?= $userStats['total_words'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="stats-card mb-0">
                            <h5>📄 Medie Cuvinte</h5>
                            <div class="value"><?= $submissions_result->num_rows > 0 ? round($userStats['total_words'] / $submissions_result->num_rows) : 0 ?></div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="stats-card mb-0">
                            <h5>⬆️ Max Cuvinte</h5>
                            <div class="value"><?= $userStats['max_words'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="stats-card mb-0">
                            <h5>⬇️ Min Cuvinte</h5>
                            <div class="value"><?= $userStats['min_words'] === PHP_INT_MAX ? 0 : $userStats['min_words'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="stats-card mb-0">
                            <h5>💾 Total Spațiu</h5>
                            <div class="value"><?= round($userStats['total_size'] / 1024, 2) ?> KB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">📄 Submiteri Detaliate</h5>
            </div>
            <div class="table-responsive submissions-table">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Data Sermon</th>
                            <th>Fișier</th>
                            <th>Cuvinte</th>
                            <th>Linii</th>
                            <th>Mărime</th>
                            <th>Data Transmitere</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $submissions_result->data_seek(0); while ($row = $submissions_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?= date('d.m.Y', strtotime($row['sermon_date'])) ?></strong>
                                    <br>
                                    <small class="text-muted-small"><?= date('l', strtotime($row['sermon_date'])) ?></small>
                                </td>
                                <td><?= htmlspecialchars(substr($row['file_name'], 0, 30)) ?><?= strlen($row['file_name']) > 30 ? '...' : '' ?></td>
                                <td><?= $row['word_count'] ?></td>
                                <td><?= $row['line_count'] ?></td>
                                <td><?= $row['file_size_kb'] ?> KB</td>
                                <td><?= $row['submitted_formatted'] ?></td>
                                <td>
                                    <a href="view_sermon.php?id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-success" 
                                       target="_blank">
                                        <i class="bi bi-eye"></i> Vezi
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif (isset($_GET['user_id'])): ?>
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle"></i> Utilizatorul selectat nu are nici o submitere.
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
