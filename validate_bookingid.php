<?php
require_once 'db_connect.php';

$bookingid = $_GET['bookingid'] ?? '';
$response = ['valid' => false];

if ($bookingid) {
    $stmt = $conn->prepare("SELECT 1 FROM booking WHERE bookingid = ? LIMIT 1");
    $stmt->bind_param("s", $bookingid);
    $stmt->execute();
    $stmt->store_result();
    $response['valid'] = $stmt->num_rows > 0;
    $stmt->close();
}
header('Content-Type: application/json');
echo json_encode($response);
