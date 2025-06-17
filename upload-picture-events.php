<?php
require 'database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['event_code'])) {
        $message = "Event code is required.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $message = "Please select a valid image file.";
    } else {
        $event_code = $_POST['event_code'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($file_type, $allowed_types)) {
            $message = "Only JPG, PNG, and GIF files are allowed.";
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('event_', true) . '.' . $ext;
            $upload_dir = 'assets/img/event/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $stmt = $conn->prepare("UPDATE event_details SET picture = ? WHERE event_code = ?");
                $stmt->bind_param("ss", $destination, $event_code);

                if ($stmt->execute()) {
                    $message = "Image uploaded and database updated successfully.";
                } else {
                    $message = "Database update failed: " . $stmt->error;
                    unlink($destination);
                }
                $stmt->close();
            } else {
                $message = "Failed to move uploaded file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Upload Event Picture</title>
</head>
<body>
  <h1>Upload Picture for Event</h1>

  <?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form action="upload-picture-events.php" method="post" enctype="multipart/form-data">
    <label for="event_code">Event Code:</label><br />
    <input type="text" id="event_code" name="event_code" required /><br /><br />

    <label for="image">Select image to upload:</label><br />
    <input type="file" name="image" id="image" accept="image/*" required /><br /><br />

    <input type="submit" value="Upload Image" />
  </form>
</body>
</html>
