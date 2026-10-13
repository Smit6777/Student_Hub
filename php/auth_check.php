<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Check Remember-Me cookie if session is not active
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

// 2. Block unauthorized access
if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.php?error=unauthorized");
    exit();
}

// 3. Enforce session timeout (15 minutes of inactivity)
$timeout_seconds = 900;
if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > $timeout_seconds)) {
    // Clear session data
    $_SESSION = array();

    // Expire session cookie
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

// Update last activity timestamp on current request
$_SESSION["last_activity"] = time();

// 4. Role-based access control helper function
function require_role($required_role) {
    if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== $required_role) {
        header("Location: ../pages/dashboard.php?error=forbidden");
        exit();
    }
}
?>
