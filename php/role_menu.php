<?php
function get_role_menu() {
    $role = $_SESSION["user_role"] ?? "";

    if ($role === "admin") {
        return [
            ["title" => "Home", "link" => "index.html"],
            ["title" => "Admin Panel", "link" => "admin.php"],
            ["title" => "Students Directory", "link" => "students.php"],
            ["title" => "Live Directory", "link" => "students_ajax.php"],
            ["title" => "Manage Events", "link" => "events_manage.php"],
            ["title" => "Logout", "link" => "../php/logout.php", "special" => "logout"]
        ];
    } elseif ($role === "student") {
        return [
            ["title" => "Home", "link" => "index.html"],
            ["title" => "My Dashboard", "link" => "dashboard.php"],
            ["title" => "Browse Events", "link" => "eventshub.html"],
            ["title" => "Feedback", "link" => "feedback.html"],
            ["title" => "Logout", "link" => "../php/logout.php", "special" => "logout"]
        ];
    } else {
        return [
            ["title" => "Home", "link" => "index.html"],
            ["title" => "Events", "link" => "eventshub.html"],
            ["title" => "Login", "link" => "login.php"],
            ["title" => "Register", "link" => "register_user.html"]
        ];
    }
}
?>
