<?php
session_start();
include 'db.php';
include 'auth.php';

checkPersistentLogin();

date_default_timezone_set("Europe/Bucharest");

// helper to render a status screen with an image
function showMessage($message, $imageFile) {
    echo "<!DOCTYPE html><html lang=\"ro\"><head>\n" .
         "<meta charset=\"UTF-8\">\n" .
         "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n" .
         "<title>Check-in</title>\n" .
         "<link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">\n" .
         "<style>\n" .
         "body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, #101820 0%, #183153 45%, #c8102e 100%); color:#fff; font-family:Arial,sans-serif; }\n" .
         ".status-card { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.14); border-radius: 24px; padding: 32px 28px; box-shadow: 0 18px 40px rgba(0,0,0,0.18); max-width: 540px; width: min(90vw, 540px); text-align:center; }\n" .
         ".status-badge { display:inline-block; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.18); border-radius: 999px; letter-spacing: 0.12em; text-transform: uppercase; padding: 8px 14px; font-size: 0.72rem; margin-bottom: 18px; }\n" .
         ".status-img { max-width: 220px; height:auto; margin-bottom: 18px; border-radius: 16px; }\n" .
         "h1 { font-size: clamp(1.8rem, 3vw, 2.7rem); font-weight:800; margin:0; }\n" .
         "p { margin: 12px 0 0; color: #e7edf8; font-size: 1.05rem; }\n" .
         "</style>\n" .
         "</head><body>\n" .
         "<div class='status-card'>" .
         "<div class='status-badge'>ACASĂ • Tabăra 2027</div>" .
         "<img src=\"/images/" . htmlspecialchars($imageFile) . "\" alt=\"status\" class=\"status-img\">" .
         "<h1>" . htmlspecialchars($message) . "</h1>" .
         "<p>Germania te așteaptă.</p>" .
         "</div></body></html>";
    exit;
}

// helper to get a random image from a folder
function getRandomImage($folder) {
    $dir = __DIR__ . "/images/" . $folder;
    if (!is_dir($dir)) {
        return "success.jpg"; // fallback
    }
    $files = array_diff(scandir($dir), array('.', '..'));
    $images = array_filter($files, function($file) {
        return preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
    });
    if (empty($images)) {
        return "success.jpg"; // fallback
    }
    return $folder . "/" . $images[array_rand($images)];
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['token'])) {
    showMessage("Cod QR invalid.", getRandomImage("too_late"));
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
    showMessage("Cod QR expirat. Scanează din nou.", getRandomImage("too_late"));
}

$current_time = date("H:i");

$start = "18:45";
$end = "19:15";

if ($current_time < $start) {
    showMessage("Ai ajuns prea devreme. Așteaptă până la 18:45.", getRandomImage("too_late"));
}

if ($current_time > $end) {
    showMessage("Ai ajuns prea târziu. Încearcă vinerea viitoare.", getRandomImage("too_late"));
}

$current_date = date("Y-m-d");

$stmt = $conn->prepare("SELECT id FROM attendance WHERE user_id=? AND date=?");
$stmt->bind_param("is", $user_id, $current_date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    showMessage("Prezența este deja înregistrată.", getRandomImage("too_late"));
}

$stmt = $conn->prepare("INSERT INTO attendance (user_id, date) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $current_date);
$stmt->execute();

showMessage("Prezența înregistrată cu succes!", getRandomImage("on_time"));
exit();
?>