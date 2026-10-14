<?php
$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event | StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-container {
            max-width: 650px;
            margin: 40px auto;
            background: white;
            padding: 35px 40px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .form-container h2 {
            color: #1d2b42;
            margin-bottom: 6px;
            font-size: 24px;
        }

        .form-container p.subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
            font-size: 14px;
        }

        .form-group input, 
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            transition: 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input:focus, 
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .file-note {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
            display: block;
        }

        .btn-submit {
            width: 100%;
            background: #2563eb;
            color: white;
            padding: 13px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #1d4ed8;
        }

        .btn-back {
            display: inline-block;
            margin-top: 15px;
            text-align: center;
            width: 100%;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-back:hover {
            color: #2563eb;
            text-decoration: underline;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
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
        <h1>Add New Event</h1>
        <p>Publish an upcoming campus event with a custom poster.</p>
    </header>

    <main>
        <div class="form-container">
            <h2>➕ Add Event & Poster</h2>
            <p class="subtitle">Fill out the event schedule and choose a poster image to upload.</p>

            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <?php 
                        if ($error === "file_type") echo "Invalid image format. Only JPG, PNG, and WebP are allowed.";
                        elseif ($error === "file_size") echo "The selected poster file is too large. Maximum size is 2MB.";
                        elseif ($error === "empty") echo "Please fill in all required fields.";
                        elseif ($error === "upload_failed") echo "Failed to save the poster file on the server.";
                        else echo "An error occurred while creating the event.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="../php/event_add_process.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Event Title</label>
                    <input type="text" id="title" name="title" placeholder="e.g. Annual Hackathon 2026" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Hackathon">Hackathon</option>
                            <option value="Cultural">Cultural</option>
                            <option value="Sports">Sports</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="event_date">Event Date</label>
                        <input type="date" id="event_date" name="event_date" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location / Venue</label>
                    <input type="text" id="location" name="location" placeholder="e.g. Innovation Hall, Block 3" required>
                </div>

                <div class="form-group">
                    <label for="description">Event Description</label>
                    <textarea id="description" name="description" rows="3" placeholder="Provide details, eligibility, and workshop schedule..."></textarea>
                </div>

                <!-- Poster File Upload with File Validation Requirements -->
                <div class="form-group">
                    <label for="poster">Upload Event Poster</label>
                    <input type="file" id="poster" name="poster" accept=".jpg, .jpeg, .png, .webp" required>
                    <small class="file-note">Supported formats: JPG, JPEG, PNG, WebP (Maximum file size: 2MB)</small>
                </div>

                <button type="submit" class="btn-submit">🚀 Publish Event</button>
                <a href="events_manage.php" class="btn-back">⬅ Cancel & Return to Events Directory</a>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 StudentHub | Designed by Smit Makwana</p>
    </footer>

    <script src="../javascripts/scripts.js"></script>
</body>
</html>
