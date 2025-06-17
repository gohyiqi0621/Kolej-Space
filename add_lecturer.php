<?php
require 'database.php'; // Your DB connection

$success = '';
$error = '';
$lecturers = [];
$selectedLecturerId = $_POST['lecturer_id'] ?? $_GET['id'] ?? '';

$result = $conn->query("SELECT id, lecturer_name, picture FROM lecturers ORDER BY lecturer_name ASC");
while ($row = $result->fetch_assoc()) {
    $lecturers[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lecturer_id'])) {
    $selectedLecturerId = (int)$_POST['lecturer_id'];

    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {
        $targetDir = "uploads/lecturers/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($_FILES['picture']['name']);
        $targetFile = $targetDir . time() . '_' . $fileName;
        $fileType = mime_content_type($_FILES['picture']['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFile)) {
                $stmt = $conn->prepare("UPDATE lecturers SET picture = ? WHERE id = ?");
                $stmt->bind_param("si", $targetFile, $selectedLecturerId);

                if ($stmt->execute()) {
                    $success = "Picture uploaded successfully.";
                } else {
                    $error = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, and GIF allowed.";
        }
    } else {
        $error = "No picture uploaded or upload error.";
    }
}

// Get selected lecturer's current picture if available
$currentPicture = '';
if ($selectedLecturerId) {
    $stmt = $conn->prepare("SELECT picture FROM lecturers WHERE id = ?");
    $stmt->bind_param("i", $selectedLecturerId);
    $stmt->execute();
    $stmt->bind_result($currentPicture);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Lecturer Picture</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        select, input, button { width: 100%; padding: 10px; margin-top: 10px; }
        .success { color: green; text-align: center; }
        .error { color: red; text-align: center; }
        .preview { text-align: center; margin-top: 15px; }
        .preview img { max-width: 200px; border-radius: 8px; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Upload Lecturer Picture</h2>

<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Select Lecturer:</label>
    <select name="lecturer_id" required onchange="this.form.submit()">
        <option value="">-- Choose Lecturer --</option>
        <?php foreach ($lecturers as $lect): ?>
            <option value="<?= $lect['id'] ?>" <?= ($lect['id'] == $selectedLecturerId) ? 'selected' : '' ?>>
                <?= htmlspecialchars($lect['lecturer_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if ($selectedLecturerId): ?>
        <?php if ($currentPicture): ?>
            <div class="preview">
                <p>Current Picture:</p>
                <img src="<?= htmlspecialchars($currentPicture) ?>" alt="Lecturer Picture">
            </div>
        <?php else: ?>
            <div class="preview">
                <p>No picture uploaded yet.</p>
            </div>
        <?php endif; ?>

        <label>Upload New Picture:</label>
        <input type="file" name="picture" accept="image/*" required>
        <button type="submit">Upload</button>
    <?php endif; ?>
</form>

</body>
</html>
