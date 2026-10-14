<?php
require_once "../php/auth_check.php";

// Strict role guard: only allow admin role
require_role("admin");

require_once "../php/db_connect_mysqli.php";

$admin_name  = htmlspecialchars($_SESSION["user_name"] ?? "Administrator");
$admin_email = htmlspecialchars($_SESSION["user_email"] ?? "");
$last_active = date("h:i:s A", $_SESSION["last_activity"] ?? time());

// Fetch users from database for admin overview
$users_list = [];
$total_users = 0;
$result = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users_list[] = $row;
    }
    $total_users = count($users_list);
}
$conn->close();
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

        .table-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        .table-card h3 {
            color: #1d2b42;
            margin-bottom: 15px;
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
        <p>Restricted Role-Based Administrative Management.</p>
    </header>

    <nav>
        <button class="hamburger" aria-label="Toggle menu">☰</button>
        <div id="nav-links">
            <a href="index.html">Home</a>
            <a href="admin.php">Admin Panel</a>
            <a href="students.php">Students</a>
            <a href="events.html">Events</a>
            <a href="../php/logout.php" style="color: #f87171; font-weight: bold;">Logout</a>
            <button id="theme-toggle" style="margin-left: 10px; padding: 8px 14px; font-weight: 600;">🌙 Dark Mode</button>
        </div>
    </nav>

    <main>
        <!-- Admin Greeting Header -->
        <div class="admin-header-card">
            <div>
                <h2 style="color: #1d2b42; margin-bottom: 5px;">
                    Welcome, <?php echo $admin_name; ?>! <span class="role-badge-admin">Admin Privileges</span>
                </h2>
                <p style="color: #64748b; margin: 0;">Session Email: <strong><?php echo $admin_email; ?></strong> | Last Active: <?php echo $last_active; ?></p>
            </div>
            <div>
                <a href="../php/logout.php" class="btn-logout">🚪 Secure Logout</a>
            </div>
        </div>

        <!-- System Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <p>Total Registered Accounts</p>
                <h3><?php echo $total_users; ?></h3>
            </div>
            <div class="stat-card" style="border-left-color: #16a34a;">
                <p>Role Security</p>
                <h3 style="font-size: 20px; color: #16a34a; margin-top: 10px;">Role-Based Access</h3>
            </div>
            <div class="stat-card" style="border-left-color: #f59e0b;">
                <p>Session Guard</p>
                <h3 style="font-size: 20px; color: #f59e0b; margin-top: 10px;">15m Inactivity Lock</h3>
            </div>
        </div>

        <!-- Registered Users Table (Role Management) -->
        <div class="table-card">
            <h3>👥 Registered System Accounts</h3>
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
        <p>&copy; 2026 StudentHub | Designed by Smit Makwana</p>
    </footer>

    <script src="../javascripts/scripts.js"></script>
</body>
</html>
