<?php
require_once "auth_check.php";
require_once "db_connect_mysqli.php";
require_once "audit_logger.php";
require_once "csrf.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Verify CSRF token
    if (!verify_csrf_token()) {
        header("Location: ../pages/delete_account.php?error=csrf");
        exit();
    }

    $password = $_POST["confirm_password"] ?? "";
    $user_id  = (int)($_SESSION["user_id"] ?? 0);

    if (empty($password) || $user_id <= 0) {
        header("Location: ../pages/delete_account.php?error=empty");
        exit();
    }

    // Verify password against DB
    $stmt = $conn->prepare("SELECT email, password FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user["password"])) {
                $user_email = $user["email"];
                $stmt->close();

                // Delete the user record
                $del_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                if ($del_stmt) {
                    $del_stmt->bind_param("i", $user_id);
                    $del_stmt->execute();
                    $del_stmt->close();

                    // Log audit event
                    log_audit($conn, "Account Deletion", "User deleted own account: " . $user_email);

                    // Clear remember-me cookie
                    if (isset($_COOKIE["remember_token"])) {
                        setcookie("remember_token", "", time() - 3600, "/");
                    }

                    // Destroy user session
                    $_SESSION = array();
                    if (ini_get("session.use_cookies")) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), "", time() - 42000,
                            $params["path"], $params["domain"],
                            $params["secure"], $params["httponly"]
                        );
                    }
                    session_destroy();

                    $conn->close();
                    header("Location: ../pages/login.php?msg=account_deleted");
                    exit();
                } else {
                    die("Failed to prepare delete query: " . $conn->error);
                }
            } else {
                $stmt->close();
                $conn->close();
                header("Location: ../pages/delete_account.php?error=wrong_password");
                exit();
            }
        } else {
            $stmt->close();
            $conn->close();
            header("Location: ../pages/delete_account.php?error=wrong_password");
            exit();
        }
    } else {
        die("Query failed: " . $conn->error);
    }

} else {
    header("Location: ../pages/dashboard.php");
    exit();
}
