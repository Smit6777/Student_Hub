<?php
require_once "../php/db_connect_mysqli.php";

$events = [];
$sql    = "SELECT event_id, title, event_date, location, category, poster, description FROM events ORDER BY event_date ASC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
$total_events = count($events);
$conn->close();

$msg   = $_GET["msg"] ?? "";
$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events | StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            padding: 25px 30px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .page-title h2 {
            color: #1d2b42;
            font-size: 26px;
            margin-bottom: 4px;
        }

        .page-title p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }

        .btn-add {
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            box-shadow: 0 4px 12px rgba(37,99,235,0.25);
        }

        .btn-add:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        /* Feedback Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Events Table */
        .table-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .table-meta {
            padding: 16px 24px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #64748b;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f8fafc;
            color: #334155;
            padding: 14px 18px;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .poster-thumb {
            width: 80px;
            height: 55px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            display: block;
        }

        .poster-placeholder {
            width: 80px;
            height: 55px;
            background: #f1f5f9;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
        }

        .badge-cat {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #e0f2fe;
            color: #0369a1;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-edit:hover {
            background: #fde68a;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-delete:hover {
            background: #fecaca;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #64748b;
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
        <h1>Event Management</h1>
        <p>Manage campus workshops, hackathons, seminars, and event posters.</p>
    </header>

    <nav>
        <button class="hamburger" aria-label="Toggle menu">☰</button>
        <div id="nav-links">
            <a href="index.html">Home</a>
            <a href="admin.php">Admin Panel</a>
            <a href="events_manage.php" style="font-weight: 700; color: #2563eb;">Manage Events</a>
            <a href="students.php">Students</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="../php/logout.php" style="color: #f87171; font-weight: bold;">Logout</a>
            <button id="theme-toggle" style="margin-left: 10px; padding: 8px 14px; font-weight: 600;">🌙 Dark Mode</button>
        </div>
    </nav>

    <div class="container">

        <!-- Feedback Alert Messages -->
        <?php if ($msg === "added"): ?>
            <div class="alert alert-success">✅ Success: New event and poster uploaded successfully!</div>
        <?php elseif ($msg === "updated"): ?>
            <div class="alert alert-success">✅ Success: Event details have been updated successfully!</div>
        <?php elseif ($msg === "deleted"): ?>
            <div class="alert alert-success">🗑️ Success: Event record and poster file deleted successfully!</div>
        <?php elseif ($error === "file_type"): ?>
            <div class="alert alert-danger">❌ File Error: Only JPG, JPEG, PNG, and WebP poster files are allowed.</div>
        <?php elseif ($error === "file_size"): ?>
            <div class="alert alert-danger">❌ File Error: Poster file exceeds the maximum 2MB size limit.</div>
        <?php elseif ($error === "upload_failed"): ?>
            <div class="alert alert-danger">❌ Upload Error: Failed to upload poster image to the server.</div>
        <?php elseif ($error === "empty"): ?>
            <div class="alert alert-danger">❌ Validation Error: Please fill in all required event details.</div>
        <?php elseif ($error === "not_found"): ?>
            <div class="alert alert-danger">❌ Error: The requested event could not be found.</div>
        <?php elseif ($error === "failed"): ?>
            <div class="alert alert-danger">❌ Error: Database operation failed. Please try again.</div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h2>📅 Campus Events Directory</h2>
                <p>Add new events, upload custom event posters, and update event schedules.</p>
            </div>
            <div>
                <a href="event_add.php" class="btn-add">➕ Add New Event</a>
            </div>
        </div>

        <!-- Events Table -->
        <div class="table-card">
            <div class="table-meta">
                <span>Showing <strong><?php echo $total_events; ?></strong> upcoming events</span>
                <span>Sorted by event date</span>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Poster</th>
                            <th>Event Title</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Description</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_events > 0): ?>
                            <?php foreach ($events as $e): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($e["poster"]) && file_exists("../uploads/" . $e["poster"])): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($e['poster']); ?>" alt="Poster" class="poster-thumb">
                                        <?php else: ?>
                                            <div class="poster-placeholder">No Poster</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($e["title"]); ?></strong></td>
                                    <td><span class="badge-cat"><?php echo htmlspecialchars($e["category"]); ?></span></td>
                                    <td><?php echo htmlspecialchars(date("M d, Y", strtotime($e["event_date"]))); ?></td>
                                    <td><?php echo htmlspecialchars($e["location"]); ?></td>
                                    <td style="color: #64748b; font-size: 13px;">
                                        <?php 
                                            $desc = $e["description"] ?? "No description available.";
                                            echo htmlspecialchars(strlen($desc) > 50 ? substr($desc, 0, 50) . "..." : $desc); 
                                        ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="actions" style="justify-content: center;">
                                            <a href="event_edit.php?id=<?php echo $e['event_id']; ?>" class="btn-edit">✏️ Edit</a>
                                            <!-- Delete confirmation check -->
                                            <a 
                                                href="../php/event_delete.php?id=<?php echo $e['event_id']; ?>" 
                                                class="btn-delete" 
                                                onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars(addslashes($e['title'])); ?>?');"
                                            >
                                                🗑️ Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <h4>No Events Found</h4>
                                        <p>Click "Add New Event" to create a new workshop, hackathon, or seminar with a poster.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <footer>
        <p>&copy; 2026 StudentHub | Designed by Smit Makwana</p>
    </footer>

    <script src="../javascripts/scripts.js"></script>
</body>
</html>
