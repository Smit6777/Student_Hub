<?php
require_once "db_connect_mysqli.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Sanitize and retrieve POST inputs
    $name      = trim($_POST["name"] ?? "");
    $email     = trim($_POST["email"] ?? "");
    $mobile    = trim($_POST["mobile"] ?? "");
    $course_id = filter_var($_POST["course_id"] ?? null, FILTER_VALIDATE_INT);
    $year      = filter_var($_POST["year"] ?? null, FILTER_VALIDATE_INT);
    $gender    = trim($_POST["gender"] ?? "");

    // 2. Validate inputs
    if (empty($name) || empty($email) || empty($mobile) || !$course_id || !$year || empty($gender)) {
        header("Location: ../pages/student_add.php?error=empty");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../pages/student_add.php?error=email");
        exit();
    }

    // 3. Insert student record using MySQLi Prepared Statement
    $sql  = "INSERT INTO students (name, email, mobile, course_id, year, gender) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssiss", $name, $email, $mobile, $course_id, $year, $gender);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: ../pages/students.php?msg=added");
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("Location: ../pages/student_add.php?error=failed");
            exit();
        }
    } else {
        $conn->close();
        header("Location: ../pages/student_add.php?error=failed");
        exit();
    }

} else {
    header("Location: ../pages/students.php");
    exit();
}
?>
