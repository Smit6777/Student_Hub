<?php
// This file reads the CSV file and displays the saved records in a table.
$csvFile = __DIR__ . '/../data/registrations.csv';

if (!file_exists($csvFile)) {
    echo "<h2>No Records Found</h2>";
    echo "<p>No registration data has been stored yet.</p>";
    exit;
}

$file = fopen($csvFile, "r");
if ($file === false) {
    echo "<h2>File Error</h2><p>Unable to open the registration file.</p>";
    exit;
}

$header = fgetcsv($file);
$records = array();

while (($row = fgetcsv($file)) !== false) {
    if ($row === array(null) || count($row) < 7) {
        continue;
    }

    $records[] = array(
        "Name" => $row[0],
        "Email" => $row[1],
        "Mobile" => $row[2],
        "Course" => $row[4],
        "Year" => $row[5],
        "Gender" => $row[6]
    );
}

fclose($file);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stored Registrations</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .table-box {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #dfe7f1;
            padding: 10px 12px;
            text-align: left;
        }

        th {
            background: #eaf2ff;
        }
    </style>
</head>
<body>
    <div class="table-box">
        <h2>Student Registrations</h2>

        <?php if (count($records) === 0): ?>
            <p>No student records found.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Gender</th>
                </tr>

                <?php foreach ($records as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['Name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($student['Email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($student['Mobile'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($student['Course'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($student['Year'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($student['Gender'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <p><a href="../pages/register.html">Go Back</a></p>
    </div>
</body>
</html>
