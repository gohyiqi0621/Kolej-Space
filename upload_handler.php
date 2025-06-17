<?php
// Include database connection
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_code = $_POST['course_code'];
    $target_dir = "uploads/"; // Directory to store images
    $file_name = basename($_FILES["course_cover_pic"]["name"]);
    $unique_name = uniqid() . "_" . $file_name; // Prevent name conflicts
    $target_file = $target_dir . $unique_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_type, $allowed_types)) {
        die("Error: Only JPG, JPEG, PNG, and GIF files are allowed.");
    }
    if ($_FILES["course_cover_pic"]["size"] > 5000000) { // 5MB limit
        die("Error: File is too large (max 5MB).");
    }

    // Check if course_code exists
    $sql = "SELECT course_code FROM courses WHERE course_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $course_code);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $stmt->close();
        die("Error: Course code '$course_code' does not exist.");
    }
    $stmt->close();

    // Move uploaded file to server
    if (move_uploaded_file($_FILES["course_cover_pic"]["tmp_name"], $target_file)) {
        // Update database with file path
        $sql = "UPDATE courses SET course_cover_pic = ? WHERE course_code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $target_file, $course_code);
        if ($stmt->execute()) {
            echo "Image uploaded and path saved successfully for course '$course_code'.";
            // Redirect to course display page (optional)
            header("Location: courses.php");
        } else {
            echo "Error saving path to database: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
}

$conn->close();
?>