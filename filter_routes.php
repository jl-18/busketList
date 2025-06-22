<?php
session_start();
require_once 'db_connect.php';

$selectedDate = $_GET['date'] ?? '';

if (empty($selectedDate)) {
    echo "No date selected.";
    exit;
}

// Prepare the query to filter based on sched_date from schedmatrix
$query = "
    SELECT r.origin, r.destination, COUNT(*) AS frequency
    FROM booking b
    JOIN schedmatrix s ON b.schedid = s.schedid
    JOIN routes r ON s.routeid = r.routeid
    WHERE s.scheddate = ?
    GROUP BY r.origin, r.destination
    ORDER BY frequency DESC
    LIMIT 3
";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $selectedDate);
$stmt->execute();
$result = $stmt->get_result();

$topRoutes = [];
while ($row = $result->fetch_assoc()) {
    $topRoutes[] = $row;
}

// Build the HTML (similar to your original structure)
if (count($topRoutes) > 0) {
    echo '<div class="route-group-title">';
    echo '<p>ROUTES</p><p>FREQUENCY</p>';
    echo '</div>';

    foreach ($topRoutes as $route) {
        echo '<div class="route-group">';
        echo '<p>' . strtoupper($route['origin']) . " to " . strtoupper($route['destination']) . '</p>';
        echo '<p>' . $route['frequency'] . '</p>';
        echo '</div>';
    }
} else {
    echo '<div class="route-group"><p colspan="2">No data available for selected date.</p></div>';
}
?>
