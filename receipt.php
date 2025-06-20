<?php
session_start();
include 'db_connect.php';

function safe($key, $array) {
    return htmlspecialchars(urldecode($array[$key] ?? ''));
}

// Retrieve session values for trip and passenger.
$trip = $_SESSION['trip'] ?? [];
$passenger = $_SESSION['passenger'] ?? [];

$origin      = strtoupper(safe('origin', $trip));
$destination = strtoupper(safe('destination', $trip));
$passengers  = intval(safe('passengers', $trip));

// Schedule details.
$selectedTime  = safe('time', $trip);
$selectedClass = safe('class', $trip);
$sched_id      = safe('sched_id', $trip);
$fare          = floatval(safe('fare', $trip));
$depart        = safe('depart', $trip);  // Departure date/time

// Passenger details.
$firstName   = safe('firstName', $passenger);
$middleName  = safe('middleName', $passenger);
$lastName    = safe('lastName', $passenger);
$email       = safe('email', $passenger);
$mobileNo    = safe('mobileNo', $passenger);
$fullAddress = safe('fullAddress', $passenger);

$totalFare = floatval($_SESSION['totalFare'] ?? 0);
$bookingid = $_SESSION['trip_id'];  // Booking ID was saved earlier

// Insert the passenger record if it doesn't exist.
$passenger_id = $_SESSION['passenger_id'] ?? ''; 
if (!empty($passenger_id)) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM passenger WHERE passengerid = ?");
    $stmt->bind_param("s", $passenger_id);
    $stmt->execute();
    $stmt->bind_result($pCount);
    $stmt->fetch();
    $stmt->close();

    if ($pCount == 0) {
        $stmt = $conn->prepare("INSERT INTO passenger (passengerid, lastname, firstname, middlename, email, phonenumber, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $passenger_id, $lastName, $firstName, $middleName, $email, $mobileNo, $fullAddress);
        $stmt->execute();
        $stmt->close();
    }
}

// Retrieve route ID based on origin and destination.
$routeid = null;
$stmt = $conn->prepare("SELECT routeid FROM routes WHERE origin = ? AND destination = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param("ss", $origin, $destination);
    $stmt->execute();
    $stmt->bind_result($routeid);
    $stmt->fetch();
    $stmt->close();
}

// ---------------------------------------------------------------------------
// IMPORTANT: Insert the booking record FIRST since invoice.bookingid is a foreign key.
// Check if a booking record with this bookingid already exists.
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM booking WHERE bookingid = ?");
$stmt->bind_param("s", $bookingid);
$stmt->execute();
$stmt->bind_result($bCount);
$stmt->fetch();
$stmt->close();

$bookingDate = date('Y-m-d');
$bookingTime = date('H:i:s');

if ($bCount == 0 && !empty($sched_id) && !empty($passenger_id) && !empty($routeid)) {
    $stmt = $conn->prepare("INSERT INTO booking (bookingid, passengerid, schedid, routeid, bookingdate, bookingtime) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $bookingid, $passenger_id, $sched_id, $routeid, $bookingDate, $bookingTime);
    $stmt->execute();
    $stmt->close();
}

// ---------------------------------------------------------------------------
// Now, insert the invoice record.
// Retrieve invoice details stored in session.
$invoice = $_SESSION['invoice'] ?? [];
if (!empty($invoice)) {
    $stmt = $conn->prepare("INSERT INTO invoice (bookingid, fareid, discounttypeid, discountamount, grandtotal, issueddate, issuedtime, paymentdate, paymenttime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    // Bind parameters: bookingid, fareid, discounttypeid are strings; discountamount and grandtotal are doubles; then the remaining 4 are strings.
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
    $stmt->execute();
    $stmt->close();
    // echo '<pre>' . print_r($_SESSION['invoice'], true) . '</pre>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Ticket</title>
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
            <div class="bus-class"><?php echo $selectedClass; ?></div>
            <div class="bus-route"><?php echo $origin; ?> <span class="arrow-separator">►</span> <?php echo $destination; ?></div>
        </div>

        <div class="ticket-details">
            <div class="detail-row">
                <div class="detail-item"><span class="label">Booking ID:</span> <?php echo $bookingid; ?></div>
                
            </div>
            <div class="detail-row ">
                <div class="detail-item"><span class="label">Passenger Name:</span> <?php echo $firstName . ' ' . ($middleName ? substr($middleName,0,1) . '. ' : '') . $lastName; ?></div>
            </div>
            <div class="detail-row ">
                <div class="detail-item"><span class="label">Passenger Count:</span> <?php echo $passengers; ?> Only</div>
            </div>
            <div class="detail-row">
                <div class="detail-item total-price">
                    <span class="label">Total Price</span>
                    <span class="price-value">₱<?php echo number_format($totalFare, 2); ?></span>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-item status">
                    <div class="bill-paid"><i class="fas fa-check-circle"></i> Bill Paid</div>
                </div>
            </div>
            <div class="route-time-section">
                <div class="route"><?php echo $origin; ?> <span class="arrow">►</span> <?php echo $destination; ?></div>
                <div class="time-details">
                    <div class="time-item"><span class="label">Departure:</span> <?php echo $selectedTime ? date('H:i', strtotime($selectedTime)) : 'N/A'; ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="ticket-stub">
        <div class="stub-header">Bus Ticket</div>
        <div class="stub-details">
            <p><span class="label">Booking ID:</span> <?php echo $bookingid; ?></p>
            <p><span class="label">Name:</span> <?php echo $firstName . ' ' . ($middleName ? substr($middleName,0,1).'. ' : '') . $lastName; ?></p>
            <p><span class="label">From:</span> <?php echo $origin; ?></p>
            <p><span class="label">To:</span> <?php echo $destination; ?></p>
            <p><span class="label">Departure:</span> <?php echo $depart; ?></p>
            <p><span class="label">Passenger(s):</span> <?php echo $passengers; ?> Only</p>
            <p><span class="label">Total:</span> ₱<?php echo number_format($totalFare, 2); ?></p>
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