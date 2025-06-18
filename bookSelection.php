<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Your Trip - Busket List</title>
    <link rel="stylesheet" href="styling/style2.css?v=<?php echo time(); ?>">
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
    </style>
</head>
<body>
<?php
function get_safe_param($key) {
    return htmlspecialchars(urldecode($_GET[$key] ?? ''));
}

$origin = get_safe_param('origin');
$destination = get_safe_param('destination');
$depart = get_safe_param('depart');
$tripType = get_safe_param('trip-type');
$passengers = get_safe_param('passengers') ?: '1';
$returnDate = get_safe_param('return');

$formattedOrigin = strtoupper($origin);
$formattedDestination = strtoupper($destination);
$displayDepartDate = $depart ?: 'N/A';
$displayTripType = $tripType ? ucwords(str_replace('-', ' ', $tripType)) : 'N/A';
$displayPassengers = $passengers ?: '1';
?>
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
            <p><span>Step 1: </span>Choose a departure schedule</p>
        </div>
    </section>
</header>

<main>
    <div class="book-selection">
        <div class="book-header">
            <div class="origin-destination">
                <h1><?php echo $formattedOrigin; ?> <span class="arrow-separator">►</span> <?php echo $formattedDestination; ?></h1>
            </div>
            <div class="book-info">
                <p class="book-details">
                    <?php echo $displayDepartDate; ?> <span class="separator">|</span>
                    <?php echo $displayTripType; ?> <span class="separator">|</span>
                    Total Passenger: <?php echo $displayPassengers; ?>
                    <?php if (!empty($returnDate)) echo '<span class="separator">|</span> Return Date: ' . htmlspecialchars($returnDate); ?>
                </p>
            </div>
        </div>

        <table class="trips-table">
            <thead>
                <tr>
                    <th>Departure Time</th>
                    <th>Class</th>
                    <th>Available Seats</th>
                    <th>Fare</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php
            $trips = [
                ['time' => '12:00 AM', 'class' => 'Regular Aircon (EXPRESS)', 'seats' => 38, 'fare' => 586.00],
                ['time' => '07:00 AM', 'class' => 'Regular Aircon (EXPRESS)', 'seats' => 38, 'fare' => 586.00],
                ['time' => '08:00 AM', 'class' => 'Regular Aircon (EXPRESS)', 'seats' => 38, 'fare' => 586.00],
                ['time' => '09:00 AM', 'class' => 'Regular Aircon (EXPRESS)', 'seats' => 38, 'fare' => 586.00],
                ['time' => '11:00 AM', 'class' => 'Deluxe (EXPRESS)', 'seats' => 31, 'fare' => 624.00],
                ['time' => '02:30 PM', 'class' => 'Deluxe (EXPRESS)', 'seats' => 31, 'fare' => 624.00],
                ['time' => '03:30 PM', 'class' => 'Regular Aircon (EXPRESS)', 'seats' => 38, 'fare' => 586.00],
            ];

            foreach ($trips as $trip) {
                $params = [
                    'time' => urlencode($trip['time']),
                    'class' => urlencode($trip['class']),
                    'seats' => urlencode($trip['seats']),
                    'fare' => urlencode($trip['fare']),
                    'origin' => urlencode($origin),
                    'destination' => urlencode($destination),
                    'depart' => urlencode($depart),
                    'trip-type' => urlencode($tripType),
                    'passengers' => urlencode($passengers),
                ];
                if (!empty($returnDate)) {
                    $params['return'] = urlencode($returnDate);
                }
                $query_string = http_build_query($params);

                echo '<tr>';
                echo '<td>' . htmlspecialchars($trip['time']) . '</td>';
                echo '<td>' . htmlspecialchars($trip['class']) . '</td>';
                echo '<td>' . htmlspecialchars($trip['seats']) . '</td>';
                echo '<td>₱' . number_format($trip['fare'], 2) . '</td>';
                echo '<td><a href="passenger.php?' . $query_string . '" class="book-button">Book</a></td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
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
