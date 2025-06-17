<?php
// Include database connection
include 'database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Course Cover Image</title>
    <!-- Include Bootstrap for styling (optional) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Upload Course Cover Image</h2>
        <form action="upload_handler.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="course_code">Course Code</label>
                <input type="text" name="course_code" id="course_code" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="course_cover_pic">Select Image</label>
                <input type="file" name="course_cover_pic" id="course_cover_pic" class="form-control-file" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</body>
</html>