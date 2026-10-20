<?php
require_once "../php/auth_check.php";
require_role("admin");

require_once "../php/db_connect_mysqli.php";
require_once "../php/role_menu.php";

$admin_name  = htmlspecialchars($_SESSION["user_name"] ?? "Administrator");
$admin_email = htmlspecialchars($_SESSION["user_email"] ?? "");
$last_active = date("h:i:s A", $_SESSION["last_activity"] ?? time());

$total_users = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($res) {
    $row = $res->fetch_assoc();
    $total_users = (int)$row["total"];
}

$total_students = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM students");
if ($res) {
    $row = $res->fetch_assoc();
    $total_students = (int)$row["total"];
}

$total_events = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM events");
if ($res) {
    $row = $res->fetch_assoc();
    $total_events = (int)$row["total"];
}

$total_registrations = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM event_registrations");
if ($res) {
    $row = $res->fetch_assoc();
    $total_registrations = (int)$row["total"];
}

$latest_registrations = [];
$reg_sql = "SELECT r.reg_id, s.name AS student_name, s.email AS student_email, e.title AS event_title, e.category, r.registration_date 
            FROM event_registrations r 
            JOIN students s ON r.student_id = s.id 
            JOIN events e ON r.event_id = e.event_id 
            ORDER BY r.registration_date DESC 
            LIMIT 5";
$reg_res = $conn->query($reg_sql);
if ($reg_res) {
    while ($row = $reg_res->fetch_assoc()) {
        $latest_registrations[] = $row;
    }
}

$audit_logs = [];
$audit_res = $conn->query("SELECT log_id, user_name, action, details, ip_address, created_at FROM audit_logs ORDER BY log_id DESC LIMIT 5");
if ($audit_res) {
    while ($row = $audit_res->fetch_assoc()) {
        $audit_logs[] = $row;
    }
}

$users_list = [];
$res = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC LIMIT 5");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $users_list[] = $row;
    }
}
$conn->close();

