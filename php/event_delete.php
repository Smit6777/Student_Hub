<?php
require_once "db_connect_mysqli.php";

$id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: ../pages/events_manage.php?error=not_found");
    exit();
}

// 1. Fetch poster file name so we can delete the file from server
$p_stmt = $conn->prepare("SELECT poster FROM events WHERE event_id = ?");
if ($p_stmt) {
    $p_stmt->bind_param("i", $id);
    $p_stmt->execute();
    $p_res = $p_stmt->get_result();
    if ($row = $p_res->fetch_assoc()) {
        $poster_file = $row["poster"];
        if (!empty($poster_file) && file_exists("../uploads/" . $poster_file)) {
            unlink("../uploads/" . $poster_file);
        }
    }
    $p_stmt->close();
}

// 2. Delete event record from MySQL using Prepared Statement
$stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");

if ($stmt) {
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: ../pages/events_manage.php?msg=deleted");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: ../pages/events_manage.php?error=failed");
        exit();
    }
} else {
    $conn->close();
    header("Location: ../pages/events_manage.php?error=failed");
    exit();
}
?>
