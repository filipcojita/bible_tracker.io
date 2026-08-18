<?php
/**
 * Prayer Wall API - Delete Prayer Request
 * POST /api/pray_delete.php
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
$data = json_decode(file_get_contents('php://input'), true);
$prayer_id = isset($data['prayer_id']) ? (int)$data['prayer_id'] : 0;

if (!$prayer_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Prayer ID is required']);
    exit();
}

// Get prayer details
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

// Check permission (owner or admin)
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
if ($prayer['user_id'] !== $user_id && !$is_admin) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit();
}

// Delete prayer (cascade deletes reactions and praying records)
$delete_stmt = $conn->prepare("DELETE FROM prayer_requests WHERE id = ?");
$delete_stmt->bind_param('i', $prayer_id);

if ($delete_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Prayer deleted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete prayer']);
}

$delete_stmt->close();
?>
