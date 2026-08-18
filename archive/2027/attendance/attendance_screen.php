<?php
date_default_timezone_set("Europe/Bucharest");

$start = "18:45";
$end = "19:15";

$current = date("H:i");
$current_date = date("d.m.Y");
$current_time = date("H:i:s");

$status = "closed";

if ($current < $start) {
    $status = "early";
}
elseif ($current >= $start && $current <= $end) {
    $status = "open";
}
else {
    $status = "late";
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>

<meta charset="UTF-8">
<title>Check-in Tineret</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    margin: 0;
    min-height: 100vh;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #101820 0%, #183153 45%, #c8102e 100%);
    color: #fff;
    text-align: center;
}

.page-shell {
    width: min(1200px, 92vw);
    margin: 0 auto;
    padding: 30px 0 50px;
}

.top-badge {
    display: inline-block;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.22);
    padding: 10px 18px;
    border-radius: 999px;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    margin-bottom: 18px;
    color: #f3f6fb;
}

h1 {
    font-size: clamp(2rem, 4vw, 3.1rem);
    font-weight: 800;
    margin: 0;
}

.timebox {
    margin-top: 18px;
    font-size: clamp(1rem, 2vw, 1.6rem);
    color: #e4ebf6;
}

.panel-wrap {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 2.5rem;
    flex-wrap: wrap;
    margin-top: 30px;
}

.panel {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255,255,255,0.14);
    border-radius: 24px;
    backdrop-filter: blur(4px);
    box-shadow: 0 18px 40px rgba(0,0,0,0.15);
}

.qr {
    padding: 26px 22px 20px;
    min-width: 420px;
}

.qr img {
    width: min(400px, 78vw);
    border-radius: 18px;
    background: #fff;
    padding: 10px;
    box-shadow: 0 12px 28px rgba(0,0,0,0.18);
}

.big {
    font-size: clamp(1.6rem, 3vw, 2.6rem);
    font-weight: 800;
    margin-top: 22px;
    line-height: 1.2;
    color: #fff;
}

.count {
    font-size: clamp(1.2rem, 2vw, 2rem);
    margin-top: 18px;
    font-weight: 700;
    color: #eaf1fb;
}

.feed-container {
    width: min(320px, 82vw);
    padding: 22px 18px 18px;
}

.feed-container h2 {
    margin: 0 0 14px;
    font-size: 1.4rem;
    color: #f7f9fc;
}

.feed-box {
    height: 380px;
    overflow: hidden;
    background: rgba(10, 16, 26, 0.22);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 12px 16px;
    transition: opacity 0.8s ease;
}

.feed-list {
    list-style: none;
    font-size: clamp(1.1rem, 2vw, 1.7rem);
    margin: 0;
    padding: 0;
    animation: scrollUp 20s linear infinite;
    color: #f8fbff;
}

.feed-list li {
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

@keyframes scrollUp {
    0% { transform: translateY(100%); opacity:0; }
    10% { opacity:1; }
    90% { opacity:1; }
    100% { transform: translateY(-100%); opacity:0; }
}
</style>

</head>

<body>

<div class="page-shell">
    <div class="top-badge">ACASĂ • Tabăra 2027 • Germania</div>
    <h1>Check-in Tineret</h1>

    <div class="timebox">
        📅 <?php echo $current_date; ?>
        &nbsp;|&nbsp;
        ⏰ <?php echo $current_time; ?>
    </div>

    <div class="panel-wrap">
        <div class="panel qr">
            <img id="qr">

            <div class="big mt-3">
            <?php
            if($status=="early"){
                echo "Check-in începe la 18:45";
            }
            if($status=="open"){
                echo "Scanează pentru prezență";
            }
            if($status=="late"){
                echo "Check-in închis";
            }
            ?>
            </div>

            <div class="count mt-3">
                Prezenți: <span id="count">0</span>
            </div>
        </div>

        <div class="panel feed-container">
            <h2>Prezenți la timp:</h2>
            <div class="feed-box">
                <ul id="feed" class="feed-list"></ul>
            </div>
        </div>
    </div>
</div>

<script>

function generateQR(){
    fetch("generate_token.php")
    .then(r=>r.text())
    .then(token=>{
        let url = "https://tineretsperanta.net/attendance.php?token="+token;
        let qr = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data="+encodeURIComponent(url);
        document.getElementById("qr").src = qr;
    });
}

function updateCount(){
    fetch("attendance_count.php")
    .then(r=>r.text())
    .then(data=>{
        document.getElementById("count").innerText=data;
    });
}

function updateFeed(){
    const box = document.querySelector('.feed-box');
    const feed = document.getElementById("feed");
    const oldHtml = feed.innerHTML;

    fetch("attendance_feed.php")
    .then(r=>r.json())
    .then(data=>{
        let html="";
        data.forEach(item=>{
            html += `<li>${item.username} - ${item.time}</li>`;
        });

        if(html === oldHtml) return;

        box.style.opacity = 0;

        setTimeout(() => {
            feed.innerHTML = html;

            feed.style.animation = 'none';
            requestAnimationFrame(()=>{
                feed.style.animation = 'scrollUp 20s linear infinite';
            });

            box.style.opacity = 1;
        }, 800);
    });
}

function updateClock(){
    let now = new Date();
    let time = now.toLocaleTimeString("ro-RO");
    let date = now.toLocaleDateString("ro-RO");
    document.querySelector(".timebox").innerHTML = "📅 " + date + " ⏰ " + time;
}

// Init
generateQR();
updateCount();
updateFeed();
updateClock();

// Intervals
setInterval(generateQR,30000);
setInterval(updateCount,10000);
setInterval(updateFeed,5000);
setInterval(updateClock,1000);

</script>

</body>
</html>