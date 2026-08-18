<?php
$host = "mysql.hostinger.com";
$user = "u462094724_admin_account";  // Default WAMP MySQL user
$pass = "CromastronX1";  // No password for local MySQL
$dbname = "u462094724_bible_tracker";  // The name of the database you created

$conn = new mysqli($host, $user, $pass, $dbname);

// $host = "localhost";
// $user = "root";
// $password = "";
// $database = "bible_tracker";

// $conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
