<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styling/style3.css">
    <title>Busket List</title>
</head>
<body>
    <header>
        <nav>
            <h1>Busket List</h1>
            <ul>
                <li>Home</li>
                <li>About</li>
            </ul>
        </nav>

        <div class="img-placeholder"></div>

        <section>
            <div class="book-steps">
                <p><span>Step 4: </span>Payment</p>
            </div>
        </section>
    </header>

    <main>
    <?php
        // Retrieve ALL data from POST (from seatSelection.php)
        $trip_id = $_POST['trip_id'] ?? 'TRIP001'; // Default, will come from DB eventually

        $origin = htmlspecialchars(urldecode($_POST['originalOrigin'] ?? ''));
        $destination = htmlspecialchars(urldecode($_POST['originalDestination'] ?? ''));
        $depart = htmlspecialchars(urldecode($_POST['originalDepart'] ?? ''));
        $tripType = htmlspecialchars(urldecode($_POST['originalTripType'] ?? ''));
        $passengers = htmlspecialchars(urldecode($_POST['originalPassengers'] ?? '1'));
        $returnDate = htmlspecialchars(urldecode($_POST['originalReturnDate'] ?? ''));

        $selectedTime = htmlspecialchars(urldecode($_POST['selectedTime'] ?? ''));
        $selectedClass = htmlspecialchars(urldecode($_POST['selectedClass'] ?? ''));
        $selectedFare = htmlspecialchars(urldecode($_POST['selectedFare'] ?? ''));

        $firstName = htmlspecialchars(urldecode($_POST['firstName'] ?? ''));
        $middleName = htmlspecialchars(urldecode($_POST['middleName'] ?? ''));
        $lastName = htmlspecialchars(urldecode($_POST['lastName'] ?? ''));
        $email = htmlspecialchars(urldecode($_POST['email'] ?? ''));
        $mobileNo = htmlspecialchars(urldecode($_POST['mobileNo'] ?? ''));
        $fullAddress = htmlspecialchars(urldecode($_POST['fullAddress'] ?? ''));

        $selectedSeatsString = htmlspecialchars(urldecode($_POST['selected_seats_ids'] ?? ''));
        $selectedSeatsArray = explode(',', $selectedSeatsString); // If you need them as an array

        $totalFare = (float)$passengers * (float)$selectedFare;
    ?>

    <div class="book-selection">
        <div class="book-header">
            <div class="origin-destination">
                <h1><?php echo strtoupper($origin); ?> <span class="arrow-separator">►</span> <?php echo strtoupper($destination); ?></h1>
            </div>
            <div class="book-info">
                <p class="book-details">
                    <?php echo $depart ?: 'N/A'; ?> <span class="separator">|</span>
                    <?php echo $tripType ? ucwords(str_replace('-', ' ', $tripType)) : 'N/A'; ?> <span class="separator">|</span>
                    Total Passenger: <?php echo $passengers ?: '1'; ?>
                    <?php if (!empty($returnDate)) { echo '<span class="separator">|</span> Return Date: ' . htmlspecialchars($returnDate); } ?>
                </p>
                
                <div class="book-details-schedule">
                    <p><span>Departure Time:</span> <?php echo $selectedTime; ?></p>
                    <p><span>Class:</span> <?php echo $selectedClass; ?></p> 
                    <p><span>Fare Per Seat:</span> ₱<?php echo number_format((float)$selectedFare, 2); ?></p>
                    <p><span>Selected Seats:</span> <?php echo $selectedSeatsString; ?></p>
                </div> 
            </div>
        </div>
    </div>

    <div class="fare-payment-container">
        <div class="fare-amount-container">
            <div class="fare-amount-title">
                <p>FARE AMOUNT</pack>
            </div>

            <div class="fare-amount-info">
                <div class="fare-amount-summary">
                    <p>Fare amount:</p>
                    <div class="fare-amount-calcu">
                        <p><?php echo $passengers; ?> Passenger(s) x ₱<?php echo number_format((float)$selectedFare, 2); ?></p>
                        <p>₱ <?php echo number_format($totalFare, 2); ?></p>
                    </div>
                </div>

                <div class="fare-amount-group">
                    <p>Total amount: </p>
                    <p>₱ <?php echo number_format($totalFare, 2); ?></p>
                </div>
            </div>
        </div>

        <form action="receipt.php" method="POST" class="payment-container">
            <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($trip_id); ?>">
            <input type="hidden" name="originalOrigin" value="<?php echo htmlspecialchars($origin); ?>">
            <input type="hidden" name="originalDestination" value="<?php echo htmlspecialchars($destination); ?>">
            <input type="hidden" name="originalDepart" value="<?php echo htmlspecialchars($depart); ?>">
            <input type="hidden" name="originalTripType" value="<?php echo htmlspecialchars($tripType); ?>">
            <input type="hidden" name="originalPassengers" value="<?php echo htmlspecialchars($passengers); ?>">
            <input type="hidden" name="originalReturnDate" value="<?php echo htmlspecialchars($returnDate); ?>">
            <input type="hidden" name="selectedTime" value="<?php echo htmlspecialchars($selectedTime); ?>">
            <input type="hidden" name="selectedClass" value="<?php echo htmlspecialchars($selectedClass); ?>">
            <input type="hidden" name="selectedFare" value="<?php echo htmlspecialchars($selectedFare); ?>"> <input type="hidden" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>">
            <input type="hidden" name="middleName" value="<?php echo htmlspecialchars($middleName); ?>">
            <input type="hidden" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="mobileNo" value="<?php echo htmlspecialchars($mobileNo); ?>">
            <input type="hidden" name="fullAddress" value="<?php echo htmlspecialchars($fullAddress); ?>">
            <input type="hidden" name="selected_seats_ids" value="<?php echo htmlspecialchars($selectedSeatsString); ?>">
            <input type="hidden" name="totalFare" value="<?php echo htmlspecialchars(number_format($totalFare, 2, '.', '')); ?>"> <div class="payment-input">
                <label for="payment" class="required">Enter payment amount:</label>
                <input type="text"  id="payment" name="payment" required min="<?php echo number_format($totalFare, 2, '.', ''); ?>">
            </div>
            <button type="submit" class="Enter">Pay Now</button>
        </form>
    </div>
    </main>
    <footer>
        <div class="footerBoxes">
            <div class="footerBox">
                <h3>Privacy Policy</h3>
                <hr>
                <p>We are committed to protecting your privacy. We will only use the information we collect about you lawfully (in accordance with the Data Protection Act 1998). Please read on if you wish to learn more about our privacy policy.</p>
            </div>

            <div class="footerBox">
                <h3>Terms of Service</h3>
                <hr>
                <p>By using our service, you agree to provide accurate booking information and comply with our travel and cancellation policies. We are not liable for delays or missed trips caused by user error or third-party issues.</p>
            </div>

            <div class="footerBox">
                <h3>Help & Support</h3>
                <hr>
                <p>If you have any questions or need assistance, our support team is here to help. Contact us via email or visit our help center for answers to frequently asked questions.</p>
            </div>
        </div>
        <hr>
        <p class="copy-right">2025 Busket List</p> 
    </footer>
    
</body>
</html>