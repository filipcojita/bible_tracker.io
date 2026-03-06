<?php
session_start();
include 'db.php';

date_default_timezone_set("Europe/Bucharest");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['token'])) {
    die("Cod QR invalid.");
}

$secret = "bibletracker_secret_key";
$week = date("W");
$year = date("Y");

$time_block = floor(time() / 30);

$valid = false;

for ($i = -1; $i <= 1; $i++) {

    $expected = hash("sha256", $secret.$week.$year.($time_block+$i));

    if ($_GET['token'] === $expected) {
        $valid = true;
        break;
    }
}

if (!$valid) {
    die("Cod QR expirat. Scanează din nou.");
}

$current_time = date("H:i");

$start = "00:00";
$end = "23:59";

if ($current_time < $start) {
    die("Ai ajuns prea devreme. Așteaptă până la 18:45.");
}

if ($current_time > $end) {
    die("Ai ajuns prea târziu. Încearcă vinerea viitoare.");
}

$current_date = date("Y-m-d");

$stmt = $conn->prepare("SELECT id FROM attendance WHERE user_id=? AND date=?");
$stmt->bind_param("is", $user_id, $current_date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Prezența este deja înregistrată.");
}

$stmt = $conn->prepare("INSERT INTO attendance (user_id, date) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $current_date);
$stmt->execute();

header("Location: attendance_success.php");
exit();
?>