<?php
/**
 * Prayer Wall API - List Prayer Requests
 * GET /api/pray_list.php?category=lauda (optional)
 */

include '../db.php';
include '../auth.php';

session_start();
checkPersistentLogin();

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Build query
$valid_categories = ['lauda', 'multumire', 'cerere', 'mijlocire', 'marturisire'];

if ($category && in_array($category, $valid_categories)) {
    // Single category
    $query = "
        SELECT 
            pr.id, pr.user_id, pr.title, pr.description, pr.category, 
            pr.is_anonymous, pr.created_at,
            CASE WHEN pr.is_anonymous THEN 'Anonymous' ELSE u.username END as creator_name,
            COALESCE(COUNT(DISTINCT pp.id), 0) as praying_count,
            MAX(CASE WHEN pp.user_id = ? THEN 1 ELSE 0 END) as user_has_prayed
        FROM prayer_requests pr
        LEFT JOIN users u ON pr.user_id = u.id
        LEFT JOIN prayer_praying pp ON pr.id = pp.prayer_id
        WHERE pr.category = ?
        GROUP BY pr.id
        ORDER BY pr.created_at DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('is', $user_id, $category);
} else {
    // All categories
    $query = "
        SELECT 
            pr.id, pr.user_id, pr.title, pr.description, pr.category, 
            pr.is_anonymous, pr.created_at,
            CASE WHEN pr.is_anonymous THEN 'Anonymous' ELSE u.username END as creator_name,
            COALESCE(COUNT(DISTINCT pp.id), 0) as praying_count,
            MAX(CASE WHEN pp.user_id = ? THEN 1 ELSE 0 END) as user_has_prayed
        FROM prayer_requests pr
        LEFT JOIN users u ON pr.user_id = u.id
        LEFT JOIN prayer_praying pp ON pr.id = pp.prayer_id
        GROUP BY pr.id
        ORDER BY pr.created_at DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
}

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

$prayers = [];
$prayers_by_category = [];

while ($row = $result->fetch_assoc()) {
    // Get emoticon reactions
    $emoticon_stmt = $conn->prepare(
        "SELECT emoticon, COUNT(*) as count FROM prayer_reactions 
         WHERE prayer_id = ? GROUP BY emoticon"
    );
    $emoticon_stmt->bind_param('i', $row['id']);
    $emoticon_stmt->execute();
    $emoticon_result = $emoticon_stmt->get_result();
    
    $emoticons = [];
    while ($emoticon_row = $emoticon_result->fetch_assoc()) {
        $emoticons[] = [
            'emoji' => $emoticon_row['emoticon'],
            'count' => (int)$emoticon_row['count']
        ];
    }
    $emoticon_stmt->close();
    
    $row['emoticons'] = $emoticons;
    $row['praying_count'] = (int)$row['praying_count'];
    $row['user_has_prayed'] = (bool)$row['user_has_prayed'];
    
    $prayers[] = $row;
}

$stmt->close();

echo json_encode([
    'success' => true,
    'prayers' => $prayers
]);
?>
