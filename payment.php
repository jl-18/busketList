<?php
session_start();

function safe_session($key, $array) {
    return htmlspecialchars($array[$key] ?? '');
}

$trip = $_SESSION['trip'] ?? [];
$passenger = $_SESSION['passenger'] ?? [];
$showError = false;
$error = '';

if (isset($_SESSION['payment_error'])) {
    $error = $_SESSION['payment_error'];
    $showError = true;
    unset($_SESSION['payment_error']);
}

$origin = strtoupper(safe_session('origin', $trip));
$destination = strtoupper(safe_session('destination', $trip));
$depart = safe_session('depart', $trip);
$tripType = ucwords(str_replace('-', ' ', safe_session('trip_type', $trip)));
$passengers = intval(safe_session('passengers', $trip));
$returnDate = safe_session('return', $trip);
$selectedTime = safe_session('time', $trip);
$selectedClass = safe_session('class', $trip);
$selectedFare = floatval(safe_session('fare', $trip));

$firstName = safe_session('firstName', $passenger);
$middleName = safe_session('middleName', $passenger);
$lastName = safe_session('lastName', $passenger);
$email = safe_session('email', $passenger);
$mobileNo = safe_session('mobileNo', $passenger);
$fullAddress = safe_session('fullAddress', $passenger);
$discount = safe_session('discount', $passenger);

$trip_id = 'TRIP' . rand(1000, 9999);
$selectedSeatsString = htmlspecialchars($_SESSION['selected_seats'] ?? '');
$selectedSeatsArray = explode(',', $selectedSeatsString);
$totalFare = $selectedFare * $passengers;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment = floatval($_POST['payment'] ?? 0);
    if ($payment < $totalFare || $payment != $totalFare) {
        $_SESSION['payment_error'] = "Enter the correct amount: ₱" . number_format($totalFare, 2);
        header("Location: payment.php");
        exit;
    } else {
        $_SESSION['final_payment'] = $payment;
        $_SESSION['selected_seats'] = $selectedSeatsString;
        $_SESSION['trip_id'] = $trip_id;
        $_SESSION['totalFare'] = $totalFare;
        header("Location: receipt.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busket List - Payment</title>
    <link rel="stylesheet" href="styling/style3.css?v=<?php echo time(); ?>">
    <style>
        html, body {
            height: auto;
            min-height: 100%;
            overflow-x: hidden;
        }
        nav ul li a {
            text-decoration: none;
            color: inherit;
        }
        nav ul li a:hover {
            color: #555;
        }
        .error-message {
            color: red;
            font-weight: bold;
            margin: 0 0 10px 0;
            display: <?php echo $showError ? 'block' : 'none'; ?>;
        }
        .payment-input input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
<header>
    <nav>
        <h1>Busket List</h1>
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="#about-section">About</a></li>
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
    <div class="book-selection">
        <div class="book-header">
            <div class="origin-destination">
                <h1><?php echo $origin; ?> <span class="arrow-separator">►</span> <?php echo $destination; ?></h1>
            </div>
            <div class="book-info">
                <p class="book-details">
                    <?php echo $depart ?: 'N/A'; ?> <span class="separator">|</span>
                    <?php echo $tripType; ?> <span class="separator">|</span>
                    Total Passenger: <?php echo $passengers; ?>
                    <?php if (!empty($returnDate)) echo '<span class="separator">|</span> Return Date: ' . $returnDate; ?>
                </p>
                <div class="book-details-schedule">
                    <p><span>Departure Time:</span> <?php echo $selectedTime; ?></p>
                    <p><span>Class:</span> <?php echo $selectedClass; ?></p>
                    <p><span>Fare Per Seat:</span> ₱<?php echo number_format($selectedFare, 2); ?></p>
                    <p><span>Selected Seats:</span> <?php echo $selectedSeatsString ?: 'None'; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="fare-payment-container">
        <div class="fare-amount-container">
            <div class="fare-amount-title">
                <p>FARE AMOUNT</p>
            </div>
            <div class="fare-amount-info">
                <div class="fare-amount-summary">
                    <p>Fare amount:</p>
                    <div class="fare-amount-calcu">
                        <p><?php echo $passengers; ?> Passenger(s) x ₱<?php echo number_format($selectedFare, 2); ?></p>
                        <p>₱ <?php echo number_format($totalFare, 2); ?></p>
                    </div>
                </div>
                <div class="fare-amount-group">
                    <p>Total amount: </p>
                    <p>₱ <?php echo number_format($totalFare, 2); ?></p>
                </div>
            </div>
        </div>

        <form action="" method="POST" class="payment-container">
            <input type="hidden" name="selected_seats_ids" value="<?php echo $selectedSeatsString; ?>">
            <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
            <input type="hidden" name="totalFare" value="<?php echo number_format($totalFare, 2, '.', ''); ?>">

            <div class="payment-input">
                <label for="payment" class="required">Enter payment amount:</label>
                <div class="error-message"><?php echo $error; ?></div>
                <input type="number" step="0.01" id="payment" name="payment" required>
            </div>

            <button type="submit" class="Enter">Pay Now</button>
        </form>
    </div>
</main>

<footer id="about-section">
    <div class="footerBoxes">
        <div class="footerBox">
            <h3>Privacy Policy</h3>
            <hr>
            <p>
            We are committed to protecting your privacy. We will only use the
            information we collect about you lawfully (in accordance with the
            Data Protection Act 1998). Please read on if you wish to learn more
            about our privacy policy.
            </p>
        </div>
        <div class="footerBox">
            <h3>Terms of Service</h3>
            <hr>
            <p>
            By using our service, you agree to provide accurate booking
            information and comply with our travel and cancellation policies. We
            are not liable for delays or missed trips caused by user error or
            third-party issues.
            </p>
        </div>
        <div class="footerBox">
            <h3>Help & Support</h3>
            <hr>
            <p>
            If you have any questions or need assistance, our support team is
            here to help. Contact us via email or visit our help center for
            answers to frequently asked questions.
            </p>
        </div>
    </div>
    <hr>
    <p class="copy-right">2025 Busket List</p>
</footer>
</body>
</html>
