<?php
include 'database.php'; // DB connection

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize message variable for feedback
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Normalize course code
    $course_code = strtoupper($_POST['course_code'] ?? '');

    // Validate course code (optional: ensure it exists in courses table)
    $stmt_course_check = $conn->prepare("SELECT course_code FROM courses WHERE course_code = ?");
    $stmt_course_check->bind_param("s", $course_code);
    $stmt_course_check->execute();
    $result_course_check = $stmt_course_check->get_result();
    if ($result_course_check->num_rows === 0) {
        $message = '<div class="alert alert-danger">Error: Course code does not exist in courses table.</div>';
        $stmt_course_check->close();
    } else {
        $stmt_course_check->close();

        // Directory to store uploaded images
        $upload_dir = 'Uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Check if file was uploaded
        if (!isset($_FILES['course_detail_image'])) {
            $message = '<div class="alert alert-danger">Error: No file uploaded. Check form enctype.</div>';
        } elseif ($_FILES['course_detail_image']['error'] !== UPLOAD_ERR_OK) {
            $error_messages = [
                UPLOAD_ERR_INI_SIZE => 'File size exceeds server limit.',
                UPLOAD_ERR_FORM_SIZE => 'File size exceeds form limit.',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload.',
            ];
            $error_code = $_FILES['course_detail_image']['error'];
            $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($error_messages[$error_code] ?? 'Unknown upload error.') . '</div>';
        } else {
            // File details
            $file_tmp = $_FILES['course_detail_image']['tmp_name'];
            $file_name = basename($_FILES['course_detail_image']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Added webp

            // Validate file extension
            if (!in_array($file_ext, $allowed_ext)) {
                $message = '<div class="alert alert-danger">Error: Only JPG, JPEG, PNG, GIF, and WebP files are allowed.</div>';
            } elseif ($_FILES['course_detail_image']['size'] > 5 * 1024 * 1024) {
                $message = '<div class="alert alert-danger">Error: File size exceeds 5MB limit.</div>';
            } elseif (!getimagesize($file_tmp)) {
                $message = '<div class="alert alert-danger">Error: Uploaded file is not a valid image.</div>';
            } else {
                // Generate unique file name
                $new_file_name = $course_code . '_detail_' . time() . '.' . $file_ext;
                $destination = $upload_dir . $new_file_name;

                // Move uploaded file
                if (move_uploaded_file($file_tmp, $destination)) {
                    // Check if course_code exists in course_details
                    $stmt_check = $conn->prepare("SELECT course_code FROM course_details WHERE course_code = ?");
                    $stmt_check->bind_param("s", $course_code);
                    $stmt_check->execute();
                    $result_check = $stmt_check->get_result();

                    if ($result_check->num_rows > 0) {
                        // Update existing record
                        $stmt = $conn->prepare("UPDATE course_details SET picture = ? WHERE course_code = ?");
                        $stmt->bind_param("ss", $new_file_name, $course_code);
                    } else {
                        // Insert new record (adjust fields as needed)
                        $stmt = $conn->prepare("INSERT INTO course_details (course_code, picture) VALUES (?, ?)");
                        $stmt->bind_param("ss", $course_code, $new_file_name);
                    }

                    if ($stmt->execute()) {
                        $message = '<div class="alert alert-success">Image uploaded and saved to database successfully!</div>';
                    } else {
                        $message = '<div class="alert alert-danger">Error updating database: ' . htmlspecialchars($conn->error) . '</div>';
                    }
                    $stmt->close();
                    $stmt_check->close();
                } else {
                    $message = '<div class="alert alert-danger">Error moving uploaded file.</div>';
                }
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Course Detail Image</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>Upload Course Detail Image</h2>
        <?php if (!empty($message)): ?>
            <?php echo $message; ?>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="course_code" class="form-label">Course Code:</label>
                <input type="text" name="course_code" id="course_code" class="form-control" value="DCS" required>
            </div>
            <div class="mb-3">
                <label for="course_detail_image" class="form-label">Select Image:</label>
                <input type="file" name="course_detail_image" id="course_detail_image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Upload Image</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>