<?php

date_default_timezone_set("Europe/Bucharest");

$secret = "bibletracker_secret_key";
$week = date("W");
$year = date("Y");

$time_block = floor(time()/30);

$token = hash("sha256",$secret.$week.$year.$time_block);

echo $token;