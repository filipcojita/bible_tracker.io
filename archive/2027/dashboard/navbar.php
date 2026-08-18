<?php
// Shared navbar for Bible Tracker pages.
$activePage = $activePage ?? '';
$username = $_SESSION['username'] ?? 'User';
?>
<nav class="navbar navbar-expand-lg navbar-dark camp-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php"><i class="bi bi-flag-fill me-2"></i> ACASĂ • Tabără 2027</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
                <li class="nav-item">
                    <a class="nav-link<?= $activePage === 'dashboard' ? ' active' : '' ?>" aria-current="page" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $activePage === 'pray' ? ' active' : '' ?>" href="../prayer/pray.php"><i class="bi bi-journal-text"></i> Prayer Wall</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $activePage === 'leaderboard' ? ' active' : '' ?>" href="../leaderboard/leaderboard.php"><i class="bi bi-trophy"></i> Clasament</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $activePage === 'scanner' ? ' active' : '' ?>" href="qr_scanner.php"><i class="bi bi-camera"></i> Scanner</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="navbar-text welcome-message d-none d-md-inline">
                    Bun venit, <?= htmlspecialchars($username) ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-1"></i> Deconectează-te</a>
            </div>
        </div>
    </div>
</nav>
