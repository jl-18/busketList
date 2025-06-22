<?php
session_start();
require_once 'db_connect.php';

// Grab and trim GET parameters.
$date = trim($_GET['date'] ?? '');
$bookingID = trim($_GET['booking_id'] ?? '');

// If both are empty, return a message.
if(empty($date) && empty($bookingID)){
    echo '<div class="transaction-group"><p>Please enter a Booking ID or choose a date.</p></div>';
    exit;
}

// Base query: start with a condition that is always true.
$query = "
    SELECT 
        b.bookingid,
        CONCAT(p.firstname, ' ', p.lastname) AS passenger,
        r.origin,
        r.destination,
        bt.description AS bustype,
        i.grandtotal AS payment
    FROM booking b
    JOIN passenger p ON b.passengerid = p.passengerid
    JOIN routes r ON b.routeid = r.routeid
    JOIN schedmatrix s ON b.schedid = s.schedid
    JOIN bus bs ON s.busid = bs.busid
    JOIN bustype bt ON bs.bustypeid = bt.bustypeid
    JOIN invoice i ON b.bookingid = i.bookingid
    WHERE 1=1
";

// Prepare arrays for binding parameters.
$params = [];
$types = "";

// If a date is provided, add the date condition.
if (!empty($date)) {
    $query .= " AND b.bookingdate = ? ";
    $types .= "s";
    $params[] = $date;
}

// If a booking ID is provided, add the booking ID condition.
// Casting the booking ID to integer if it's numeric.
if (!empty($bookingID)) {
    $bookingID = intval($bookingID);
    $query .= " AND b.bookingid = ? ";
    $types .= "i"; 
    $params[] = $bookingID;
}

$query .= " ORDER BY b.bookingid DESC";

// Debug: you can uncomment the following line to see the final query
// error_log("SQL Query: " . $query);

// Prepare and bind parameters.
$stmt = $conn->prepare($query);
if ($stmt === false) {
    echo "Error preparing statement: " . $conn->error;
    exit;
}

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()){
        echo '<div class="transaction-group">';
        echo '<p>' . htmlspecialchars($row['bookingid']) . '</p>';
        echo '<p>' . htmlspecialchars($row['passenger']) . '</p>';
        echo '<p>' . htmlspecialchars($row['origin']) . '</p>';
        echo '<p>' . htmlspecialchars($row['destination']) . '</p>';
        echo '<p>' . htmlspecialchars($row['bustype']) . '</p>';
        echo '<p>₱' . number_format($row['payment'], 2) . '</p>';
        echo '</div>';
    }
} else {
    echo '<div class="transaction-group"><p>No bookings found for the provided criteria.</p></div>';
}

$stmt->close();
?>
