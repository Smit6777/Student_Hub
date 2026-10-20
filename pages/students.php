<?php
require_once "../php/db_connect_mysqli.php";

$search        = trim($_GET["search"] ?? "");
$course_filter = trim($_GET["course_id"] ?? "");
$year_filter   = trim($_GET["year"] ?? "");

$courses_list = [];
$c_res = $conn->query("SELECT course_id, course_name FROM courses ORDER BY course_name ASC");
if ($c_res) {
    while ($c_row = $c_res->fetch_assoc()) {
        $courses_list[] = $c_row;
    }
}

$sql    = "SELECT s.id, s.name, s.email, s.mobile, s.course_id, c.course_name, s.year, s.gender 
           FROM students s 
           LEFT JOIN courses c ON s.course_id = c.course_id 
           WHERE 1=1";
$params = [];
$types  = "";

if ($search !== "") {
    $sql     .= " AND (s.name LIKE ? OR s.email LIKE ? OR c.course_name LIKE ?)";
    $term     = "%" . $search . "%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $types   .= "sss";
}

if ($course_filter !== "" && is_numeric($course_filter)) {
    $sql     .= " AND s.course_id = ?";
    $params[] = (int)$course_filter;
    $types   .= "i";
}

if ($year_filter !== "" && is_numeric($year_filter)) {
    $sql     .= " AND s.year = ?";
    $params[] = (int)$year_filter;
    $types   .= "i";
}

$sql .= " ORDER BY s.id DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$total_count = count($students);

$stmt->close();
$conn->close();

$msg   = $_GET["msg"] ?? "";
$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Directory | StudentHub</title>
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

        /* Filter Controls */
        .filter-card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            margin-bottom: 25px;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-input {
            flex: 2;
            min-width: 220px;
            padding: 11px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
        }

        .filter-input:focus, .filter-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .filter-select {
            flex: 1;
            min-width: 160px;
            padding: 11px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            outline: none;
            cursor: pointer;
        }

        .btn-filter {
            background: #0ea5e9;
            color: white;
            padding: 11px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-filter:hover {
            background: #0284c7;
        }

        .btn-reset {
            background: #f1f5f9;
            color: #475569;
            padding: 11px 18px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-reset:hover {
            background: #e2e8f0;
        }

        /* Feedback Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
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

        /* Table Card */
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
        }

        tr:hover td {
            background: #f8fafc;
        }

        /* Badges */
        .badge-course {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-year {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            background: #f1f5f9;
            color: #475569;
        }

        /* Action Buttons */
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

        .empty-state h4 {
            color: #1e293b;
            margin-bottom: 6px;
            font-size: 18px;
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
        <h1>Student Directory</h1>
        <p>Browse, search, and manage registered campus students.</p>
    </header>

    <nav>
        <button class="hamburger" aria-label="Toggle menu">☰</button>
        <div id="nav-links">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="students.php" style="font-weight: 700; color: #2563eb;">Students</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="events.html">Events</a>
            <a href="contact.html">Contact</a>
            <a href="login.php">Login</a>
            <button id="theme-toggle" style="margin-left: 10px; padding: 8px 14px; font-weight: 600;">🌙 Dark Mode</button>
        </div>
    </nav>

    <div class="container">

        <!-- UI Feedback Alerts -->
        <?php if ($msg === "added"): ?>
            <div class="alert alert-success">✅ Success: New student record has been added successfully!</div>
        <?php elseif ($msg === "updated"): ?>
            <div class="alert alert-success">✅ Success: Student details have been updated successfully!</div>
        <?php elseif ($msg === "deleted"): ?>
            <div class="alert alert-success">🗑️ Success: Student record has been deleted successfully!</div>
        <?php elseif ($error === "not_found"): ?>
            <div class="alert alert-danger">❌ Error: The requested student record could not be found.</div>
        <?php elseif ($error === "failed"): ?>
            <div class="alert alert-danger">❌ Error: Database operation failed. Please check inputs and try again.</div>
        <?php endif; ?>

        <!-- Module Header -->
        <div class="page-header">
            <div class="page-title">
                <h2>🎓 Student Directory</h2>
                <p>Manage student enrollments, edit information, search and filter records.</p>
            </div>
            <div>
                <a href="student_add.php" class="btn-add">➕ Add New Student</a>
            </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="filter-card">
            <form action="students.php" method="GET" class="filter-form">
                <input 
                    type="text" 
                    name="search" 
                    class="filter-input" 
                    placeholder="🔍 Search by name, email, or course..." 
                    value="<?php echo htmlspecialchars($search); ?>"
                >

                <select name="course_id" class="filter-select">
                    <option value="">All Courses</option>
                    <?php foreach ($courses_list as $c): ?>
                        <option value="<?php echo $c['course_id']; ?>" <?php echo ((string)$c['course_id'] === $course_filter) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="year" class="filter-select">
                    <option value="">All Years</option>
                    <option value="1" <?php echo ($year_filter === "1") ? "selected" : ""; ?>>1st Year</option>
                    <option value="2" <?php echo ($year_filter === "2") ? "selected" : ""; ?>>2nd Year</option>
                    <option value="3" <?php echo ($year_filter === "3") ? "selected" : ""; ?>>3rd Year</option>
                    <option value="4" <?php echo ($year_filter === "4") ? "selected" : ""; ?>>4th Year</option>
                </select>

                <button type="submit" class="btn-filter">Search & Filter</button>
                <a href="students.php" class="btn-reset">↺ Reset</a>
            </form>
        </div>

        <!-- Students Records Table -->
        <div class="table-card">
            <div class="table-meta">
                <span>Showing <strong><?php echo $total_count; ?></strong> registered students</span>
                <span>Sorted by latest enrollment</span>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Email Address</th>
                            <th>Mobile</th>
                            <th>Enrolled Course</th>
                            <th>Year</th>
                            <th>Gender</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_count > 0): ?>
                            <?php foreach ($students as $s): ?>
                                <tr>
                                    <td><strong>#<?php echo htmlspecialchars($s["id"]); ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars($s["name"]); ?></strong></td>
                                    <td><?php echo htmlspecialchars($s["email"]); ?></td>
                                    <td><?php echo htmlspecialchars($s["mobile"]); ?></td>
                                    <td>
                                        <span class="badge-course">
                                            <?php echo htmlspecialchars($s["course_name"] ?? "Not Assigned"); ?>
                                        </span>
                                    </td>
                                    <td><span class="badge-year">Year <?php echo htmlspecialchars($s["year"]); ?></span></td>
                                    <td><?php echo htmlspecialchars($s["gender"]); ?></td>
                                    <td style="text-align: center;">
                                        <div class="actions" style="justify-content: center;">
                                            <a href="student_edit.php?id=<?php echo $s['id']; ?>" class="btn-edit">✏️ Edit</a>
                                            <!-- Delete confirmation alert -->
                                            <a 
                                                href="../php/student_delete.php?id=<?php echo $s['id']; ?>" 
                                                class="btn-delete" 
                                                onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars(addslashes($s['name'])); ?>? This action cannot be undone.');"
                                            >
                                                🗑️ Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <h4>No Student Records Found</h4>
                                        <p>No students match your search or filter criteria. Try adjusting your search query or reset filters.</p>
                                        <a href="students.php" class="btn-reset" style="display: inline-block; margin-top: 10px;">View All Students</a>
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
        <div class="footer-container">
            <div class="footer-section">
                <h2>🎓 StudentHub</h2>
                <p>Your Digital Campus Portal for academics, events, and student life.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="index.html">Home</a>
                <a href="students.php">Students</a>
                <a href="dashboard.php">Dashboard</a>
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
