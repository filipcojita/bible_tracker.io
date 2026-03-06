<?php

include 'db.php';

date_default_timezone_set("Europe/Bucharest");

$date = date("Y-m-d");

$result = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE date='$date'");
$row = $result->fetch_assoc();

echo $row['total'];