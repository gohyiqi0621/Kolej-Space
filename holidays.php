<?php
header('Content-Type: application/json');
require_once 'database.php';

try {
    $query = "SELECT id, title, start_date, end_date, category FROM holidays";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception("Query failed: " . mysqli_error($conn));
    }

    $holidays = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $holiday = [
            'id' => $row['id'],
            'title' => $row['title'],
            'start' => $row['start_date'],
            'color' => '#d4af37' // Default color; will be overridden by calendar.js
        ];
        if (!empty($row['end_date']) && $row['end_date'] !== $row['start_date']) {
            $holiday['end'] = $row['end_date'];
        }
        $holiday['category'] = $row['category']; // Add category to the event data
        $holidays[] = $holiday;
    }

    mysqli_free_result($result);
    echo json_encode(['status' => 'success', 'events' => $holidays]);
} catch (Exception $e) {
    error_log("Error in holidays.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    mysqli_close($conn);
}
?>