<?php
function log_audit($conn, $action, $details = "") {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $user_id   = $_SESSION["user_id"] ?? null;
    $user_name = $_SESSION["user_name"] ?? "System";
    $ip        = $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1";

    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, user_name, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("issss", $user_id, $user_name, $action, $details, $ip);
        $stmt->execute();
        $stmt->close();
    }
}
?>
