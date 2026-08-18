<?php
session_start();
include 'db.php';
include 'auth.php';

checkPersistentLogin();

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit('Unauthorized');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Invalid request');
}

$submissionId = (int) $_GET['id'];
$userId = $_SESSION['user_id'];

$sql = "SELECT user_id, file_path, file_name FROM sermon_submissions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $submissionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('HTTP/1.1 404 Not Found');
    exit('Submission not found');
}

$submission = $result->fetch_assoc();

if ($submission['user_id'] !== $userId && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

$filePath = $submission['file_path'];
$fileName = $submission['file_name'];

if (!file_exists($filePath) || !is_readable($filePath)) {
    header('HTTP/1.1 404 Not Found');
    exit('File not found');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
