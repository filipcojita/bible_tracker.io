<?php
/**
 * Prayer Wall API - Create Prayer Request
 * POST /api/pray_create.php
 */

include '../db.php';
include '../auth.php';

session_start();
checkPersistentLogin();

// Ensure APIs always return valid JSON even if PHP emits warnings/notices.
// Buffer output so stray echoes/warnings don't break JSON parsing on the client.
ob_start();
ini_set('display_errors', '0');
error_reporting(E_ALL);

function send_json($data, $http_code = 200) {
    if (ob_get_length()) {
        // Clear any buffered output that could break JSON
        ob_clean();
    }
    http_response_code($http_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit();
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    send_json(['success' => false, 'message' => 'Not authenticated'], 401);
}

$user_id = $_SESSION['user_id'];

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
$title = isset($data['title']) ? trim($data['title']) : '';
$description = isset($data['description']) ? trim($data['description']) : '';
$category = isset($data['category']) ? trim($data['category']) : '';
$is_anonymous = isset($data['is_anonymous']) ? (bool)$data['is_anonymous'] : false;

// Validation rules
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

// Return errors if any
if (!empty($errors)) {
    send_json(['success' => false, 'message' => implode(', ', $errors)], 400);
}

// Insert into database
$stmt = $conn->prepare(
    "INSERT INTO prayer_requests (user_id, title, description, category, is_anonymous) 
     VALUES (?, ?, ?, ?, ?)"
);

if (!$stmt) {
    send_json(['success' => false, 'message' => 'Database error: ' . $conn->error], 500);
}

$stmt->bind_param('isssi', $user_id, $title, $description, $category, $is_anonymous);

if ($stmt->execute()) {
    $prayer_id = $stmt->insert_id;
    send_json([
        'success' => true,
        'message' => 'Prayer request created successfully',
        'prayer_id' => $prayer_id
    ], 201);
} else {
    send_json(['success' => false, 'message' => 'Failed to create prayer request'], 500);
}

$stmt->close();
?>
