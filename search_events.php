<?php
require 'database.php';

// Get filter parameters
$location = isset($_POST['location']) ? $conn->real_escape_string($_POST['location']) : '';
$date = isset($_POST['date']) ? $conn->real_escape_string($_POST['date']) : '';

// Build query
$query = "SELECT * FROM events WHERE 1=1";
if (!empty($location)) {
    $query .= " AND location = '$location'";
}
if (!empty($date)) {
    $query .= " AND DATE_FORMAT(event_date, '%Y-%m') = '$date'";
}
$query .= " ORDER BY event_date, event_time";

$result = $conn->query($query);
$events = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Output results
if (!empty($events)) {
    foreach ($events as $row) {
        $day = date('d', strtotime($row['event_date']));
        $monthYear = date('M Y', strtotime($row['event_date']));
        $time = date('h:ia', strtotime($row['event_time']));
        $detailLink = "event-details.php?event_code=" . urlencode($row['event_code']);
        ?>
        <div class="event-list-items">
            <div class="event-content">
                <div class="content">
                    <div class="date">
                        <h2><?= $day ?></h2>
                        <span><?= $monthYear ?></span>
                    </div>
                    <div class="title-text">
                        <h4><a href="<?= $detailLink ?>"><?= htmlspecialchars($row['event_name']) ?></a></h4>
                        <ul class="post-time">
                            <li><i class="far fa-map-marker-alt"></i> <?= htmlspecialchars($row['location']) ?></li>
                            <li><i class="far fa-clock"></i> <?= $time ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="event-image">
                <?php if (!empty($row['small_picture'])): ?>
                    <img src="<?= htmlspecialchars($row['small_picture']) ?>" alt="Event Image">
                <?php endif; ?>
            </div>
            <div class="event-btn"> 
                <a href="<?= $detailLink ?>" class="theme-btn">View More</a>
            </div>
        </div>
        <?php
    }
} else {
    echo '<p>No events found.</p>';
}
?>