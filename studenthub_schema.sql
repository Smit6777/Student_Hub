CREATE DATABASE IF NOT EXISTS studenthub;

USE studenthub;

CREATE TABLE IF NOT EXISTS courses (
    course_id INT(11) NOT NULL AUTO_INCREMENT,
    course_name VARCHAR(100) NOT NULL,
    PRIMARY KEY (course_id)
);

INSERT INTO courses (course_name)
VALUES
('Information Technology'),
('Computer Science'),
('Computer Engineering');

CREATE TABLE IF NOT EXISTS students (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    course_id INT(11),
    year INT(11),
    gender VARCHAR(20),
    PRIMARY KEY (id),
    FOREIGN KEY (course_id)
        REFERENCES courses(course_id)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT
);

INSERT INTO students (id, name, email, mobile, course_id, year, gender) VALUES
(1, 'Smit Makwana', 'smit.makwana@studenthub.com', '9998403012', 1, 2, 'Male'),
(2, 'Priya Patel', 'priya.patel@example.com', '9876543210', 2, 3, 'Female'),
(3, 'Rahul Shah', 'rahul.shah@example.com', '9898123456', 3, 1, 'Male'),
(4, 'Ananya Sharma', 'ananya.s@example.com', '9723456789', 1, 4, 'Female'),
(5, 'Harsh Mehta', 'harsh.mehta@example.com', '9638527410', 2, 2, 'Male')
ON DUPLICATE KEY UPDATE name=VALUES(name);

CREATE TABLE IF NOT EXISTS events (
    event_id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    poster VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    PRIMARY KEY (event_id)
);

INSERT INTO events (title, event_date, location, category) VALUES 
('Web Development Workshop', '2026-08-15', 'Computer Lab', 'Workshop'),
('AI and Machine Learning Seminar', '2026-08-20', 'Seminar Hall', 'Seminar'),
('Hackathon 2026', '2026-08-25', 'Innovation Center', 'Hackathon');

CREATE TABLE IF NOT EXISTS event_registrations (
    reg_id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    event_id INT(11) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (reg_id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'student',
    remember_token VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

INSERT INTO users (id, name, email, password, role) VALUES 
(1, 'Admin User', 'admin@studenthub.com', '$2y$10$4GgTmP7MZY6Exln3VQFsjuN2tNAmwqDZx5Q//DyZ/dOriykOKQTGS', 'admin'),
(2, 'Smit Makwana', 'student@studenthub.com', '$2y$10$3uICpgQOpanhyo2P2UM0fe7gK0QiVu/T26z3XS7NRtfVOUpaWEyWi', 'student')
ON DUPLICATE KEY UPDATE role = VALUES(role);

CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) DEFAULT NULL,
    user_name VARCHAR(100) DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (log_id)
);

