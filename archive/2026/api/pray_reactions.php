<?php
/**
 * Prayer Wall API - Add/Update Emoticon Reaction
 * POST /api/pray_reactions.php
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
$emoticon = isset($data['emoticon']) ? trim($data['emoticon']) : '';

// Validate
if (!$prayer_id || !$emoticon) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Prayer ID and emoticon are required']);
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

// Insert or update reaction (UNIQUE constraint prevents duplicates, so we use INSERT ... ON DUPLICATE KEY UPDATE)
$stmt = $conn->prepare(
    "INSERT INTO prayer_reactions (prayer_id, user_id, emoticon) 
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE emoticon = VALUES(emoticon), created_at = CURRENT_TIMESTAMP"
);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$stmt->bind_param('iis', $prayer_id, $user_id, $emoticon);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Emoticon added successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to add emoticon']);
}

$stmt->close();
?>
