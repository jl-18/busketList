<?php
session_start();
require_once 'db_connect.php';

// Check if the booking ID is provided
if (!isset($_GET['bookingid']) || empty($_GET['bookingid'])) {
    echo "No booking ID provided.";
    exit;
}

$bookingid = $_GET['bookingid'];

// Prepare the deletion statement (adjust table/column names if necessary)
$stmt = $conn->prepare("DELETE FROM booking WHERE bookingid = ?");
if (!$stmt) {
    echo "Error preparing the statement: " . $conn->error;
    exit;
}

$stmt->bind_param("s", $bookingid);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Booking with ID " . htmlspecialchars($bookingid) . " was successfully deleted.";
} else {
    echo "No booking found with the provided ID or the booking was already deleted.";
}

$stmt->close();
$conn->close();
?>
