<?php
include 'db.php';

function checkPersistentLogin() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];

        $stmt = $GLOBALS['conn']->prepare("SELECT id, username, role FROM users WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
        }
        $stmt->close();
    }
}
?>