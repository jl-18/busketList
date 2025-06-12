<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Ticket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styling/receipt.css">
</head>

<body>
    <?php
        // Retrieve ALL variables passed from payment.php via POST
        $trip_id = htmlspecialchars(urldecode($_POST['trip_id'] ?? 'TRIP001')); 
        $origin = strtoupper(htmlspecialchars(urldecode($_POST['originalOrigin'] ?? 'Manila')));
        $destination = strtoupper(htmlspecialchars(urldecode($_POST['originalDestination'] ?? 'Baguio')));
        $depart = htmlspecialchars(urldecode($_POST['originalDepart'] ?? '2025-07-15'));
        $tripType = htmlspecialchars(urldecode($_POST['originalTripType'] ?? 'One-Way'));
        $passengers = htmlspecialchars(urldecode($_POST['originalPassengers'] ?? '1'));
        $returnDate = htmlspecialchars(urldecode($_POST['originalReturnDate'] ?? 'N/A'));

        $selectedTime = htmlspecialchars(urldecode($_POST['selectedTime'] ?? '10:00 AM'));
        $selectedClass = htmlspecialchars(urldecode($_POST['selectedClass'] ?? 'Economy'));
        $selectedFare = htmlspecialchars(urldecode($_POST['selectedFare'] ?? '850.00')); // Fare per seat, keep as number for calculation

        $firstName = htmlspecialchars(urldecode($_POST['firstName'] ?? 'Juan'));
        $middleName = htmlspecialchars(urldecode($_POST['middleName'] ?? ''));
        $lastName = htmlspecialchars(urldecode($_POST['lastName'] ?? 'Dela Cruz'));
        $email = htmlspecialchars(urldecode($_POST['email'] ?? 'juan.delacruz@example.com'));
        $mobileNo = htmlspecialchars(urldecode($_POST['mobileNo'] ?? '09171234567'));
        $fullAddress = htmlspecialchars(urldecode($_POST['fullAddress'] ?? '123 Main St, Anytown'));

        $selectedSeatsString = htmlspecialchars(urldecode($_POST['selected_seats_ids'] ?? 'A1, A2'));
        $selectedSeatsArray = explode(',', $selectedSeatsString);

        $totalFare = htmlspecialchars($_POST['totalFare'] ?? '0.00'); 
        $paymentAmount = htmlspecialchars($_POST['payment'] ?? '0.00');

        // for backend: compare paymentAmount with totalFare here for validation
        $balance = (float)$totalFare - (float)$paymentAmount;
    ?>
    <div class="ticket-container">
        <div class="ticket-main">
            <div class="ticket-header">
                <div class="bus-logo">
                    <i class="fa-solid fa-bus fa-xl"></i>
                </div>
                <div class="bus-class"><?php echo $selectedClass; ?></div>
                <div class="bus-route"><?php echo strtoupper($origin); ?> <span class="arrow-separator">►</span> <?php echo strtoupper($destination); ?></div>
            </div>

            <div class="ticket-details">
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="label">Trip Id: </span> <?php echo $trip_id; ?>
                    </div>
                    <div class="detail-item price">
                        ₱<?php echo number_format((float)$selectedFare, 2); ?> <span class="small-text">/seat</span>
                    </div>
                    
                </div>

                <div class="detail-row name-section">
                    <span class="label">Name of Passenger:</span> <?php echo $firstName; ?> <?php if (!empty($middleName)) echo substr($middleName, 0, 1) . '. '; ?> <?php echo $lastName; ?>
                </div>

                <div class="detail-row">
                    <div class="detail-item">
                        <span class="label">Total Seat No.:</span> <?php echo $selectedSeatsString; ?>
                    </div>
                    <div class="detail-item total-price">
                        <span class="label">Total Price</span>
                        <span class="price-value">₱<?php echo number_format((float)$totalFare, 2); ?></span>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-item">
                        <span class="label">Total No. of Passenger:</span> <?php echo $passengers; ?> Only
                    </div>
                    <div class="detail-item status">
                        <div class="bill-paid">
                            <i class="fas fa-check-circle"></i> Bill Paid
                        </div>
                    </div>
                </div>

                <div class="route-time-section">
                    <div class="route">
                        <?php echo $origin; ?> <span class="arrow">&#x25BA;</span> <?php echo $destination; ?>
                    </div>
                    <div class="time-details">
                        <div class="time-item">
                            <span class="label">Departure at:</span> <?php echo $selectedTime; ?>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>

        <div class="ticket-stub">
            <div class="stub-header">Bus Ticket</div>
            <div class="stub-details">
                <p><span class="label">Trip ID:</span> <?php echo $trip_id; ?></p>
                <p><span class="label">Name:</span> <?php echo $firstName; ?> <?php if (!empty($middleName)) echo substr($middleName, 0, 1) . '. '; ?> <?php echo $lastName; ?> </p>
                <p><span class="label">From:</span> <?php echo $origin; ?></p>
                <p><span class="label">To:</span> <?php echo $destination; ?></p>
                <p><span class="label">Dept. Time:</span> <?php echo $selectedTime; ?></p>
                <p><span class="label">Seat No.:</span> <?php echo $selectedSeatsString; ?></p>
                <p><span class="label">Total Passenger:</span> <?php echo $passengers; ?> Only</p>
                <p><span class="label">Total Price:</span> ₱<?php echo number_format((float)$totalFare, 2); ?></p>
            </div>
        </div>

        <div class="ticket-footer">
            <div class="note">
                Thank you for booking with Busket List!
            </div>
            <div class="contact-info">
                <i class="fas fa-phone-alt"></i> +977-9876543210, +977-0123456789
            </div>
        </div>
        <div class="ticket-stub-footer">
            <div class="contact-info">
                <i class="fas fa-phone-alt"></i> +977-9876543210
            </div>
        </div>
    </div>
    <div class="home-button-container">
        <a href="hero.html" class="home-button">Return to Home</a>
    </div>
    
</body>
</html>