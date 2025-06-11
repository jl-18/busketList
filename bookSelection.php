
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busket List</title>
    <link rel="stylesheet" href="style2.css?v=<?php echo time(); ?>">
</head>

<body>
    <header>
        <nav>
            <h1>Busket List</h1>
            <ul>
                <li>Home</li>
                <li>Services</li>
                <li>About</li>
            </ul>
        </nav>

        <div>pic placeholder</div>

        <section>
            <div class="book-steps">
            <p><span>Step 1: </span>Choose a departure schedule</p>
            </div>
        </section>
    </header>

    <main>
        <div class="book-selection">
            <div class="book-header">
                <div class="origin-destination"></div>
                <div class="book-info"></div>
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
                    // This section will now be processed by the PHP server.
                    // Replace this static array with actual database fetching code.
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
                    // Encode trip details for URL parameters
                    $encodedTime = urlencode($trip['time']);
                    $encodedClass = urlencode($trip['class']);
                    $encodedSeats = urlencode($trip['seats']);
                    $encodedFare = urlencode($trip['fare']);

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($trip['time']) . '</td>';
                    echo '<td>' . htmlspecialchars($trip['class']) . '</td>';
                    echo '<td>' . htmlspecialchars($trip['seats']) . '</td>';
                    echo '<td>₱' . number_format($trip['fare'], 2) . '</td>';
                    // Link the "Book" button to customerInfo.php, passing trip details as URL parameters
                    echo '<td><a href="passenger.php?time=' . $encodedTime . '&class=' . $encodedClass . '&seats=' . $encodedSeats . '&fare=' . $encodedFare . '" class="book-button">Book</a></td>';
                    echo '</tr>';
}
                    ?>
                </tbody>
            </table>
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

    <script>
        // Move script to the end of the body for better performance
        const params = new URLSearchParams(window.location.search);
        const tripType = params.get('trip-type');
        const origin = params.get('origin');
        const destination = params.get('destination');
        const depart = params.get('depart');
        const returnDate = params.get('return');
        const passengers = params.get('passengers');

        const originDestinationDiv = document.querySelector('.origin-destination');
        const bookInfoDiv = document.querySelector('.book-info');

        if (origin && destination) {
            const formattedOrigin = origin.toUpperCase();
            const formattedDestination = destination.toUpperCase();
            
            originDestinationDiv.innerHTML = `<h1>${formattedOrigin} <span class="arrow-separator">►</span> ${formattedDestination} </h1>`;
        } else {
            originDestinationDiv.innerHTML = `<h1>Route Information Unavailable</h1>`;
        }

        const displayDepartDate = depart || 'N/A'; 
        const displayTripType = tripType ? tripType.replace('-', ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ') : 'N/A'; 
        const displayPassengers = passengers || '1';

        bookInfoDiv.innerHTML = `
            <p class="book-details">
                ${displayDepartDate} <span class="separator">|</span>
                ${displayTripType} <span class="separator">|</span>
                Total Passenger: ${displayPassengers}
            </p>
        `;

        console.log(tripType, origin, destination, depart, returnDate, passengers);
    </script>
</body>
</html>