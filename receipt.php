<?php
session_start();
require_once 'db_connect.php';

function safe($key, $array) {
    return htmlspecialchars(urldecode($array[$key] ?? ''));
}

$bookingid = $_GET['bookingid'] ?? ($_SESSION['trip_id'] ?? '');
$trip = $_SESSION['trip'] ?? [];
$passenger = $_SESSION['passenger'] ?? [];
$invoice = $_SESSION['invoice'] ?? [];

if ($bookingid && isset($_GET['bookingid'])) {
    // Fetch existing booking
    $stmt = $conn->prepare("SELECT b.bookingid, b.passengerid, r.origin, r.destination, sm.departtime, sm.scheddate, 
        bt.description AS bus_class, i.grandtotal, p.firstname, p.lastname, p.middlename, i.paymentdate, i.paymenttime
        FROM booking b
        JOIN schedmatrix sm ON b.schedid = sm.schedid
        JOIN routes r ON b.routeid = r.routeid
        JOIN passenger p ON b.passengerid = p.passengerid
        JOIN invoice i ON b.bookingid = i.bookingid
        JOIN bus bs ON sm.busid = bs.busid
        JOIN bustype bt ON bs.bustypeid = bt.bustypeid
        WHERE b.bookingid = ?");
    $stmt->bind_param("s", $bookingid);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($data) {
        $origin = strtoupper($data['origin']);
        $destination = strtoupper($data['destination']);
        $selectedTime = $data['departtime'];
        $selectedClass = $data['bus_class'];
        $schedDate = $data['scheddate'];
        $firstName = $data['firstname'];
        $middleName = $data['middlename'];
        $lastName = $data['lastname'];
        $passengers = 1;
        $totalFare = $data['grandtotal'];
    }
} else {
    // New Booking Flow
    $origin        = strtoupper(safe('origin', $trip));
    $destination   = strtoupper(safe('destination', $trip));
    $passengers    = intval(safe('passengers', $trip));
    $selectedTime  = safe('time', $trip);
    $selectedClass = safe('class', $trip);
    $schedDate     = safe('depart', $trip);
    $fare          = floatval(safe('fare', $trip));

    $firstName   = safe('firstName', $passenger);
    $middleName  = safe('middleName', $passenger);
    $lastName    = safe('lastName', $passenger);
    $totalFare   = floatval($_SESSION['totalFare'] ?? 0);
    $bookingid   = $_SESSION['trip_id'] ?? '';
    $passenger_id = $_SESSION['passenger_id'] ?? '';

    // Insert passenger
    if ($passenger_id) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM passenger WHERE passengerid = ?");
        $stmt->bind_param("s", $passenger_id);
        $stmt->execute(); $stmt->bind_result($exists); $stmt->fetch(); $stmt->close();

        if (!$exists) {
            $stmt = $conn->prepare("INSERT INTO passenger (passengerid, lastname, firstname, middlename, email, phonenumber, address)
                                     VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $passenger_id, $lastName, $firstName, $middleName,
                $passenger['email'], $passenger['mobileNo'], $passenger['fullAddress']);
            $stmt->execute(); $stmt->close();
        }
    }

    // Get routeid
    $stmt = $conn->prepare("SELECT routeid FROM routes WHERE origin = ? AND destination = ? LIMIT 1");
    $stmt->bind_param("ss", $origin, $destination);
    $stmt->execute(); $stmt->bind_result($routeid); $stmt->fetch(); $stmt->close();

    // Insert booking
    $stmt = $conn->prepare("SELECT COUNT(*) FROM booking WHERE bookingid = ?");
    $stmt->bind_param("s", $bookingid);
    $stmt->execute(); $stmt->bind_result($exists); $stmt->fetch(); $stmt->close();

    if (!$exists && $bookingid && $routeid && $passenger_id && $trip['sched_id']) {
        $stmt = $conn->prepare("INSERT INTO booking (bookingid, passengerid, schedid, routeid, bookingdate, bookingtime)
                                VALUES (?, ?, ?, ?, CURDATE(), CURTIME())");
        $stmt->bind_param("ssii", $bookingid, $passenger_id, $trip['sched_id'], $routeid);
        $stmt->execute(); $stmt->close();
    }

    // Insert invoice
    if (!empty($invoice)) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM invoice WHERE bookingid = ?");
        $stmt->bind_param("s", $bookingid);
        $stmt->execute(); $stmt->bind_result($exists); $stmt->fetch(); $stmt->close();

        if (!$exists) {
            $stmt = $conn->prepare("INSERT INTO invoice (bookingid, fareid, discounttypeid, discountamount, grandtotal, issueddate, issuedtime, paymentdate, paymenttime)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssddssss",
                $invoice['bookingid'],
                $invoice['fareid'],
                $invoice['discounttypeid'],
                $invoice['discountamount'],
                $invoice['grandtotal'],
                $invoice['issueddate'],
                $invoice['issuedtime'],
                $invoice['paymentdate'],
                $invoice['paymenttime']
            );
            $stmt->execute(); $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bus Ticket</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="styling/receipt.css">
</head>
<body>
<div class="ticket-container">
  <div class="ticket-main">
    <div class="ticket-header">
      <div class="bus-logo"><i class="fa-solid fa-bus fa-xl"></i></div>
      <div class="bus-class"><?php echo $selectedClass ?? ''; ?></div>
      <div class="bus-route"><?php echo $origin ?? ''; ?> <span class="arrow-separator">►</span> <?php echo $destination ?? ''; ?></div>
    </div>

    <div class="ticket-details">
      <div class="detail-row"><div class="detail-item"><span class="label">Booking ID:</span> <?php echo $bookingid ?? ''; ?></div></div>
      <div class="detail-row"><div class="detail-item"><span class="label">Passenger Name:</span> <?php echo ($firstName ?? '') . ' ' . (($middleName ?? '') ? substr($middleName, 0, 1) . '. ' : '') . ($lastName ?? ''); ?></div></div>
      <div class="detail-row"><div class="detail-item"><span class="label">Passenger Count:</span> <?php echo $passengers ?? 1; ?> Only</div></div>
      <div class="detail-row"><div class="detail-item total-price"><span class="label">Total Price</span> <span class="price-value">₱<?php echo number_format($totalFare ?? 0, 2); ?></span></div></div>
      <div class="detail-row"><div class="detail-item status"><div class="bill-paid"><i class="fas fa-check-circle"></i> Bill Paid</div></div></div>
      <div class="route-time-section">
        <div class="route"><?php echo $origin ?? ''; ?> <span class="arrow">►</span> <?php echo $destination ?? ''; ?></div>
        <div class="time-details">
          <div class="time-item"><span class="label">Departure:</span> <?php echo $selectedTime ? date('H:i', strtotime($selectedTime)) : 'N/A'; ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="ticket-stub">
    <div class="stub-header">Bus Ticket</div>
    <div class="stub-details">
      <p><span class="label">Booking ID:</span> <?php echo $bookingid ?? ''; ?></p>
      <p><span class="label">Name:</span> <?php echo ($firstName ?? '') . ' ' . (($middleName ?? '') ? substr($middleName, 0, 1) . '. ' : '') . ($lastName ?? ''); ?></p>
      <p><span class="label">From:</span> <?php echo $origin ?? ''; ?></p>
      <p><span class="label">To:</span> <?php echo $destination ?? ''; ?></p>
      <p><span class="label">Departure:</span> <?php echo $schedDate ?? ''; ?></p>
      <p><span class="label">Passenger(s):</span> <?php echo $passengers ?? 1; ?> Only</p>
      <p><span class="label">Total:</span> ₱<?php echo number_format($totalFare ?? 0, 2); ?></p>
    </div>
  </div>

  <div class="ticket-footer">
    <div class="note">Thank you for booking with Busket List!</div>
    <div class="contact-info"><i class="fas fa-phone-alt"></i> +977-9876543210, +977-0123456789</div>
  </div>
  <div class="ticket-stub-footer"><div class="contact-info"><i class="fas fa-phone-alt"></i> +977-9876543210</div></div>
</div>
<div class="home-button-container">
  <a href="hero.php" class="home-button">Return to Home</a>
</div>
</body>
</html>

