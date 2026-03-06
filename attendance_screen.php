<?php
date_default_timezone_set("Europe/Bucharest");

$secret = "bibletracker_secret_key";
$week = date("W");
$year = date("Y");

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

body{
background:#111;
color:white;
text-align:center;
font-family:Arial;
}

.qr img{
width:400px;
}

.big{
font-size:48px;
font-weight:bold;
margin-top:20px;
}

.count{
font-size:32px;
margin-top:20px;
}

.timebox{
margin-top:20px;
font-size:26px;
}

.feed-container{
    width:300px;
    max-height:500px;
    overflow:hidden;
    position:relative;
}

.feed-box{
    height:400px;
    overflow:hidden;
    /* allow opacity transitions for fade-out/in */
    transition: opacity 0.8s ease;
}

.feed-list{
    list-style:none;
    font-size:28px;
    margin:0;
    padding:0;
    animation: scrollUp 20s linear infinite;
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

<h1 class="mt-4">Check-in Tineret</h1>

<div class="timebox">
📅 <?php echo $current_date; ?>  
⏰ <?php echo $current_time; ?>
</div>

<div class="d-flex justify-content-center align-items-start mt-4" style="gap:2rem;flex-wrap:wrap;">
    <div class="qr">
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

    <div class="feed-container">
        <h2>Ultimii sosiți</h2>
        <div class="feed-box">
            <ul id="feed" class="feed-list"></ul>
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
            let time=item.created_at.split(" ")[1].substring(0,5);
            html += `<li>${item.username} - ${time}</li>`;
        });

        if(html === oldHtml) {
            // no change, do nothing
            return;
        }

        // content changed - perform fade
        box.style.opacity = 0;
        setTimeout(() => {
            feed.innerHTML = html;
            // restart scroll animation smoothly
            feed.style.animation = 'none';
            requestAnimationFrame(()=>{
                feed.style.animation = 'scrollUp 20s linear infinite';
            });
            // fade back in
            box.style.opacity = 1;
        }, 800); // match transition duration
    });
}

function updateClock(){

let now = new Date();

let time = now.toLocaleTimeString("ro-RO");

let date = now.toLocaleDateString("ro-RO");

document.querySelector(".timebox").innerHTML = "📅 " + date + " ⏰ " + time;

}

generateQR();
updateCount();
updateFeed();
updateClock();

setInterval(generateQR,30000);
setInterval(updateCount,10000);
setInterval(updateFeed,5000);
setInterval(updateClock,1000);

</script>

</body>
</html>