<?php
session_start();
require_once 'db_connect.php';

$selectedDate = $_GET['date'] ?? '';

if (empty($selectedDate)) {
    echo '<p>No date selected.</p>';
    exit;
}

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
    ORDER BY b.bookingid DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $selectedDate);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
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
    echo '<div class="transaction-group"><p style="grid-column: span 6;">No transactions found for the selected date.</p></div>';
}
$stmt->close();
?>
