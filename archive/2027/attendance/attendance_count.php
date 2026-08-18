<?php

include 'db.php';

// ✅ Set PHP timezone
date_default_timezone_set("Europe/Bucharest");

// ✅ Sync MySQL timezone
$conn->query("SET time_zone = '+02:00'");

$date = date("Y-m-d");

$result = $conn->query("
SELECT COUNT(*) as total 
FROM attendance 
WHERE date = '$date'
");

$row = $result->fetch_assoc();

echo $row['total'];