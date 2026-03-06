<?php

include 'db.php';

date_default_timezone_set("Europe/Bucharest");

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

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);