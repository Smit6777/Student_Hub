<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION["user_id"])) {
    if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin") {
        header("Location: admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

require_once "../php/csrf.php";

$error_msg   = "";
$success_msg = "";

if (isset($_GET["error"])) {
    switch ($_GET["error"]) {
        case "invalid":
            $error_msg = "Invalid email or password. Please try again.";
            break;
        case "unauthorized":
            $error_msg = "Access denied! Please login to access your dashboard.";
            break;
        case "timeout":
            $error_msg = "Your session has expired due to 15 minutes of inactivity. Please login again.";
            break;
        case "forbidden":
            $error_msg = "Access restricted! You do not have permission to view that page.";
            break;
        case "empty":
            $error_msg = "Please enter both email and password.";
            break;
        case "throttled":
            $wait = isset($_GET["wait"]) ? (int)$_GET["wait"] : 5;
            $error_msg = "Too many failed attempts. Please wait " . $wait . " minute(s) before trying again.";
            break;
        case "csrf":
            $error_msg = "Security token expired or invalid. Please try logging in again.";
            break;
    }
}

if (isset($_GET["msg"])) {
    if ($_GET["msg"] === "logged_out") {
        $success_msg = "You have been logged out successfully.";
    } elseif ($_GET["msg"] === "account_deleted") {
        $success_msg = "Your account and associated data have been permanently deleted.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .login-card {
            background: white;
            padding: 40px;
            margin: 40px auto;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            max-width: 450px;
        }

        .login-card h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #1d2b42;
        }

        .alert-box {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #1d2b42;
        }

        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
            transition: 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .remember-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #475569;
        }

        .remember-group input[type="checkbox"] {
            width: 17px;
            height: 17px;
            cursor: pointer;
        }

        .remember-group label {
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #38bdf8, #2563eb);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37,99,235,0.3);
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #64748b;
            font-size: 14px;
        }

        .register-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.classList.add('dark-theme');
        }
    </script>
</head>
<body>

    <header>
        <img src="../image/image-removebg-preview.png" alt="StudentHub Logo" width="100">
        <h1>StudentHub Login</h1>
        <p>Welcome back! Please login to your account.</p>
    </header>

    <nav>
        <button class="hamburger" aria-label="Toggle menu">☰</button>
        <div id="nav-links">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="register_user.html">Register</a>
            <a href="login.php">Login</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="events.html">Events</a>
            <a href="contact.html">Contact</a>
            <button id="theme-toggle" style="margin-left: 10px; padding: 8px 14px; font-weight: 600;">🌙 Dark Mode</button>
        </div>
    </nav>

    <main>
        <div class="login-card">
            <h2>🔐 Account Login</h2>

            <?php if (!empty($error_msg)): ?>
                <div class="alert-box alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <?php if (!empty($success_msg)): ?>
                <div class="alert-box alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>

            <form action="../php/process_login.php" method="POST">
                <?php echo csrf_input(); ?>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="e.g. student@studenthub.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>

                <div class="remember-group">
                    <input type="checkbox" id="remember_me" name="remember_me" value="1">
                    <label for="remember_me">Remember Me for 30 days</label>
                </div>

                <button type="submit" class="btn-login">🚀 Login</button>
            </form>

            <p class="register-link">
                Don't have an account? <a href="register_user.html">Register here</a>
            </p>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h2>🎓 StudentHub</h2>
                <p>Your Digital Campus Portal for academics, events, and student life.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="index.html">Home</a>
                <a href="about.html">About</a>
                <a href="events.html">Events</a>
                <a href="contact.html">Contact</a>
                <a href="privacy.html">Privacy Policy</a>
                <a href="terms.html">Terms of Service</a>
                <a href="cookie-policy.html">Cookie Policy</a>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>📧 smitmakwana6777@gmail.com</p>
                <p>📞 +91 9998403012</p>
                <p>📍 CHARUSAT University Campus, Changa, Gujarat - 388421</p>
            </div>
        </div>
        <hr>
        <p class="copyright">&copy; 2026 StudentHub | Designed by Smit Makwana</p>
    </footer>

    <script src="../javascripts/scripts.js"></script>
</body>
</html>
