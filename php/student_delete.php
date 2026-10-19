<?php
require_once "db_connect_mysqli.php";
require_once "audit_logger.php";

$id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: ../pages/students.php?error=invalid_id");
    exit();
}

$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");

if ($stmt) {
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        log_audit($conn, "Delete Student", "Deleted student ID: " . $id);
        $stmt->close();
        $conn->close();
        header("Location: ../pages/students.php?msg=deleted");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: ../pages/students.php?error=failed");
        exit();
    }
} else {
    $conn->close();
    header("Location: ../pages/students.php?error=failed");
    exit();
}
?>
