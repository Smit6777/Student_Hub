<?php
require_once "../php/auth_check.php";
require_once "../php/role_menu.php";

$user_name     = htmlspecialchars($_SESSION["user_name"] ?? "Student");
$user_email    = htmlspecialchars($_SESSION["user_email"] ?? "");
$user_role     = htmlspecialchars(ucfirst($_SESSION["user_role"] ?? "student"));
$session_id    = htmlspecialchars(session_id());
$last_active   = date("h:i:s A", $_SESSION["last_activity"] ?? time());
$forbidden_msg = (isset($_GET["error"]) && $_GET["error"] === "forbidden") ? "Access Denied: You do not have permission to view the Admin panel." : "";
$menu_items    = get_role_menu();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard-header-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .user-greeting h2 {
            color: #1d2b42;
            margin-bottom: 5px;
        }

        .role-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-left: 10px;
        }

        .btn-logout {
            display: inline-block;
            background: #ef4444;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background: #dc2626;
        }

        .session-info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .session-info-card h3 {
            color: #1e293b;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .session-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .session-item {
            background: white;
            padding: 12px 16px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
        }

        .session-item small {
            color: #64748b;
            display: block;
            font-size: 12px;
        }

        .session-item strong {
            color: #0f172a;
            font-size: 14px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .content-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .content-card h3 {
            color: #1d2b42;
            margin-bottom: 15px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
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
        <h1>🎓 Student Dashboard</h1>
        <p>Welcome to your student portal and academic hub.</p>
    </header>

    <nav>
        <button class="hamburger" aria-label="Toggle menu">☰</button>
        <div id="nav-links">
            <?php foreach ($menu_items as $item): ?>
                <?php if (!empty($item["special"]) && $item["special"] === "logout"): ?>
                    <a href="<?php echo $item['link']; ?>" style="color: #f87171; font-weight: bold;">🚪 <?php echo htmlspecialchars($item['title']); ?></a>
                <?php else: ?>
                    <a href="<?php echo $item['link']; ?>" class="<?php echo ($item['link'] === 'dashboard.php') ? 'active-link' : ''; ?>">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
            <button id="theme-toggle" style="margin-left: 10px; padding: 8px 14px; font-weight: 600;">🌙 Dark Mode</button>
        </div>
    </nav>

    <main>
        <?php if (!empty($forbidden_msg)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($forbidden_msg); ?></div>
        <?php endif; ?>

        <!-- User Greeting and Logout Header -->
        <div class="dashboard-header-card">
            <div class="user-greeting">
                <h2>Welcome, <?php echo $user_name; ?>! <span class="role-badge"><?php echo $user_role; ?></span></h2>
                <p style="color: #64748b; margin: 0;">Logged in as: <strong><?php echo $user_email; ?></strong></p>
            </div>
            <div>
                <a href="../php/logout.php" class="btn-logout">🚪 Logout</a>
            </div>
        </div>

        <!-- Account Details -->
        <div class="session-info-card">
            <h3>👤 Account & Session Details</h3>
            <div class="session-grid">
                <div class="session-item">
                    <small>Status</small>
                    <strong style="color: #16a34a;">Active</strong>
                </div>
                <div class="session-item">
                    <small>User Role</small>
                    <strong><?php echo $user_role; ?></strong>
                </div>
                <div class="session-item">
                    <small>Session Timeout</small>
                    <strong>15 Minutes</strong>
                </div>
                <div class="session-item">
                    <small>Last Active Time</small>
                    <strong><?php echo $last_active; ?></strong>
                </div>
            </div>
        </div>

        <!-- Academic & Event Overview -->
        <div class="grid-2">
            <div class="content-card">
                <h3>📚 Academic Overview</h3>
                <p>Welcome to your central student space. Here you can track your registered courses, attendance, and exam notifications.</p>
                <ul style="color: #475569; line-height: 1.8; margin-top: 10px;">
                    <li>Semester: <strong>Semester 3 (2026)</strong></li>
                    <li>Current Standing: <strong>Good Standing</strong></li>
                    <li>Library Access: <strong>Active</strong></li>
                </ul>
            </div>

            <div class="content-card">
                <h3>📅 Upcoming Campus Events</h3>
                <p>Stay up to date with workshops and technical hackathons:</p>
                <ul style="color: #475569; line-height: 1.8; margin-top: 10px;">
                    <li>Web Development Workshop &mdash; <em>Oct 20, 2026</em></li>
                    <li>AI & Machine Learning Seminar &mdash; <em>Oct 28, 2026</em></li>
                    <li>Annual Hackathon &mdash; <em>Nov 05, 2026</em></li>
                </ul>
            </div>
        </div>

        <!-- Privacy & Account Options -->
        <div class="content-card" style="margin-bottom: 25px;">
            <h3>🛡️ Privacy & Account Settings</h3>
            <p style="color: #64748b; margin-bottom: 15px;">Manage your student data rights and account security settings in compliance with privacy guidelines:</p>
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                <a href="privacy.html" style="color: #2563eb; text-decoration: none; font-weight: 600;">📄 View Privacy Policy</a>
                <span style="color: #cbd5e1;">|</span>
                <a href="cookie-policy.html" style="color: #2563eb; text-decoration: none; font-weight: 600;">🍪 Cookie Settings</a>
                <span style="color: #cbd5e1;">|</span>
                <a href="delete_account.php" style="color: #dc2626; text-decoration: none; font-weight: 600;">🗑️ Permanent Account Deletion</a>
            </div>
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
