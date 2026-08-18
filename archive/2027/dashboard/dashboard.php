<?php
session_start();
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/auth.php';

checkPersistentLogin();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username']; // Get username for greeting
$today = date("Y-m-d");

// Get the last 3 days (today, yesterday, and the day before yesterday)
$last_three_days = [];
for ($i = 0; $i < 3; $i++) {
    $last_three_days[] = date("Y-m-d", strtotime("$today -$i day"));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $date = $_POST['date'];
    $passage = $_POST['passage'];
    $reflection = $_POST['reflection'];

    // Check if the selected date is within the last 3 days
    if (!in_array($date, $last_three_days)) {
        echo "<script>alert('❌ You can only submit responses for the last 3 days.');</script>";
    } else {
        // Check if the user already submitted for this day
        $sql = "SELECT COUNT(*) AS count FROM submissions WHERE user_id = ? AND date = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            echo "<script>alert('❌ You have already submitted for this day.');</script>";
        } else {
            // Insert the form data into the database
            $sql = "INSERT INTO submissions (user_id, date, passage, reflection) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $user_id, $date, $passage, $reflection);

            if ($stmt->execute()) {
                echo "<script>alert('Submission successful!');</script>";
            } else {
                echo "<script>alert('Error submitting your entry. Please try again.');</script>";
            }
        }
    }
}

// Fetch submitted dates for this user
$sql = "SELECT date FROM submissions WHERE user_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$submitted_dates = [];

while ($row = $result->fetch_assoc()) {
    $submitted_dates[] = $row['date'];
}

$show_all_button = count($submitted_dates) > 5;
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bible Tracker - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="sermon_styles.css">
</head>
<body>

<?php $activePage = 'dashboard'; include 'navbar.php'; ?>

<div class="container mt-4">
    <div class="row gx-4 gy-4">
        <div class="col-12 col-lg-6">
            <div class="camp-banner mb-4">
                <span class="camp-badge">Tabără 2027 • Germania</span>
                <strong>13–22 August</strong>
            </div>
            <h2>Înregistrează-ți meditația biblică de astăzi</h2>

            <!-- Submission Form -->
            <form method="post" class="form-group">
                <div class="mb-3">
                    <label for="date" class="form-label">Selectează Data:</label>
                    <input type="date" name="date" id="date-picker" max="<?= $today ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="passage" class="form-label">Pasaj Biblic:</label>
                    <input type="text" name="passage" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="reflection" class="form-label">Reflecție: (Cu ce ai rămas?)</label>
                    <textarea name="reflection" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Trimite</button>
            </form>

            <!-- User Past Submissions -->
            <h3 class="mt-4">Înregistrări anterioare:</h3>
            <div class="submitted-dates">
                <ul>
                    <?php $count = 0; foreach ($submitted_dates as $date): ?>
                        <li class="submission-item<?php if ($count >= 5) echo ' hidden'; ?>"<?php if ($count >= 5) echo ' style="display:none;"'; ?>><strong><?= htmlspecialchars(date('d-M-Y', strtotime($date))) ?></strong></li>
                    <?php $count++; endforeach; ?>
                </ul>
            </div>

            <?php if ($show_all_button): ?>
                <button id="toggle-submissions-btn" class="btn btn-secondary me-2"><i class="bi bi-eye"></i> Arată toate</button>
            <?php endif; ?>

            <div class="mt-3">
                <a href="qr_scanner.php" class="btn btn-success me-2 mb-2"><i class="bi bi-camera"></i> Deschide Cameră</a>
                <a href="../leaderboard/leaderboard.php" class="btn btn-info me-2 mb-2"><i class="bi bi-trophy"></i> Vezi Clasamentul</a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="../admin/admin.php" class="btn btn-warning mb-2"><i class="bi bi-gear"></i> Panou Admin</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <?php include 'sermon_calendar.php'; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="sermon_modal.js"></script>

<script>
    let submittedDates = <?= json_encode($submitted_dates) ?>;
    let datePicker = document.getElementById("date-picker");

    datePicker.addEventListener("change", function() {
        if (submittedDates.includes(this.value)) {
            alert("❌ Ai trimis deja astăzi.");
            this.value = ""; // Clear the selected date
        }
    });

    // Toggle show/hide submissions button
    let toggleBtn = document.getElementById('toggle-submissions-btn');
    if (toggleBtn) {
        let hiddenItems = document.querySelectorAll('.submission-item.hidden');
        let isShowingAll = false;

        toggleBtn.addEventListener('click', function() {
            if (isShowingAll) {
                hiddenItems.forEach(item => {
                    item.style.display = 'none';
                    item.classList.add('hidden');
                });
                this.innerHTML = '<i class="bi bi-eye"></i> Arată toate';
                isShowingAll = false;
            } else {
                hiddenItems.forEach(item => {
                    item.style.display = '';
                    item.classList.remove('hidden');
                });
                this.innerHTML = '<i class="bi bi-eye-slash"></i> Ascunde';
                isShowingAll = true;
            }
        });
    }
</script>

</body>
</html>
