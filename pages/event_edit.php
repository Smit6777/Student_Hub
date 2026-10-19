<?php
require_once "../php/db_connect_mysqli.php";

$id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    header("Location: events_manage.php?error=not_found");
    exit();
}

$stmt = $conn->prepare("SELECT event_id, title, event_date, location, category, poster, description FROM events WHERE event_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res   = $stmt->get_result();
$event = $res->fetch_assoc();
$stmt->close();
$conn->close();

if (!$event) {
    header("Location: events_manage.php?error=not_found");
    exit();
}

$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event | StudentHub</title>
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

        .poster-preview-box {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f8fafc;
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-bottom: 10px;
        }

        .preview-img {
            width: 90px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }

        .file-note {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
            display: block;
        }

        .btn-submit {
            width: 100%;
            background: #d97706;
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
            background: #b45309;
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
        <h1>Edit Event Details</h1>
        <p>Update campus event schedules, venue, and poster image.</p>
    </header>

    <main>
        <div class="form-container">
            <h2>✏️ Edit Event #<?php echo htmlspecialchars($event["event_id"]); ?></h2>
            <p class="subtitle">Modify the fields below. You can also replace the existing poster image.</p>

            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <?php 
                        if ($error === "file_type") echo "Invalid image format. Only JPG, PNG, and WebP are allowed.";
                        elseif ($error === "file_size") echo "The selected poster file is too large. Maximum size is 2MB.";
                        elseif ($error === "empty") echo "Please fill in all required fields.";
                        elseif ($error === "upload_failed") echo "Failed to save the new poster file.";
                        else echo "An error occurred while updating the event.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="../php/event_edit_process.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['event_id']); ?>">
                <input type="hidden" name="current_poster" value="<?php echo htmlspecialchars($event['poster'] ?? ''); ?>">

                <div class="form-group">
                    <label for="title">Event Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="Workshop" <?php echo ($event['category'] === 'Workshop') ? 'selected' : ''; ?>>Workshop</option>
                            <option value="Seminar" <?php echo ($event['category'] === 'Seminar') ? 'selected' : ''; ?>>Seminar</option>
                            <option value="Hackathon" <?php echo ($event['category'] === 'Hackathon') ? 'selected' : ''; ?>>Hackathon</option>
                            <option value="Cultural" <?php echo ($event['category'] === 'Cultural') ? 'selected' : ''; ?>>Cultural</option>
                            <option value="Sports" <?php echo ($event['category'] === 'Sports') ? 'selected' : ''; ?>>Sports</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="event_date">Event Date</label>
                        <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location / Venue</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Event Description</label>
                    <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
                </div>

                <!-- Current Poster Preview -->
                <div class="form-group">
                    <label>Current Event Poster</label>
                    <?php if (!empty($event["poster"]) && file_exists("../uploads/" . $event["poster"])): ?>
                        <div class="poster-preview-box">
                            <img src="../uploads/<?php echo htmlspecialchars($event['poster']); ?>" alt="Current Poster" class="preview-img">
                            <span style="font-size: 13px; color: #475569;">
                                File: <strong><?php echo htmlspecialchars($event['poster']); ?></strong>
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="poster-preview-box">
                            <span style="font-size: 13px; color: #64748b;">No poster image currently uploaded.</span>
                        </div>
                    <?php endif; ?>

                    <label for="poster" style="margin-top: 10px;">Change Poster (Optional)</label>
                    <input type="file" id="poster" name="poster" accept=".jpg, .jpeg, .png, .webp">
                    <small class="file-note">Leave empty to keep the current poster. Allowed: JPG, PNG, WebP (Max 2MB)</small>
                </div>

                <button type="submit" class="btn-submit">💾 Update Event Details</button>
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
