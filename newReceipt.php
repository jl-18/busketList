<?php
session_start();
require_once 'db_connect.php';

$bookingid = $_POST['bookingid'] ?? '';
$schedid = $_POST['schedid'] ?? '';
$updated = false;

if ($bookingid && $schedid) {
    // Update booking schedule
    $stmt = $conn->prepare("UPDATE booking SET schedid = ? WHERE bookingid = ?");
    $stmt->bind_param("is", $schedid, $bookingid);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $updated = true;
    }
    $stmt->close();

    // Fetch updated booking info
    $stmt = $conn->prepare("SELECT p.firstname, p.lastname, s.scheddate, s.departtime, r.origin, r.destination, b.passengerid
                            FROM booking b
                            JOIN passenger p ON b.passengerid = p.passengerid
                            JOIN schedmatrix s ON b.schedid = s.schedid
                            JOIN routes r ON b.routeid = r.routeid
                            WHERE b.bookingid = ?");
    $stmt->bind_param("s", $bookingid);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Updated Ticket - Busket List</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="styling/receipt.css">
</head>
<body>
<div class="ticket-container">
  <div class="ticket-main">
    <div class="ticket-header">
      <div class="bus-logo">
        <i class="fa-solid fa-bus fa-xl"></i>
      </div>
      <div class="bus-class">RESCHEDULED</div>
      <div class="bus-route">
        <?php echo strtoupper($booking['origin']) . ' <span class="arrow-separator">&#9658;</span> ' . strtoupper($booking['destination']); ?>
      </div>
    </div>

    <div class="ticket-details">
      <div class="detail-row">
        <div class="detail-item"><span class="label">Booking ID:</span> <?php echo $bookingid; ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-item"><span class="label">Passenger Name:</span> <?php echo $booking['firstname'] . ' ' . $booking['lastname']; ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-item"><span class="label">Updated Departure:</span> <?php echo $booking['scheddate'] . ' at ' . date('g:i A', strtotime($booking['departtime'])); ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-item status">
          <div class="bill-paid"><i class="fas fa-check-circle"></i> Schedule Updated</div>
        </div>
      </div>
      <div class="route-time-section">
        <div class="route">
          <?php echo strtoupper($booking['origin']) . ' <span class="arrow">&#9658;</span> ' . strtoupper($booking['destination']); ?>
        </div>
        <div class="time-details">
          <div class="time-item">
            <span class="label">New Departure:</span> <?php echo date('g:i A', strtotime($booking['departtime'])); ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="ticket-stub">
    <div class="stub-header">Updated Ticket</div>
    <div class="stub-details">
      <p><span class="label">Booking ID:</span> <?php echo $bookingid; ?></p>
      <p><span class="label">Name:</span> <?php echo $booking['firstname'] . ' ' . $booking['lastname']; ?></p>
      <p><span class="label">From:</span> <?php echo strtoupper($booking['origin']); ?></p>
      <p><span class="label">To:</span> <?php echo strtoupper($booking['destination']); ?></p>
      <p><span class="label">Departure:</span> <?php echo $booking['scheddate']; ?> @ <?php echo date('g:i A', strtotime($booking['departtime'])); ?></p>
    </div>
  </div>

  <div class="ticket-footer">
    <div class="note">Thank you for booking with Busket List!</div>
    <div class="contact-info"><i class="fas fa-phone-alt"></i> +977-9876543210, +977-0123456789</div>
  </div>
  <div class="ticket-stub-footer">
    <div class="contact-info"><i class="fas fa-phone-alt"></i> +977-9876543210</div>
  </div>
</div>
<div class="home-button-container">
  <a href="hero.php" class="home-button">Return to Home</a>
</div>
</body>
</html>
