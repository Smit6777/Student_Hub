<?php
require_once "db_connect_mysqli.php";

$status_type = "";
$message     = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name          = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $email         = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password      = isset($_POST["password"]) ? $_POST["password"] : "";
    $terms_consent = isset($_POST["terms_consent"]) ? true : false;

    if (empty($name) || empty($email) || empty($password)) {
        $status_type = "error";
        $message     = "All fields (Name, Email, Password) are required.";
    } elseif (!$terms_consent) {
        $status_type = "error";
        $message     = "You must confirm you are at least 16 and agree to the Terms of Service and Privacy Policy.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status_type = "error";
        $message     = "Invalid email format. Please provide a valid email address.";
    } elseif (strlen($password) < 6) {
        $status_type = "error";
        $message     = "Weak password! Password must contain at least 6 characters.";
    } else {
        $check_sql  = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);

        if ($check_stmt) {
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $status_type = "error";
                $message     = "Duplicate Email! This email is already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insert_sql  = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);

                if ($insert_stmt) {
                    $insert_stmt->bind_param("sss", $name, $email, $hashed_password);

                    if ($insert_stmt->execute()) {
                        $status_type = "success";
                        $message     = "Registration Successful! Your account has been securely created.";
                    } else {
                        $status_type = "error";
                        $message     = "Database insertion failed: " . $insert_stmt->error;
                    }
                    $insert_stmt->close();
                } else {
                    $status_type = "error";
                    $message     = "Failed to prepare insert query: " . $conn->error;
                }
            }
            $check_stmt->close();
        } else {
            $status_type = "error";
            $message     = "Failed to prepare check query: " . $conn->error;
        }
    }

    $conn->close();

} else {
    header("Location: ../pages/register_user.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status | StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .result-card {
            max-width: 520px;
            margin: 60px auto;
            background: #ffffff;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        .status-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }
        .msg-success {
            color: #15803d;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 25px;
        }
        .msg-error {
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 25px;
        }
        .action-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }
        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <header>
        <img src="../image/image-removebg-preview.png" alt="StudentHub Logo" width="100">
        <h1>Registration Status</h1>
        <p>StudentHub Account Registration</p>
    </header>

    <main>
        <div class="result-card">
            <?php if ($status_type === "success"): ?>
                <div class="status-icon">✅</div>
                <h2>Registration Successful!</h2>
                <div class="msg-success"><?php echo htmlspecialchars($message); ?></div>
            <?php else: ?>
                <div class="status-icon">❌</div>
                <h2>Registration Failed</h2>
                <div class="msg-error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="action-links">
                <a href="../pages/register_user.html" class="btn btn-secondary">⬅ Back to Registration</a>
                <a href="../pages/login.html" class="btn btn-primary">Go to Login ➡</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 StudentHub | Designed by Smit Makwana</p>
    </footer>
</body>
</html>
