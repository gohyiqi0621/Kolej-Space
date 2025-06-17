<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient = $_POST['recipient'];
    $sender = $_POST['sender'];
    $sender_position = $_POST['sender_position'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO memos (recipient, sender, sender_position, title, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $recipient, $sender, $sender_position, $title, $description);
    $stmt->execute();

    echo "<div class='alert alert-success'>Memo uploaded successfully.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Memo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2>Upload Memo</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">To (Recipient)</label>
            <input type="text" name="recipient" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">From (Sender Name)</label>
            <input type="text" name="sender" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Sender Position</label>
            <input type="text" name="sender_position" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description / Content</label>
            <textarea name="description" class="form-control" rows="8" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Upload Memo</button>
    </form>
</body>
</html>
