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

$firstName = safe('firstName', $_POST);
$middleName = safe('middleName', $_POST);
$lastName = safe('lastName', $_POST);
$email = safe('email', $_POST);
$mobileNo = safe('mobileNo', $_POST);
$fullAddress = safe('fullAddress', $_POST);
$discount = safe('discount', $_POST);

$_SESSION['passenger'] = compact('firstName', 'middleName', 'lastName', 'email', 'mobileNo', 'fullAddress', 'discount');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSeats = htmlspecialchars($_POST['selected_seats_ids'] ?? '');
    $_SESSION['selected_seats'] = $selectedSeats;
    header("Location: payment.php");
    exit;
}

$bookedSeatIds = ['B3', 'B7', 'B17', 'A2', 'A6', 'A10', 'A14', 'A18'];
$busSeatData = [];

for ($row = 0; $row < 2; $row++) {
    for ($i = 1; $i <= 9; $i++) {
        $seatId = 'B' . ($row * 9 + $i);
        $busSeatData[] = ['id' => $seatId, 'status' => in_array($seatId, $bookedSeatIds) ? 'booked' : 'available'];
    }
}
$busSeatData[] = ['id' => '19', 'status' => 'available'];
for ($row = 0; $row < 2; $row++) {
    for ($i = 1; $i <= 9; $i++) {
        $seatId = 'A' . ($row * 9 + $i);
        $busSeatData[] = ['id' => $seatId, 'status' => in_array($seatId, $bookedSeatIds) ? 'booked' : 'available'];
    }
}

function getSeatClass($seatId, $status) {
    return $status === 'booked' ? 'seat-item seat--booked' : 'seat-item seat--available';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Selection - Busket List</title>
    <link rel="stylesheet" href="styling/style2.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        html, body {
            height: auto;
            min-height: 100%;
            overflow-x: hidden;
        }
        footer {
            margin-top: 40px;
        }
        nav ul li a {
            text-decoration: none;
            color: inherit;
        }
        nav ul li a:hover {
            color: #555;
        }
        .seat-error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
            display: none;
        }
    </style>
</head>
<body>
<header>
    <nav>
        <h1>Busket List</h1>
        <ul>
            <li><a href="hero.html">Home</a></li>
            <li><a href="#about-section">About</a></li>
        </ul>
    </nav>
    <div class="img-placeholder"></div>
    <section>
        <div class="book-steps">
            <p><span>Step 3: </span>Seat Selection</p>
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
                    <p><span>Fare:</span> ₱<?php echo number_format($fare, 2); ?></p>
                </div>
            </div>
        </div>

        <div class="form-container">
            <form id="seatSelectionForm" action="" method="POST">
                <input type="hidden" name="selected_seats_ids" id="selectedSeatsInput" value="">
                <div class="seat-instruction-text">
                    <p>Click on available seats to reserve your seat.</p>
                </div>
                <div id="seatError" class="seat-error">Please select <?php echo $passengers; ?> seat(s).</div>
                <div class="seat-map-grid-wrapper">
                    <div class="seat-rows-container">
                        <?php
                        foreach ([['B', 0, 18], ['19', 18, 19], ['A', 19, 37]] as [$label, $start, $end]) {
                            echo '<div class="seat-row seat-row-' . strtolower($label) . '">';
                            if ($label === '19') echo '<div class="empty-col-span-9"></div>';
                            for ($i = $start; $i < $end; $i++) {
                                $seat = $busSeatData[$i];
                                echo '<div class="' . getSeatClass($seat['id'], $seat['status']) . '" data-seat-id="' . $seat['id'] . '" data-seat-status="' . $seat['status'] . '"><span class="seat-id">' . $seat['id'] . '</span></div>';
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="seat-legend">
                    <div class="legend-item"><div class="legend-square seat--available"></div> Available</div>
                    <div class="legend-item"><div class="legend-square seat--booked"></div> Booked</div>
                    <div class="legend-item"><div class="legend-square seat--selected"></div> Selected</div>
                    <div class="legend-item"><span>Fare:</span> ₱<?php echo number_format($fare, 2); ?></div>
                </div>
                <div class="form-actions payment-page">
                    <button type="submit" class="next-button">Proceed to Payment</button>
                </div>
            </form>
        </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const seats = document.querySelectorAll('.seat-item');
        const selectedSeatsInput = document.getElementById('selectedSeatsInput');
        const totalPassengers = <?php echo $passengers; ?>;
        const form = document.getElementById('seatSelectionForm');
        const seatError = document.getElementById('seatError');
        let selectedSeats = [];

        function updateSelectedSeatsInput() {
            selectedSeatsInput.value = selectedSeats.join(',');
        }

        seats.forEach(seat => {
            if (seat.dataset.seatStatus !== 'booked') {
                seat.addEventListener('click', function () {
                    const seatId = this.dataset.seatId;
                    if (this.classList.contains('seat--selected')) {
                        this.classList.remove('seat--selected');
                        this.classList.add('seat--available');
                        selectedSeats = selectedSeats.filter(id => id !== seatId);
                    } else {
                        if (selectedSeats.length < totalPassengers) {
                            this.classList.remove('seat--available');
                            this.classList.add('seat--selected');
                            selectedSeats.push(seatId);
                        } else {
                            alert('You can only select up to ' + totalPassengers + ' seat(s).');
                        }
                    }
                    updateSelectedSeatsInput();
                    seatError.style.display = 'none';
                });
            }
        });

        form.addEventListener('submit', function (e) {
            if (selectedSeats.length !== totalPassengers) {
                e.preventDefault();
                seatError.textContent = 'Please select ' + totalPassengers + ' seat(s).';
                seatError.style.display = 'block';
            }
        });

        updateSelectedSeatsInput();
    });
</script>
</body>
</html>
