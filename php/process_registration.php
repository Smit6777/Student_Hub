<?php
// Practical 7: PHP Form Processing with server-side validation and CSV storage
// This file receives POST data from the StudentHub registration form.

// 1. Check request method: only POST is allowed
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

// 2. Read form data safely
$name = trim($_POST["studentName"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";
$course = trim($_POST["course"] ?? "");
$year = trim($_POST["year"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$terms = isset($_POST["terms"]);

$errors = array();

// 3. Server-side validation
$allowedCourses = array("BCA", "BSc", "BTech", "MBA");
$allowedYears = array("1", "2", "3", "4");
$allowedGenders = array("Male", "Female", "Other");

if ($name === "") {
    $errors[] = "Name is required.";
} elseif (!preg_match("/^[A-Za-z ]+$/", $name)) {
    $errors[] = "Name must contain letters and spaces only.";
}

if ($email === "") {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

if ($mobile === "") {
    $errors[] = "Mobile number is required.";
} elseif (!preg_match("/^[6-9][0-9]{9}$/", $mobile)) {
    $errors[] = "Mobile number must be exactly 10 digits and start with 6 to 9.";
}

if ($password === "") {
    $errors[] = "Password is required.";
} elseif (strlen($password) < 8) {
    $errors[] = "Password must contain at least 8 characters.";
} elseif (!preg_match("/[A-Z]/", $password)) {
    $errors[] = "Password must contain at least one uppercase letter.";
} elseif (!preg_match("/[a-z]/", $password)) {
    $errors[] = "Password must contain at least one lowercase letter.";
} elseif (!preg_match("/[0-9]/", $password)) {
    $errors[] = "Password must contain at least one number.";
} elseif (!preg_match("/[@$!%*?&]/", $password)) {
    $errors[] = "Password must contain at least one special character.";
}

if ($confirmPassword === "") {
    $errors[] = "Confirm password is required.";
} elseif ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}

if ($course === "") {
    $errors[] = "Course is required.";
} elseif (!in_array($course, $allowedCourses, true)) {
    $errors[] = "Please select a valid course.";
}

if ($year === "") {
    $errors[] = "Year is required.";
} elseif (!in_array($year, $allowedYears, true)) {
    $errors[] = "Please select a valid year.";
}

if ($gender === "") {
    $errors[] = "Gender is required.";
} elseif (!in_array($gender, $allowedGenders, true)) {
    $errors[] = "Please select a valid gender.";
}

if (!$terms) {
    $errors[] = "You must accept the terms and conditions.";
}

// 4. If there are errors, show them and stop
if (count($errors) > 0) {
    echo "<html><head><title>Registration Failed</title><link rel='stylesheet' href='../css/style.css'></head><body style='padding:40px;'>";
    echo "<div style='max-width:600px;margin:40px auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 10px 24px rgba(0,0,0,0.08);'>";
    echo "<h2>Registration Failed</h2>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</li>";
    }
    echo "</ul>";
    echo "<p><a href='../pages/register.html'>Go Back</a></p>";
    echo "</div></body></html>";
    exit;
}

// 5. Sanitize accepted data before storing
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$mobile = htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8');
$course = htmlspecialchars($course, ENT_QUOTES, 'UTF-8');
$year = htmlspecialchars($year, ENT_QUOTES, 'UTF-8');
$gender = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');

// 6. Hash password (never store plain text)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 7. Save record in CSV file
$csvFile = __DIR__ . '/../data/registrations.csv';
$folder = dirname($csvFile);

if (!is_dir($folder)) {
    echo "<h2>Storage Error</h2><p>Data folder is missing.</p>";
    exit;
}

$header = array("Name", "Email", "Mobile", "Password", "Course", "Year", "Gender");

if (!file_exists($csvFile)) {
    $file = fopen($csvFile, "w");
    if ($file === false) {
        echo "<h2>Storage Error</h2><p>Unable to create the registration file.</p>";
        exit;
    }
    fputcsv($file, $header);
    fclose($file);
}

$dataRow = array($name, $email, $mobile, $hashedPassword, $course, $year, $gender);
$file = fopen($csvFile, "a");

if ($file === false) {
    echo "<h2>Storage Error</h2><p>Unable to open the registration file for writing.</p>";
    exit;
}

fputcsv($file, $dataRow);
fclose($file);

// 8. Show success page
echo "<html><head><title>Registration Successful</title><link rel='stylesheet' href='../css/style.css'></head><body style='padding:40px;'>";
    echo "<div style='max-width:600px;margin:40px auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 10px 24px rgba(0,0,0,0.08);'>";
    echo "<h2>Registration Successful!</h2>";
    echo "<p>Your student record has been saved successfully.</p>";
    echo "<p><a href='../pages/register.html'>Register Another Student</a></p>";
    echo "<p><a href='view_registrations.php'>View Registrations</a></p>";
    echo "</div>";
    echo "</body></html>";
?>
