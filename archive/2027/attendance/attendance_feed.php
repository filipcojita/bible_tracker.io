<?php

include 'db.php';

// ✅ Set PHP timezone
date_default_timezone_set("Europe/Bucharest");

// ✅ Sync MySQL timezone
$conn->query("SET time_zone = '+02:00'");

$date = date("Y-m-d");

$query = "
SELECT users.username, attendance.created_at
FROM attendance
JOIN users ON attendance.user_id = users.id
WHERE attendance.date = '$date'
ORDER BY attendance.created_at DESC
LIMIT 10
";

$result = $conn->query($query);

$data = [];

if ($result) {
    while($row = $result->fetch_assoc()){

        // ✅ Convert to local time
        $timestamp = strtotime($row['created_at']);

        $data[] = [
            "username" => htmlspecialchars($row['username']),
            "time" => date("H:i", $timestamp)
        ];
    }
}

// Optional: oldest first for nicer scroll
$data = array_reverse($data);

header('Content-Type: application/json');
echo json_encode($data);