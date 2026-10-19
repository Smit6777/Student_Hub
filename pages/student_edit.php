<?php
require_once "../php/db_connect_mysqli.php";

$id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    header("Location: students.php?error=not_found");
    exit();
}

$stmt = $conn->prepare("SELECT id, name, email, mobile, course_id, year, gender FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$student = $res->fetch_assoc();
$stmt->close();

if (!$student) {
    header("Location: students.php?error=not_found");
    exit();
}

$courses = [];
$c_res = $conn->query("SELECT course_id, course_name FROM courses ORDER BY course_name ASC");
if ($c_res) {
    while ($c_row = $c_res->fetch_assoc()) {
        $courses[] = $c_row;
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
    <title>Edit Student | StudentHub</title>
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
            background: #d97706;
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
            background: #b45309;
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
        <h1>Edit Student Details</h1>
        <p>Update student details and enrollment information.</p>
    </header>

    <main>
        <div class="form-container">
            <h2>✏️ Edit Student #<?php echo htmlspecialchars($student["id"]); ?></h2>
            <p class="subtitle">Update the student information below and save your changes.</p>

            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <?php 
                        if ($error === "empty") echo "All fields are required. Please complete the form.";
                        elseif ($error === "email") echo "Please enter a valid email address.";
                        else echo "Failed to update student details. Please verify your data.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="../php/student_edit_process.php" method="POST">
                <!-- Hidden ID field -->
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">

                <div class="form-group">
                    <label for="name">Full Student Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="<?php echo htmlspecialchars($student['name']); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Student Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($student['email']); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number (10 digits)</label>
                    <input 
                        type="text" 
                        id="mobile" 
                        name="mobile" 
                        value="<?php echo htmlspecialchars($student['mobile']); ?>" 
                        pattern="[0-9]{10}" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="course_id">Enrolled Course</label>
                    <select id="course_id" name="course_id" required>
                        <option value="">-- Select Enrolled Course --</option>
                        <?php foreach ($courses as $c): ?>
                            <option 
                                value="<?php echo $c['course_id']; ?>"
                                <?php echo ($c['course_id'] == $student['course_id']) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($c['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="year">Academic Year</label>
                        <select id="year" name="year" required>
                            <option value="1" <?php echo ($student['year'] == 1) ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2" <?php echo ($student['year'] == 2) ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3" <?php echo ($student['year'] == 3) ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4" <?php echo ($student['year'] == 4) ? 'selected' : ''; ?>>4th Year</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="Male" <?php echo ($student['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($student['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($student['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-submit">💾 Update Student Details</button>
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
