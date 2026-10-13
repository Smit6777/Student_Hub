<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Clear Remember-Me token in database
if (isset($_SESSION["user_id"])) {
    require_once "db_connect_mysqli.php";

    $user_id = $_SESSION["user_id"];
    $stmt    = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();
}

// 2. Clear Remember-Me cookie
if (isset($_COOKIE["remember_token"])) {
    setcookie("remember_token", "", time() - 3600, "/");
}

// 3. Unset all session variables
$_SESSION = array();

// 4. Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), "", time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 5. Destroy session completely
session_destroy();

// Redirect to login page with logout feedback
header("Location: ../pages/login.php?msg=logged_out");
exit();
?>
