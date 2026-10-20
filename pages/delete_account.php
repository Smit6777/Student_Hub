<?php
require_once "../php/auth_check.php";
require_once "../php/role_menu.php";
require_once "../php/csrf.php";

$user_id    = $_SESSION["user_id"];
$user_name  = htmlspecialchars($_SESSION["user_name"] ?? "Student");
$user_email = htmlspecialchars($_SESSION["user_email"] ?? "");
$error_msg  = "";

if (isset($_GET["error"])) {
    if ($_GET["error"] === "wrong_password") {
        $error_msg = "Incorrect password. Account deletion cancelled.";
    } elseif ($_GET["error"] === "csrf") {
        $error_msg = "Security token invalid or expired. Please try again.";
    } elseif ($_GET["error"] === "empty") {
        $error_msg = "Please enter your password to confirm account deletion.";
    }
}

$menu_items = get_role_menu();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account | StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .delete-box {
            max-width: 520px;
            margin: 40px auto;
            background: #ffffff;
            padding: 35px 30px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border-top: 5px solid #dc2626;
        }
        .delete-box h2 {
            color: #991b1b;
            margin-bottom: 12px;
            text-align: center;
        }
        .warning-text {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 14px 16px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 22px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #334155;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
        }
        .btn-delete-confirm {
            width: 100%;
            padding: 13px;
            background: #dc2626;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .btn-delete-confirm:hover {
            background: #b91c1c;
        }
        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-cancel:hover {
            text-decoration: underline;
        }
        html.dark-theme .delete-box {
            background: #1e293b;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }
        html.dark-theme .delete-box h2 {
            color: #f87171;
        }
        html.dark-theme .warning-text {
            background: #451a03;
            border-color: #78350f;
            color: #fef3c7;
        }
        html.dark-theme .form-group label {
            color: #e2e8f0;
        }
    </style>
    <script>
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.classList.add('dark-theme');
        }
    </script>
</head>
<body>

    <a href="#main-content" class="skip-link">Skip to main content</a>

    <header>
        <img src="../image/image-removebg-preview.png" alt="StudentHub Logo" width="100">
        <h1>Delete Account</h1>
        <p>Data Privacy & Erasure Request</p>
    </header>

    <nav aria-label="StudentHub Main Navigation">
        <button class="hamburger" aria-label="Toggle navigation menu">☰</button>
        <div id="nav-links">
            <?php foreach ($menu_items as $item): ?>
                <a href="<?php echo $item['link']; ?>">
                    <?php echo htmlspecialchars($item['title']); ?>
                </a>
            <?php endforeach; ?>
            <button id="theme-toggle" style="margin-left: 10px; padding: 8px 14px; font-weight: 600;">🌙 Dark Mode</button>
        </div>
    </nav>

    <main id="main-content">
        <div class="delete-box">
            <h2>⚠️ Permanent Account Deletion</h2>

            <div class="warning-text">
                <strong>Attention <?php echo $user_name; ?>:</strong>
                Deleting your account will permanently remove your login credentials, email address (<strong><?php echo $user_email; ?></strong>), and stored sessions. This action <strong>cannot be undone</strong>.
            </div>

            <?php if (!empty($error_msg)): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 18px; font-size: 14px;">
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <form action="../php/delete_account_process.php" method="POST" onsubmit="return confirm('Are you absolutely certain you want to permanently delete your account?');">
                <?php echo csrf_input(); ?>

                <div class="form-group">
                    <label for="confirm_password">Enter your current password to confirm:</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Current password" required>
                </div>

                <button type="submit" class="btn-delete-confirm">🗑️ Delete My Account Permanently</button>
                <a href="dashboard.php" class="btn-cancel">&larr; Return to Dashboard</a>
            </form>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h2>🎓 StudentHub</h2>
                <p>Your Digital Campus Portal for academics, events, workshops, and student life.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="index.html">Home</a>
                <a href="dashboard.php">Dashboard</a>
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
