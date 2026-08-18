<?php
/**
 * Prayer Wall API - Edit Prayer Request
 * POST /api/pray_edit.php
 */

include '../db.php';
include '../auth.php';

session_start();
checkPersistentLogin();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$data = json_decode(file_get_contents('php://input'), true);

$prayer_id = isset($data['prayer_id']) ? (int)$data['prayer_id'] : 0;
$title = isset($data['title']) ? trim($data['title']) : '';
$description = isset($data['description']) ? trim($data['description']) : '';
$category = isset($data['category']) ? trim($data['category']) : '';
$is_anonymous = isset($data['is_anonymous']) ? (bool)$data['is_anonymous'] : false;

// Validate input
$errors = [];

if (empty($title)) {
    $errors[] = 'Title is required';
} elseif (strlen($title) > 200) {
    $errors[] = 'Title must be 200 characters or less';
}

if (strlen($description) > 1000) {
    $errors[] = 'Description must be 1000 characters or less';
}

$valid_categories = ['lauda', 'multumire', 'cerere', 'mijlocire', 'marturisire'];
if (!in_array($category, $valid_categories)) {
    $errors[] = 'Invalid category';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit();
}

// Get prayer and verify ownership
$stmt = $conn->prepare("SELECT user_id FROM prayer_requests WHERE id = ?");
$stmt->bind_param('i', $prayer_id);
$stmt->execute();
$result = $stmt->get_result();
$prayer = $result->fetch_assoc();
$stmt->close();

if (!$prayer) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Prayer not found']);
    exit();
}

if ($prayer['user_id'] !== $user_id && !$is_admin) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit();
}

// Update prayer
$update_stmt = $conn->prepare(
    "UPDATE prayer_requests SET title = ?, description = ?, category = ?, is_anonymous = ? WHERE id = ?"
);

if (!$update_stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$update_stmt->bind_param('sssii', $title, $description, $category, $is_anonymous, $prayer_id);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Prayer updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update prayer']);
}

$update_stmt->close();
?>
