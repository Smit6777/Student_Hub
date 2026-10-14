<?php
require_once "../php/db_connect_mysqli.php";

// Fetch available courses from database
$courses = [];
$res = $conn->query("SELECT course_id, course_name FROM courses ORDER BY course_name ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $courses[] = $row;
    }
}
$conn->close();

$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student | StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 35px 40px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .form-container h2 {
            color: #1d2b42;
            margin-bottom: 6px;
            font-size: 24px;
        }

        .form-container p.subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
            font-size: 14px;
        }

        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            transition: 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input:focus, 
        .form-group select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn-submit {
            width: 100%;
            background: #2563eb;
            color: white;
            padding: 13px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #1d4ed8;
        }

        .btn-back {
            display: inline-block;
            margin-top: 15px;
            text-align: center;
            width: 100%;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-back:hover {
            color: #2563eb;
            text-decoration: underline;
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
        <h1>Add New Student</h1>
        <p>Enter student details to add them to the directory.</p>
    </header>

    <main>
        <div class="form-container">
            <h2>➕ Add Student</h2>
            <p class="subtitle">Enter the student's academic details below.</p>

            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <?php 
                        if ($error === "empty") echo "All fields are required. Please complete the form.";
                        elseif ($error === "email") echo "Please enter a valid email address.";
                        else echo "Failed to add student. Please verify input data.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="../php/student_add_process.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Student Name</label>
                    <input type="text" id="name" name="name" placeholder="e.g. Smit Makwana" required>
                </div>

                <div class="form-group">
                    <label for="email">Student Email Address</label>
                    <input type="email" id="email" name="email" placeholder="e.g. smit@example.com" required>
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number (10 digits)</label>
                    <input type="text" id="mobile" name="mobile" placeholder="e.g. 9998403012" pattern="[0-9]{10}" required>
                </div>

                <div class="form-group">
                    <label for="course_id">Enrolled Course</label>
                    <select id="course_id" name="course_id" required>
                        <option value="">-- Select Enrolled Course --</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?php echo $c['course_id']; ?>">
                                <?php echo htmlspecialchars($c['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="year">Academic Year</label>
                        <select id="year" name="year" required>
                            <option value="">Select Year</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-submit">💾 Save Student Record</button>
                <a href="students.php" class="btn-back">⬅ Cancel & Return to Student Directory</a>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 StudentHub | Designed by Smit Makwana</p>
    </footer>

    <script src="../javascripts/scripts.js"></script>
</body>
</html>
