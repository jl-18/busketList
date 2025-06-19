<?php
session_start();

function safe($key, $array) {
    return htmlspecialchars(urldecode($array[$key] ?? ''));
}

$trip = $_SESSION['trip'] ?? [];
$origin = strtoupper(safe('origin', $trip));
$destination = strtoupper(safe('destination', $trip));
$depart = safe('depart', $trip);
$tripType = ucwords(str_replace('-', ' ', safe('trip_type', $trip)));
$passengers = intval(safe('passengers', $trip));
$returnDate = safe('return', $trip);

$selectedTime = safe('time', $trip);
$selectedClass = safe('class', $trip);
$availableSeats = intval(safe('seats', $trip));
$fare = floatval(safe('fare', $trip));

$passenger = $_SESSION['passenger'] ?? [];
$firstName = safe('firstName', $passenger);
$middleName = safe('middleName', $passenger);
$lastName = safe('lastName', $passenger);
$email = safe('email', $passenger);
$mobileNo = safe('mobileNo', $passenger);
$fullAddress = safe('fullAddress', $passenger);
$discount = safe('discount', $passenger);

$selectedSeatsString = htmlspecialchars($_SESSION['selected_seats'] ?? '');
$selectedSeatsArray = explode(',', $selectedSeatsString);

$totalFare = $passengers * $fare;
$paymentAmount = floatval($_SESSION['final_payment'] ?? 0);
$trip_id = $_SESSION['trip_id'] ?? 'TRIP' . date('YmdHis');

if ($paymentAmount < $totalFare) {
    die('<h2 style="color: red; text-align: center;">Insufficient payment. Please go back and pay the correct amount.</h2>');
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
                <div class="detail-item"><span class="label">Trip ID:</span> <?php echo $trip_id; ?></div>
                <div class="detail-item price">₱<?php echo number_format($fare, 2); ?> <span class="small-text">/seat</span></div>
            </div>
            <div class="detail-row name-section">
                <span class="label">Passenger Name:</span> <?php echo $firstName . ' ' . ($middleName ? substr($middleName, 0, 1) . '. ' : '') . $lastName; ?>
            </div>
            <div class="detail-row">
                <div class="detail-item total-price">
                    <span class="label">Total Price</span>
                    <span class="price-value">₱<?php echo number_format($totalFare, 2); ?></span>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-item"><span class="label">Passenger Count:</span> <?php echo $passengers; ?> Only</div>
                <div class="detail-item status">
                    <div class="bill-paid"><i class="fas fa-check-circle"></i> Bill Paid</div>
                </div>
            </div>
            <div class="route-time-section">
                <div class="route"><?php echo $origin; ?> <span class="arrow">►</span> <?php echo $destination; ?></div>
                <div class="time-details">
                    <div class="time-item"><span class="label">Departure:</span> <?php echo $selectedTime; ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="ticket-stub">
        <div class="stub-header">Bus Ticket</div>
        <div class="stub-details">
            <p><span class="label">Trip ID:</span> <?php echo $trip_id; ?></p>
            <p><span class="label">Name:</span> <?php echo $firstName . ' ' . ($middleName ? substr($middleName, 0, 1) . '. ' : '') . $lastName; ?></p>
            <p><span class="label">From:</span> <?php echo $origin; ?></p>
            <p><span class="label">To:</span> <?php echo $destination; ?></p>
            <p><span class="label">Departure:</span> <?php echo $selectedTime; ?></p>
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
    <a href="index.html" class="home-button">Return to Home</a>
</div>
</body>
</html>
