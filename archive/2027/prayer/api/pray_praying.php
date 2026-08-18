<?php
/**
 * Prayer Wall API - Toggle "Praying for you" Status
 * POST /api/pray_praying.php
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
$action = isset($data['action']) ? trim($data['action']) : '';

// Validate
if (!$prayer_id || !in_array($action, ['add', 'remove'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Prayer ID and valid action (add/remove) are required']);
    exit();
}

// Verify prayer exists
$check_stmt = $conn->prepare("SELECT id FROM prayer_requests WHERE id = ?");
$check_stmt->bind_param('i', $prayer_id);
$check_stmt->execute();
if (!$check_stmt->get_result()->fetch_assoc()) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Prayer not found']);
    $check_stmt->close();
    exit();
}
$check_stmt->close();

if ($action === 'add') {
    // Insert "praying" record (unique constraint prevents duplicates)
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO prayer_praying (prayer_id, user_id) VALUES (?, ?)"
    );
    $stmt->bind_param('ii', $prayer_id, $user_id);
    $stmt->execute();
} else {
    // Remove "praying" record
    $stmt = $conn->prepare(
        "DELETE FROM prayer_praying WHERE prayer_id = ? AND user_id = ?"
    );
    $stmt->bind_param('ii', $prayer_id, $user_id);
    $stmt->execute();
}

// Get updated count
$count_stmt = $conn->prepare(
    "SELECT COUNT(*) as count FROM prayer_praying WHERE prayer_id = ?"
);
$count_stmt->bind_param('i', $prayer_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$praying_count = (int)$count_row['count'];
$count_stmt->close();

echo json_encode([
    'success' => true,
    'message' => $action === 'add' ? 'Added to prayer' : 'Removed from prayer',
    'praying_count' => $praying_count
]);

$stmt->close();
?>
