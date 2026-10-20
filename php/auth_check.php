<?php
require_once __DIR__ . "/security_headers.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"]) && isset($_COOKIE["remember_token"])) {
    require_once "db_connect_mysqli.php";

    $token_hash = hash("sha256", $_COOKIE["remember_token"]);
    $stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE remember_token = ?");

    if ($stmt) {
        $stmt->bind_param("s", $token_hash);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            session_regenerate_id(true);
            $_SESSION["user_id"]       = $user["id"];
            $_SESSION["user_name"]     = $user["name"];
            $_SESSION["user_email"]    = $user["email"];
            $_SESSION["user_role"]     = $user["role"];
            $_SESSION["last_activity"] = time();
        }
        $stmt->close();
    }
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.php?error=unauthorized");
    exit();
}

$timeout_seconds = 900;
if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > $timeout_seconds)) {
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), "", time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    header("Location: ../pages/login.php?error=timeout");
    exit();
}

$_SESSION["last_activity"] = time();

function require_role($required_role) {
    if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== $required_role) {
        header("Location: ../pages/dashboard.php?error=forbidden");
        exit();
    }
}
?>
