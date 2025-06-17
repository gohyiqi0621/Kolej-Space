<?php
include 'database.php';

if (!isset($_GET['id'])) {
    die("Memo ID not provided.");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM memos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Memo not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kolej Space | Memo</title>
    <link rel="shortcut icon" href="../../assets/img/kolejspace.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Georgia', serif;
        }
        .memo-paper {
            background: white;
            padding: 40px;
            margin: 30px auto;
            border: 1px solid #000;
            max-width: 800px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .memo-title {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 30px;
        }
        .memo-header-table td {
            padding: 4px 8px;
            vertical-align: top;
            font-weight: bold;
        }
        .memo-header-table td:nth-child(2) {
            font-weight: normal;
        }
        hr.thick {
            border: 2px solid black;
            margin-top: 20px;
        }
        .memo-body {
            margin-top: 20px;
            line-height: 1.7;
        }
        .memo-footer {
            margin-top: 40px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="memo-paper">
    <div class="text-center mb-4">
        <img src="../../assets/img/kolejspace.png" alt="Logo" height="80">
    </div>
    <div class="memo-title">MEMO</div>

    <table class="memo-header-table w-100 mb-3">
        <tr><td>TO</td><td>: <?= htmlspecialchars($row['recipient']) ?></td></tr>
        <tr><td>FROM</td><td>: <?= htmlspecialchars($row['sender']) ?> (<?= htmlspecialchars($row['sender_position']) ?>)</td></tr>
        <tr><td>DATE</td><td>: <?= date('j F Y', strtotime($row['created_at'])) ?></td></tr>
        <tr><td>SUBJECT</td><td>: <?= htmlspecialchars($row['title']) ?></td></tr>
    </table>

    <hr class="thick">

    <div class="memo-body">
        <?= nl2br(htmlspecialchars($row['description'])) ?>
    </div>

    <div class="memo-footer">
        <?= htmlspecialchars($row['sender']) ?><br>
        KOLEJ SPACE
    </div>
</div>

</body>
</html>
