<?php
require_once "db_connect_mysqli.php";
require_once "audit_logger.php";
require_once "csrf.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Rate Limiting / Brute Force Prevention
    $lockout_seconds = 300; // 5 minutes
    $max_attempts    = 5;

    if (isset($_SESSION["login_lockout"]) && time() < $_SESSION["login_lockout"]) {
        $remaining = ceil(($_SESSION["login_lockout"] - time()) / 60);
        header("Location: ../pages/login.php?error=throttled&wait=" . $remaining);
        exit();
    }

    // CSRF Protection
    if (!verify_csrf_token()) {
        header("Location: ../pages/login.php?error=csrf");
        exit();
    }

    $email       = trim($_POST["email"] ?? "");
    $password    = $_POST["password"] ?? "";
    $remember_me = isset($_POST["remember_me"]);

    if (empty($email) || empty($password)) {
        header("Location: ../pages/login.php?error=empty");
        exit();
    }

    $sql  = "SELECT id, name, email, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {

            if (password_verify($password, $user["password"])) {

                session_start();
                session_regenerate_id(true);

                $_SESSION["user_id"]       = $user["id"];
                $_SESSION["user_name"]     = $user["name"];
                $_SESSION["user_email"]    = $user["email"];
                $_SESSION["user_role"]     = $user["role"];
                $_SESSION["last_activity"] = time();

                if ($remember_me) {
                    $token      = bin2hex(random_bytes(32));
                    $token_hash = hash("sha256", $token);

                    $token_sql  = "UPDATE users SET remember_token = ? WHERE id = ?";
                    $token_stmt = $conn->prepare($token_sql);
                    if ($token_stmt) {
                        $token_stmt->bind_param("si", $token_hash, $user["id"]);
                        $token_stmt->execute();
                        $token_stmt->close();

                        setcookie("remember_token", $token, time() + (30 * 86400), "/", "", false, true);
                    }
                }

                unset($_SESSION["login_attempts"]);
                unset($_SESSION["login_lockout"]);

                log_audit($conn, "Login", "Signed in with role: " . $user["role"]);

                $stmt->close();
                $conn->close();

                if ($user["role"] === "admin") {
                    header("Location: ../pages/admin.php");
                } else {
                    header("Location: ../pages/dashboard.php");
                }
                exit();

            } else {
                // Invalid password
                $_SESSION["login_attempts"] = ($_SESSION["login_attempts"] ?? 0) + 1;
                if ($_SESSION["login_attempts"] >= $max_attempts) {
                    $_SESSION["login_lockout"] = time() + $lockout_seconds;
                    $_SESSION["login_attempts"] = 0;
                }
                header("Location: ../pages/login.php?error=invalid");
                exit();
            }

        } else {
            // User not found
            $_SESSION["login_attempts"] = ($_SESSION["login_attempts"] ?? 0) + 1;
            if ($_SESSION["login_attempts"] >= $max_attempts) {
                $_SESSION["login_lockout"] = time() + $lockout_seconds;
                $_SESSION["login_attempts"] = 0;
            }
            header("Location: ../pages/login.php?error=invalid");
            exit();
        }

        $stmt->close();
    } else {
        die("Query failed: " . $conn->error);
    }

    $conn->close();

} else {
    header("Location: ../pages/login.php");
    exit();
}
?>
