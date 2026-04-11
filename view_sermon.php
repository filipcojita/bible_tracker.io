<?php
session_start();
include 'db.php';
include 'auth.php';

checkPersistentLogin();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID");
}

$id = (int) $_GET['id'];

$sql = "SELECT * FROM sermon_submissions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Not found");
}

$row = $result->fetch_assoc();

// path to file (adjust if needed)
$filePath = "uploads/sermons/" . $row['file_name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Preview Sermon</title>
</head>
<body>

<h2><?= htmlspecialchars($row['file_name']) ?></h2>

<?php if (file_exists($filePath)): ?>

    <!-- If it's a text file -->
    <?php if (pathinfo($filePath, PATHINFO_EXTENSION) === 'txt'): ?>
        <pre><?php echo htmlspecialchars(file_get_contents($filePath)); ?></pre>

    <!-- If it's PDF -->
    <?php elseif (pathinfo($filePath, PATHINFO_EXTENSION) === 'pdf'): ?>
        <iframe src="<?= $filePath ?>" width="100%" height="800px"></iframe>

    <?php else: ?>
        <p>Preview not supported. <a href="<?= $filePath ?>">Download</a></p>
    <?php endif; ?>

<?php else: ?>
    <p>File not found.</p>
<?php endif; ?>

</body>
</html>
