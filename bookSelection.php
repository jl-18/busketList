<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
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
include 'db_connect.php'; // Ensure this file sets up $conn

// Helper to safely get parameters
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

// Query available trips using the selected origin, destination, and date.
// Here, we join the schedmatrix, routes, bus, bustype, and farematrix tables.
$sql = "SELECT s.sched_id, s.sched_date, s.depart_time, bt.description AS class, bt.seatcap, f.fareamount
        FROM schedmatrix s
        JOIN routes r ON s.route_id = r.routeid
        JOIN bus b ON s.bus_id = b.busid
        JOIN bustype bt ON b.bustypeid = bt.bustypeid
        JOIN farematrix f ON r.routeid = f.routeid AND b.bustypeid = f.bustypeid
        WHERE r.origin = ? AND r.destination = ? AND s.sched_date = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $origin, $destination, $depart);
$stmt->execute();
$result = $stmt->get_result();
?>

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
            <p><span>Step 1: </span>Choose a departure schedule</p>
        </div>
    </section>
</header>

<main>
  <div class="book-selection">
    <div class="book-header">
        <div class="origin-destination">
          <h1>
            <?php echo $formattedOrigin; ?> 
            <span class="arrow-separator">►</span> 
            <?php echo $formattedDestination; ?>
          </h1>
        </div>
        <div class="book-info">
          <p class="book-details">
            <?php echo $displayDepartDate; ?> 
            <span class="separator">|</span>
            <?php echo $displayTripType; ?> 
            <span class="separator">|</span> 
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
      if ($result->num_rows > 0) {
          // Fetch each available trip and generate a row.
          while ($trip = $result->fetch_assoc()) {
              // Build query parameters to pass to the next step (passenger details)
              $params = [
                'sched_id'    => urlencode($trip['sched_id']),
                'origin'      => urlencode($origin),
                'destination' => urlencode($destination),
                'depart'      => urlencode($depart),
                'trip-type'   => urlencode($tripType),
                'passengers'  => urlencode($passengers)
              ];
              if (!empty($returnDate)) {
                $params['return'] = urlencode($returnDate);
              }
              $query_string = http_build_query($params);

              echo '<tr>';
              // Format departure time to, for example, 12:00 AM
              echo '<td>' . htmlspecialchars(date("h:i A", strtotime($trip['depart_time']))) . '</td>';
              echo '<td>' . htmlspecialchars($trip['class']) . '</td>';
              echo '<td>' . htmlspecialchars($trip['seatcap']) . '</td>';
              echo '<td>₱' . number_format($trip['fareamount'], 2) . '</td>';
              echo '<td><a href="passenger.php?' . $query_string . '" class="book-button">Book</a></td>';
              echo '</tr>';
          }
      } else {
          echo '<tr><td colspan="5" style="text-align:center;">No trips available on this date</td></tr>';
      }
      $conn->close();
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
