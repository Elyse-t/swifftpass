<?php
session_start(); // ✅ MUST be at the top

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Optional: clear the session cookie (more thorough)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirect to login page
header("Location: index.php");
exit;
?>