$menu_items = get_role_menu();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-header-card {
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

        .role-badge-admin {
            display: inline-block;
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border-left: 5px solid #2563eb;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card h3 {
            font-size: 28px;
            color: #1e293b;
            margin: 5px 0 0;
        }

        .stat-card p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }

        .quick-actions {
            background: white;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .quick-actions strong {
            color: #1e293b;
            font-size: 14px;
        }

        .action-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            color: #1e293b;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
        }

        .action-chip:hover {
            background: #2563eb;
            color: white;
        }

        .table-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            overflow-x: auto;
            margin-bottom: 25px;
        }

        .table-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .table-card h3 {
            color: #1d2b42;
            margin: 0;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f8fafc;
            color: #334155;
            font-weight: 600;
        }

        .badge-student {
            background: #e0f2fe;
            color: #0369a1;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-admin {
            background: #fef3c7;
            color: #b45309;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-action {
            display: inline-block;
            background: #ede9fe;
            color: #6d28d9;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-category {
            background: #f1f5f9;
            color: #475569;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
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
        <h1>⚙️ Administrator Control Panel</h1>
        <p>Manage campus portal users, events, and records.</p>
    </header>

    <nav>
        <button class="hamburger" aria-label="Toggle menu">☰</button>
        <div id="nav-links">
            <?php foreach ($menu_items as $item): ?>
                <?php if (!empty($item["special"]) && $item["special"] === "logout"): ?>
                    <a href="<?php echo $item['link']; ?>" style="color: #f87171; font-weight: bold;">🚪 <?php echo htmlspecialchars($item['title']); ?></a>
                <?php else: ?>
                    <a href="<?php echo $item['link']; ?>" class="<?php echo ($item['link'] === 'admin.php') ? 'active-link' : ''; ?>">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
            <button id="theme-toggle" style="margin-left: 10px; padding: 8px 14px; font-weight: 600;">🌙 Dark Mode</button>
        </div>
    </nav>

    <main>
        <div class="admin-header-card">
            <div>
                <h2 style="color: #1d2b42; margin-bottom: 5px;">
                    Welcome, <?php echo $admin_name; ?>! <span class="role-badge-admin">Administrator</span>
                </h2>
                <p style="color: #64748b; margin: 0;">Session Email: <strong><?php echo $admin_email; ?></strong> | Last Active: <?php echo $last_active; ?></p>
            </div>
            <div>
                <a href="../php/logout.php" class="btn-logout">🚪 Logout</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <p>Total User Accounts</p>
                <h3><?php echo $total_users; ?></h3>
            </div>
            <div class="stat-card" style="border-left-color: #16a34a;">
                <p>Registered Students</p>
                <h3><?php echo $total_students; ?></h3>
            </div>
            <div class="stat-card" style="border-left-color: #f59e0b;">
                <p>Campus Events</p>
                <h3><?php echo $total_events; ?></h3>
            </div>
            <div class="stat-card" style="border-left-color: #8b5cf6;">
                <p>Event Registrations</p>
                <h3><?php echo $total_registrations; ?></h3>
            </div>
        </div>

        <div class="quick-actions">
            <strong>⚡ Quick Actions:</strong>
            <a href="students.php" class="action-chip">👥 Students Directory</a>
            <a href="student_add.php" class="action-chip">➕ Add Student</a>
            <a href="students_ajax.php" class="action-chip">⚡ Live Directory</a>
            <a href="events_manage.php" class="action-chip">📅 Events Directory</a>
            <a href="event_add.php" class="action-chip">➕ Add Event</a>
        </div>

        <div class="table-card">
            <div class="table-card-header">
                <h3>🎟️ Latest Event Registrations</h3>
                <a href="events_manage.php" class="action-chip" style="font-size: 12px;">View Events</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Reg ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Event Title</th>
                        <th>Category</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($latest_registrations) > 0): ?>
                        <?php foreach ($latest_registrations as $r): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($r["reg_id"]); ?></td>
                                <td><strong><?php echo htmlspecialchars($r["student_name"]); ?></strong></td>
                                <td><?php echo htmlspecialchars($r["student_email"]); ?></td>
                                <td><?php echo htmlspecialchars($r["event_title"]); ?></td>
                                <td><span class="badge-category"><?php echo htmlspecialchars($r["category"]); ?></span></td>
                                <td><?php echo htmlspecialchars($r["registration_date"]); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">No event registrations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card">
            <div class="table-card-header">
                <h3>📜 Activity Log</h3>
                <span style="font-size: 13px; color: #64748b;">Recent portal activity</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($audit_logs) > 0): ?>
                        <?php foreach ($audit_logs as $log): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($log["log_id"]); ?></td>
                                <td><strong><?php echo htmlspecialchars($log["user_name"]); ?></strong></td>
                                <td><span class="badge-action"><?php echo htmlspecialchars($log["action"]); ?></span></td>
                                <td><?php echo htmlspecialchars($log["details"]); ?></td>
                                <td><code><?php echo htmlspecialchars($log["ip_address"]); ?></code></td>
                                <td><?php echo htmlspecialchars($log["created_at"]); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">No recent activity found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card">
            <div class="table-card-header">
                <h3>👥 Registered Accounts</h3>
                <span style="font-size: 13px; color: #64748b;">User accounts</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Assigned Role</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users_list) > 0): ?>
                        <?php foreach ($users_list as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u["id"]); ?></td>
                                <td><?php echo htmlspecialchars($u["name"]); ?></td>
                                <td><?php echo htmlspecialchars($u["email"]); ?></td>
                                <td>
                                    <?php if ($u["role"] === "admin"): ?>
                                        <span class="badge-admin">Admin</span>
                                    <?php else: ?>
                                        <span class="badge-student">Student</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($u["created_at"]); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748b;">No registered users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
                <a href="admin.php">Admin Panel</a>
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
