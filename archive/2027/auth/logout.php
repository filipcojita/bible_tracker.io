<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    // Clear remember token from database
    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

session_unset();  // Clear all session variables
session_destroy();  // Destroy the session

// Clear the remember cookie
setcookie('remember_token', '', time() - 3600, "/");

header('Location: /auth/login.php'); // Redirect to the login page
exit();
?>
