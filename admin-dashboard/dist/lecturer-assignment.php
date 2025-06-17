<?php
session_start();
include 'database.php';

/*
// Only allow lecturers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $courses = $_POST['course_codes'] ?? [];
    $semester = $_POST['semester'];
    $due_date = $_POST['due_date'];
    $created_by = $_SESSION['id'];
    $filePath = null;

    // Handle optional file upload
    $target_dir = __DIR__ . "/uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['file']['name']);
        $safe_name = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $filename);
        $target_file = $target_dir . time() . "_" . $safe_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $filePath = "uploads/" . basename($target_file);
        } else {
            echo "<div class='alert alert-danger'>Failed to upload file.</div>";
        }
    }

    // Insert one record per course code
    $stmt = $conn->prepare("INSERT INTO assignments (title, description, file_path, due_date, course_code, semester, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($courses as $course_code) {
        $stmt->bind_param("ssssssi", $title, $desc, $filePath, $due_date, $course_code, $semester, $created_by);
        $stmt->execute();
    }

    echo "<script>alert('Assignments added successfully.'); window.location.href = window.location.href;</script>";
    exit();
}

// Fetch available courses
$courses_result = $conn->query("SELECT course_code, course_name FROM courses ORDER BY course_code");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Homework</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h3 class="mb-4">Create Assignment</h3>
    <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Course Codes</label>
            <select name="course_codes[]" class="form-select" multiple required>
                <?php while ($row = $courses_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['course_code']) ?>">
                        <?= htmlspecialchars($row['course_code'] . ' - ' . $row['course_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <div class="form-text">Hold Ctrl (Windows) or Cmd (Mac) to select multiple courses.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Semester</label>
            <input type="number" name="semester" min="1" max="7" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Attach File (optional)</label>
            <input type="file" name="file" class="form-control">
        </div>
        <button class="btn btn-success" type="submit">Create Assignment</button>
    </form>
</div>
</body>
</html>
