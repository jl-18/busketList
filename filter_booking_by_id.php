<?php
session_start();
require_once 'db_connect.php';

$date = $_GET['date'] ?? '';
$bookingID = $_GET['booking_id'] ?? '';

// Require a date since the booking id is to be filtered only for the inputted date.
if (empty($date)) {
    echo '<p>No date selected.</p>';
    exit;
}

// Base query: filter by booking date.
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
    WHERE b.bookingdate = ?
";

// If a bookingID is provided, add it to the WHERE clause.
if (!empty($bookingID)) {
    $query .= " AND b.bookingid = ?";
}

$query .= " ORDER BY b.bookingid DESC";

// Prepare statement.
$stmt = $conn->prepare($query);

// Bind parameters based on whether bookingID was provided.
if (!empty($bookingID)) {
    $stmt->bind_param('ss', $date, $bookingID);
} else {
    $stmt->bind_param('s', $date);
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
    echo '<div class="transaction-group"><p colspan="6">No bookings found for the provided criteria.</p></div>';
}

$stmt->close();
?>
