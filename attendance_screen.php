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

</style>

</head>

<body>

<h1 class="mt-4">Check-in Tineret</h1>

<div class="timebox">
📅 <?php echo $current_date; ?>  
⏰ <?php echo $current_time; ?>
</div>

<div class="qr">
<img id="qr">
</div>

<div class="big">

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

<div class="count">
Prezenți: <span id="count">0</span>
</div>

<div class="mt-5">
<h2>Ultimii sosiți</h2>
<ul id="feed" style="list-style:none;font-size:28px;"></ul>
</div>

<script>

function generateQR(){

fetch("generate_token.php")
.then(r=>r.text())
.then(token=>{

let url = "http://localhost/bible_tracker/attendance.php?token="+token;

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

fetch("attendance_feed.php")
.then(r=>r.json())
.then(data=>{

let html="";

data.forEach(item=>{

let time=item.created_at.split(" ")[1].substring(0,5);

html += `<li>${item.username} - ${time}</li>`;

});

document.getElementById("feed").innerHTML=html;

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