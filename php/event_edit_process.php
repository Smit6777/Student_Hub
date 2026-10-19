<?php
require_once "db_connect_mysqli.php";
require_once "audit_logger.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $event_id       = filter_var($_POST["event_id"] ?? null, FILTER_VALIDATE_INT);
    $current_poster = trim($_POST["current_poster"] ?? "");
    $title          = trim($_POST["title"] ?? "");
    $category       = trim($_POST["category"] ?? "");
    $event_date     = trim($_POST["event_date"] ?? "");
    $location       = trim($_POST["location"] ?? "");
    $description    = trim($_POST["description"] ?? "");

    if (!$event_id || empty($title) || empty($category) || empty($event_date) || empty($location)) {
        header("Location: ../pages/event_edit.php?id=" . urlencode($event_id) . "&error=empty");
        exit();
    }

    $poster_filename = $current_poster;

    if (isset($_FILES["poster"]) && $_FILES["poster"]["error"] === UPLOAD_ERR_OK) {
        $file_tmp   = $_FILES["poster"]["tmp_name"];
        $file_name  = $_FILES["poster"]["name"];
        $file_size  = $_FILES["poster"]["size"];
        $max_size   = 2 * 1024 * 1024;

        if ($file_size > $max_size) {
            header("Location: ../pages/event_edit.php?id=" . urlencode($event_id) . "&error=file_size");
            exit();
        }

        $allowed_exts  = ["jpg", "jpeg", "png", "webp"];
        $allowed_mimes = ["image/jpeg", "image/png", "image/webp"];

        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_mime = mime_content_type($file_tmp);

        if (!in_array($ext, $allowed_exts) || !in_array($file_mime, $allowed_mimes)) {
            header("Location: ../pages/event_edit.php?id=" . urlencode($event_id) . "&error=file_type");
            exit();
        }

        $upload_dir = "../uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $new_filename = "event_" . time() . "_" . uniqid() . "." . $ext;
        $target_path  = $upload_dir . $new_filename;

        if (move_uploaded_file($file_tmp, $target_path)) {
            if (!empty($current_poster) && file_exists($upload_dir . $current_poster)) {
                unlink($upload_dir . $current_poster);
            }
            $poster_filename = $new_filename;
        } else {
            header("Location: ../pages/event_edit.php?id=" . urlencode($event_id) . "&error=upload_failed");
            exit();
        }
    }

    $sql  = "UPDATE events SET title = ?, event_date = ?, location = ?, category = ?, poster = ?, description = ? WHERE event_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssssi", $title, $event_date, $location, $category, $poster_filename, $description, $event_id);

        if ($stmt->execute()) {
            log_audit($conn, "Update Event", "Updated event: " . $title . " (ID: " . $event_id . ")");
            $stmt->close();
            $conn->close();
            header("Location: ../pages/events_manage.php?msg=updated");
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("Location: ../pages/event_edit.php?id=" . urlencode($event_id) . "&error=failed");
            exit();
        }
    } else {
        $conn->close();
        header("Location: ../pages/event_edit.php?id=" . urlencode($event_id) . "&error=failed");
        exit();
    }

} else {
    header("Location: ../pages/events_manage.php");
    exit();
}
?>
