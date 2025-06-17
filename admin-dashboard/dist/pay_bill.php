<?php
session_start();

if (!isset($_SESSION['matric_no'])) {
    header("Location: sign-in.html?error=Please log in to view your academic results");
    exit();
}

include 'database.php';

$matricNo = $_SESSION['matric_no'];

// Step 1: Get student_id from users table
$studentResult = $conn->query("SELECT id FROM users WHERE matric_no = '$matricNo'");
if ($studentResult->num_rows > 0) {
    $studentRow = $studentResult->fetch_assoc();
    $student_id = $studentRow['id'];
} else {
    die("Student not found.");
}

// Step 2: Now fetch finance data using the student_id
$query = "SELECT * FROM finance WHERE student_id = $student_id ORDER BY semester ASC, id ASC";
$result = $conn->query($query);

$total_debit = 0;
$total_credit = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Finance</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="mb-4">Student Payment Dashboard</h2>

  <table class="table table-bordered table-hover bg-white">
    <thead class="table-dark">
      <tr>
        <th>Semester</th>
        <th>Date</th>
        <th>Description</th>
        <th>Receipt No</th>
        <th class="text-end">Debit (RM)</th>
        <th class="text-end">Credit (RM)</th>
      </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['semester'] ?></td>
        <td><?= $row['date'] ?? '-' ?></td>
        <td><?= $row['description'] ?></td>
        <td><?= $row['receipt_no'] ?? '-' ?></td>
        <td class="text-end"><?= $row['debit'] ? number_format($row['debit'], 2) : '-' ?></td>
        <td class="text-end"><?= $row['credit'] ? number_format($row['credit'], 2) : '-' ?></td>
      </tr>
      <?php
        $total_debit += $row['debit'] ?? 0;
        $total_credit += $row['credit'] ?? 0;
      ?>
    <?php endwhile; ?>
    </tbody>
    <tfoot>
      <tr class="fw-bold">
        <td colspan="4" class="text-end">Total</td>
        <td class="text-end text-danger"><?= number_format($total_debit, 2) ?></td>
        <td class="text-end text-success"><?= number_format($total_credit, 2) ?></td>
      </tr>
      <tr class="fw-bold table-warning">
        <td colspan="4" class="text-end">Outstanding Balance</td>
        <td colspan="2" class="text-end text-primary"><?= number_format($total_debit - $total_credit, 2) ?></td>
      </tr>
    </tfoot>
  </table>

  <?php $outstanding = $total_debit - $total_credit; ?>
  <?php if ($outstanding > 0): ?>
    <form action="create-checkout-session.php" method="POST">
      <input type="hidden" name="amount" value="<?= $outstanding ?>">
      <button type="submit" class="btn btn-success btn-lg">Pay RM <?= number_format($outstanding, 2) ?> with Stripe</button>
    </form>
  <?php else: ?>
    <div class="alert alert-success">All dues are paid. No outstanding balance.</div>
  <?php endif; ?>

</div>

</body>
</html>
