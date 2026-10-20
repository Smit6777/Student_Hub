<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Directory | StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            padding: 25px 30px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .page-title h2 {
            color: #1d2b42;
            font-size: 26px;
            margin-bottom: 4px;
        }

        .page-title p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }

        .btn-add {
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            box-shadow: 0 4px 12px rgba(37,99,235,0.25);
        }

        .btn-add:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        /* Loading, Success, Error States */
        #alert-container {
            margin-bottom: 20px;
            display: none;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .loading-state {
            text-align: center;
            padding: 40px;
            font-size: 16px;
            color: #2563eb;
            font-weight: 600;
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }

        /* Table Card */
        .table-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .table-meta {
            padding: 16px 24px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #64748b;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f8fafc;
            color: #334155;
            padding: 14px 18px;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .badge-course {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-year {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            background: #f1f5f9;
            color: #475569;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-edit:hover {
            background: #fde68a;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-delete:hover {
            background: #fecaca;
        }

        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 550px;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
        }

        .modal-header h3 {
            color: #1d2b42;
            font-size: 20px;
            margin: 0;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 20px;
            color: #94a3b8;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
            font-size: 13px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 25px;
        }

        .btn-save {
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
            padding: 10px 18px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
    <script>
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.classList.add('dark-theme');
        }
    </script>
</head>
<body>

    <header>
        <img src="../image/image-removebg-preview.png" alt="StudentHub Logo" width="100">
        <h1>Student Directory</h1>
        <p>Manage and update student records in real time.</p>
    </header>

    <nav>
        <button class="hamburger" aria-label="Toggle menu">☰</button>
        <div id="nav-links">
            <a href="index.html">Home</a>
            <a href="students_ajax.php" style="font-weight: 700; color: #2563eb;">Live Directory</a>
            <a href="students.php">Students</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="events.html">Events</a>
            <a href="contact.html">Contact</a>
            <button id="theme-toggle" style="margin-left: 10px; padding: 8px 14px; font-weight: 600;">🌙 Dark Mode</button>
        </div>
    </nav>

    <div class="container">

        <div id="alert-container"></div>

        <div class="page-header">
            <div class="page-title">
                <h2>👥 Student Directory</h2>
                <p>View, update, or remove student profiles.</p>
            </div>
            <div>
                <button class="btn-add" onclick="openAddModal()">➕ Add Student</button>
            </div>
        </div>

        <div id="loading" class="loading-state">
            ⏳ Loading student records...
        </div>

        <!-- Dynamic Table Card -->
        <div id="table-card" class="table-card" style="display: none;">
            <div class="table-meta">
                <span id="record-count">Showing 0 registered students</span>
                <span style="color: #16a34a; font-weight: 600;">● Live API Connected</span>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Email Address</th>
                            <th>Mobile</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Gender</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="student-table-body">
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div id="student-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Add New Student</h3>
                <button class="btn-close" onclick="closeModal()">&times;</button>
            </div>

            <form id="student-form" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="student_id" name="id" value="">
                <input type="hidden" id="form_action" name="action" value="add">

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="e.g. Smit Makwana" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="e.g. user@example.com" required>
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number (10 digits)</label>
                    <input type="text" id="mobile" name="mobile" placeholder="e.g. 9998403012" pattern="[0-9]{10}" required>
                </div>

                <div class="form-group">
                    <label for="course_id">Course</label>
                    <select id="course_id" name="course_id" required>
                        <option value="">Select Course</option>
                        <!-- Populated dynamically from API -->
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select id="year" name="year" required>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save" id="btn-save">Save Student</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 StudentHub | Designed by Smit Makwana</p>
    </footer>

    <script>
        const API_URL = "../api/students_api.php";
        let coursesCache = [];

        document.addEventListener("DOMContentLoaded", () => {
            loadCourses();
            loadStudents();
        });

        function showAlert(message, type = "success") {
            const container = document.getElementById("alert-container");
            const alertClass = (type === "success") ? "alert-success" : "alert-danger";
            const icon = (type === "success") ? "✅" : "❌";

            container.innerHTML = `<div class="alert ${alertClass}">${icon} ${message}</div>`;
            container.style.display = "block";

            setTimeout(() => {
                container.style.display = "none";
            }, 4000);
        }

        async function loadCourses() {
            try {
                const response = await fetch(`${API_URL}?type=courses`);
                const result = await response.json();
                if (result.status === "success") {
                    coursesCache = result.data;
                    const select = document.getElementById("course_id");
                    select.innerHTML = '<option value="">Select Course</option>' + 
                        coursesCache.map(c => `<option value="${c.course_id}">${c.course_name}</option>`).join('');
                }
            } catch (err) {
                console.error("Failed to load courses:", err);
            }
        }

        async function loadStudents() {
            const loadingElem = document.getElementById("loading");
            const tableCard   = document.getElementById("table-card");
            const tableBody   = document.getElementById("student-table-body");

            loadingElem.style.display = "block";
            tableCard.style.display   = "none";

            try {
                const response = await fetch(API_URL);
                const result   = await response.json();

                loadingElem.style.display = "none";
                tableCard.style.display   = "block";

                if (result.status === "success") {
                    document.getElementById("record-count").textContent = `Showing ${result.count} registered students`;

                    if (result.data.length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:30px; color:#64748b;">No students found. Click "Add Student" to create one.</td></tr>`;
                        return;
                    }

                    tableBody.innerHTML = result.data.map(s => `
                        <tr>
                            <td><strong>#${s.id}</strong></td>
                            <td><strong>${escapeHtml(s.name)}</strong></td>
                            <td>${escapeHtml(s.email)}</td>
                            <td>${escapeHtml(s.mobile)}</td>
                            <td><span class="badge-course">${escapeHtml(s.course_name || 'Not Assigned')}</span></td>
                            <td><span class="badge-year">Year ${s.year}</span></td>
                            <td>${escapeHtml(s.gender)}</td>
                            <td style="text-align: center;">
                                <div class="actions" style="justify-content: center;">
                                    <button class="btn-edit" onclick="openEditModal(${s.id})">✏️ Edit</button>
                                    <button class="btn-delete" onclick="deleteStudent(${s.id}, '${escapeHtml(s.name)}')">🗑️ Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    showAlert(result.message || "Failed to load students", "error");
                }
            } catch (err) {
                loadingElem.style.display = "none";
                showAlert("Network connection error. Could not connect to API.", "error");
            }
        }

        function openAddModal() {
            document.getElementById("student-form").reset();
            document.getElementById("student_id").value = "";
            document.getElementById("form_action").value = "add";
            document.getElementById("modal-title").textContent = "Add New Student";
            document.getElementById("btn-save").textContent = "Save Student";
            document.getElementById("student-modal").style.display = "flex";
        }

        async function openEditModal(id) {
            try {
                const response = await fetch(`${API_URL}?id=${id}`);
                const result   = await response.json();

                if (result.status === "success") {
                    const s = result.data;
                    document.getElementById("student_id").value  = s.id;
                    document.getElementById("form_action").value = "update";
                    document.getElementById("name").value        = s.name;
                    document.getElementById("email").value       = s.email;
                    document.getElementById("mobile").value      = s.mobile;
                    document.getElementById("course_id").value   = s.course_id;
                    document.getElementById("year").value        = s.year;
                    document.getElementById("gender").value      = s.gender;

                    document.getElementById("modal-title").textContent = `Edit Student #${s.id}`;
                    document.getElementById("btn-save").textContent = "Update Student";
                    document.getElementById("student-modal").style.display = "flex";
                } else {
                    showAlert(result.message, "error");
                }
            } catch (err) {
                showAlert("Failed to retrieve student details.", "error");
            }
        }

        function closeModal() {
            document.getElementById("student-modal").style.display = "none";
        }

        async function handleFormSubmit(event) {
            event.preventDefault();
            const form = document.getElementById("student-form");
            const formData = new FormData(form);

            const saveBtn = document.getElementById("btn-save");
            saveBtn.disabled = true;
            saveBtn.textContent = "Saving...";

            try {
                const response = await fetch(API_URL, {
                    method: "POST",
                    body: formData
                });
                const result = await response.json();

                saveBtn.disabled = false;

                if (result.status === "success") {
                    closeModal();
                    showAlert(result.message, "success");
                    loadStudents();
                } else {
                    showAlert(result.message || "Operation failed", "error");
                }
            } catch (err) {
                saveBtn.disabled = false;
                showAlert("Server error during save operation.", "error");
            }
        }

        async function deleteStudent(id, name) {
            if (!confirm(`Are you sure you want to delete ${name}? This action cannot be undone.`)) {
                return;
            }

            const formData = new FormData();
            formData.append("action", "delete");
            formData.append("id", id);

            try {
                const response = await fetch(API_URL, {
                    method: "POST",
                    body: formData
                });
                const result = await response.json();

                if (result.status === "success") {
                    showAlert(result.message, "success");
                    loadStudents();
                } else {
                    showAlert(result.message || "Failed to delete student", "error");
                }
            } catch (err) {
                showAlert("Failed to connect to API to delete student.", "error");
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>"']/g, function(m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
            });
        }
    </script>
</body>
</html>
