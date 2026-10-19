<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../php/db_connect_mysqli.php";
require_once __DIR__ . "/../php/audit_logger.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {

    if (isset($_GET["type"]) && $_GET["type"] === "courses") {
        $courses = [];
        $res = $conn->query("SELECT course_id, course_name FROM courses ORDER BY course_name ASC");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $courses[] = $row;
            }
        }
        echo json_encode(["status" => "success", "data" => $courses]);
        $conn->close();
        exit();
    }

    if (isset($_GET["id"])) {
        $id = filter_var($_GET["id"], FILTER_VALIDATE_INT);
        if (!$id || $id <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid student ID"]);
            $conn->close();
            exit();
        }

        $stmt = $conn->prepare("SELECT s.id, s.name, s.email, s.mobile, s.course_id, c.course_name, s.year, s.gender 
                                FROM students s 
                                LEFT JOIN courses c ON s.course_id = c.course_id 
                                WHERE s.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $student = $res->fetch_assoc();
        $stmt->close();
        $conn->close();

        if ($student) {
            echo json_encode(["status" => "success", "data" => $student]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Student not found"]);
        }
        exit();
    }

    $sql = "SELECT s.id, s.name, s.email, s.mobile, s.course_id, c.course_name, s.year, s.gender 
            FROM students s 
            LEFT JOIN courses c ON s.course_id = c.course_id 
            ORDER BY s.id DESC";
    $res = $conn->query($sql);

    $students = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $students[] = $row;
        }
    }
    $conn->close();

    echo json_encode([
        "status" => "success",
        "count"  => count($students),
        "data"   => $students
    ]);
    exit();
}

if ($method === "POST") {

    $input = $_POST;
    if (empty($input)) {
        $raw = file_get_contents("php://input");
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $input = $decoded;
        }
    }

    $action = $input["action"] ?? $_GET["action"] ?? "";

    if ($action === "add") {
        $name      = trim($input["name"] ?? "");
        $email     = trim($input["email"] ?? "");
        $mobile    = trim($input["mobile"] ?? "");
        $course_id = filter_var($input["course_id"] ?? null, FILTER_VALIDATE_INT);
        $year      = filter_var($input["year"] ?? null, FILTER_VALIDATE_INT);
        $gender    = trim($input["gender"] ?? "");

        if (empty($name) || empty($email) || empty($mobile) || !$course_id || !$year || empty($gender)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            $conn->close();
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid email address format"]);
            $conn->close();
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO students (name, email, mobile, course_id, year, gender) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $name, $email, $mobile, $course_id, $year, $gender);

        if ($stmt->execute()) {
            $new_id = $conn->insert_id;
            log_audit($conn, "API Add Student", "Created student: " . $name . " (ID: " . $new_id . ")");
            $stmt->close();
            $conn->close();
            echo json_encode([
                "status"  => "success",
                "message" => "Student record added successfully",
                "id"      => $new_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to insert student record"]);
            $stmt->close();
            $conn->close();
        }
        exit();
    }

    if ($action === "update") {
        $id        = filter_var($input["id"] ?? null, FILTER_VALIDATE_INT);
        $name      = trim($input["name"] ?? "");
        $email     = trim($input["email"] ?? "");
        $mobile    = trim($input["mobile"] ?? "");
        $course_id = filter_var($input["course_id"] ?? null, FILTER_VALIDATE_INT);
        $year      = filter_var($input["year"] ?? null, FILTER_VALIDATE_INT);
        $gender    = trim($input["gender"] ?? "");

        if (!$id || empty($name) || empty($email) || empty($mobile) || !$course_id || !$year || empty($gender)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            $conn->close();
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid email address format"]);
            $conn->close();
            exit();
        }

        $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, mobile = ?, course_id = ?, year = ?, gender = ? WHERE id = ?");
        $stmt->bind_param("sssissi", $name, $email, $mobile, $course_id, $year, $gender, $id);

        if ($stmt->execute()) {
            log_audit($conn, "API Update Student", "Updated student: " . $name . " (ID: " . $id . ")");
            $stmt->close();
            $conn->close();
            echo json_encode([
                "status"  => "success",
                "message" => "Student details updated successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to update student record"]);
            $stmt->close();
            $conn->close();
        }
        exit();
    }

    if ($action === "delete") {
        $id = filter_var($input["id"] ?? null, FILTER_VALIDATE_INT);

        if (!$id || $id <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid student ID"]);
            $conn->close();
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            log_audit($conn, "API Delete Student", "Deleted student ID: " . $id);
            $stmt->close();
            $conn->close();
            echo json_encode([
                "status"  => "success",
                "message" => "Student record deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to delete student record"]);
            $stmt->close();
            $conn->close();
        }
        exit();
    }

    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid action specified"]);
    $conn->close();
    exit();
}

http_response_code(405);
echo json_encode(["status" => "error", "message" => "Method not allowed"]);
$conn->close();
?>
