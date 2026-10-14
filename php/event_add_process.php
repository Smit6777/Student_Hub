<?php
require_once "db_connect_mysqli.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Sanitize text inputs
    $title       = trim($_POST["title"] ?? "");
    $category    = trim($_POST["category"] ?? "");
    $event_date  = trim($_POST["event_date"] ?? "");
    $location    = trim($_POST["location"] ?? "");
    $description = trim($_POST["description"] ?? "");

    // Basic validation
    if (empty($title) || empty($category) || empty($event_date) || empty($location)) {
        header("Location: ../pages/event_add.php?error=empty");
        exit();
    }

    $poster_filename = null;

    // 2. Validate and process poster image upload
    if (isset($_FILES["poster"]) && $_FILES["poster"]["error"] === UPLOAD_ERR_OK) {
        $file_tmp   = $_FILES["poster"]["tmp_name"];
        $file_name  = $_FILES["poster"]["name"];
        $file_size  = $_FILES["poster"]["size"];
        $max_size   = 2 * 1024 * 1024; // 2MB limit

        // A. File Size Validation
        if ($file_size > $max_size) {
            header("Location: ../pages/event_add.php?error=file_size");
            exit();
        }

        // B. File Type and MIME Validation
        $allowed_exts  = ["jpg", "jpeg", "png", "webp"];
        $allowed_mimes = ["image/jpeg", "image/png", "image/webp"];

        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_mime = mime_content_type($file_tmp);

        if (!in_array($ext, $allowed_exts) || !in_array($file_mime, $allowed_mimes)) {
            header("Location: ../pages/event_add.php?error=file_type");
            exit();
        }

        // C. Generate safe unique filename to prevent overwriting
        $upload_dir = "../uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $poster_filename = "event_" . time() . "_" . uniqid() . "." . $ext;
        $target_path     = $upload_dir . $poster_filename;

        if (!move_uploaded_file($file_tmp, $target_path)) {
            header("Location: ../pages/event_add.php?error=upload_failed");
            exit();
        }
    }

    // 3. Insert event record using MySQLi Prepared Statement
    $sql  = "INSERT INTO events (title, event_date, location, category, poster, description) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssss", $title, $event_date, $location, $category, $poster_filename, $description);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: ../pages/events_manage.php?msg=added");
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("Location: ../pages/event_add.php?error=failed");
            exit();
        }
    } else {
        $conn->close();
        header("Location: ../pages/event_add.php?error=failed");
        exit();
    }

} else {
    header("Location: ../pages/events_manage.php");
    exit();
}
?>
