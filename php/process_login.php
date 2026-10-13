<?php
require_once "db_connect_mysqli.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email       = trim($_POST["email"] ?? "");
    $password    = $_POST["password"] ?? "";
    $remember_me = isset($_POST["remember_me"]);

    // 1. Basic validation
    if (empty($email) || empty($password)) {
        header("Location: ../pages/login.php?error=empty");
        exit();
    }

    // 2. Fetch user by email using Prepared Statement
    $sql  = "SELECT id, name, email, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {

            // 3. Verify hashed password
            if (password_verify($password, $user["password"])) {

                // 4. Start session and regenerate session ID to prevent fixation attacks
                session_start();
                session_regenerate_id(true);

                // 5. Store session variables
                $_SESSION["user_id"]       = $user["id"];
                $_SESSION["user_name"]     = $user["name"];
                $_SESSION["user_email"]    = $user["email"];
                $_SESSION["user_role"]     = $user["role"];
                $_SESSION["last_activity"] = time();

                // 6. Handle optional Remember-Me token
                if ($remember_me) {
                    $token      = bin2hex(random_bytes(32));
                    $token_hash = hash("sha256", $token);

                    $token_sql  = "UPDATE users SET remember_token = ? WHERE id = ?";
                    $token_stmt = $conn->prepare($token_sql);
                    if ($token_stmt) {
                        $token_stmt->bind_param("si", $token_hash, $user["id"]);
                        $token_stmt->execute();
                        $token_stmt->close();

                        // Set cookie for 30 days (HTTPOnly flag enabled)
                        setcookie("remember_token", $token, time() + (30 * 86400), "/", "", false, true);
                    }
                }

                $stmt->close();
                $conn->close();

                // 7. Role-based dashboard redirection
                if ($user["role"] === "admin") {
                    header("Location: ../pages/admin.php");
                } else {
                    header("Location: ../pages/dashboard.php");
                }
                exit();

            } else {
                // Invalid password
                header("Location: ../pages/login.php?error=invalid");
                exit();
            }

        } else {
            // User not found
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
