<?php
session_start();
require_once 'db_connect.php';

$bookingid = $_GET['bookingid'] ?? '';
$booking = null;
$scheduleOptions = [];

if ($bookingid) {
    $stmt = $conn->prepare("SELECT b.bookingid, s.busid, r.origin, r.destination, s.schedid, s.scheddate, s.departtime FROM booking b JOIN schedmatrix s ON b.schedid = s.schedid JOIN routes r ON b.routeid = r.routeid WHERE b.bookingid = ?");
    $stmt->bind_param("s", $bookingid);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($booking) {
        $busid = $booking['busid'];
        $route = $booking['origin'];
        $destination = $booking['destination'];

        $schedStmt = $conn->prepare("SELECT s.schedid, s.scheddate, s.departtime FROM schedmatrix s JOIN routes r ON s.routeid = r.routeid WHERE s.busid = ? AND r.origin = ? AND r.destination = ?");
        $schedStmt->bind_param("iss", $busid, $route, $destination);
        $schedStmt->execute();
        $scheduleOptions = $schedStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $schedStmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reschedule - Busket List</title>
  <link rel="stylesheet" href="styling/style.css?v=<?php echo time(); ?>" />
  <style>
    html, body { height: auto; min-height: 100%; overflow-x: hidden; }
    nav ul li a { text-decoration: none; color: inherit; }
    nav ul li a:hover { color: #555; }
    .reschedule-container {
      max-width: 700px;
      margin: 80px auto;
      padding: 30px;
      border-radius: 20px;
      background-color: #f8f8f8;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    }
    h1 { text-align: center; color: #444; }
    .form-group { margin-bottom: 20px; }
    label { font-weight: bold; color: #444; display: block; margin-bottom: 6px; }
    select, input[type="text"], button {
      width: 100%; padding: 10px; border: 1px solid #bbb;
      border-radius: 8px; font-size: 1rem;
    }
    button {
      background-color: #364f6b; color: #fff;
      border: none; margin-top: 15px; cursor: pointer;
    }
    button:hover { background-color: #3e5e7d; }
    .current-booking {
      margin-bottom: 30px; padding: 15px;
      background-color: #eaeaea; border-radius: 10px;
    }
    .back-link {
      display: block; margin-top: 30px;
      text-align: center; text-decoration: none; color: #333;
    }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
<header>
  <nav class="main-navbar">
    <h1>Busket List</h1>
    <ul>
      <li><a href="hero.php">Home</a></li>
      <li><a href="#about-section">About</a></li>
    </ul>
  </nav>
  <div class="img-placeholder"></div>
</header>

<main>
  <div class="reschedule-container">
    <h1>Change Schedule</h1>

    <?php if ($booking): ?>
      <div class="current-booking">
        <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking['bookingid']); ?></p>
        <p><strong>Current Schedule:</strong> <?php echo $booking['scheddate'] . " at " . date("g:i A", strtotime($booking['departtime'])); ?></p>
        <p><strong>Route:</strong> <?php echo $booking['origin'] . " → " . $booking['destination']; ?></p>
      </div>

      <form method="POST" action="newReceipt.php">
        <input type="hidden" name="bookingid" value="<?php echo htmlspecialchars($booking['bookingid']); ?>" />

        <div class="form-group">
          <label for="schedid">Select New Schedule:</label>
          <select name="schedid" required>
            <option value="" disabled selected>Choose a new schedule</option>
            <?php foreach ($scheduleOptions as $option): ?>
              <?php if ($option['schedid'] != $booking['schedid']): ?>
                <option value="<?php echo $option['schedid']; ?>">
                  <?php echo $option['scheddate'] . " at " . date("g:i A", strtotime($option['departtime'])); ?>
                </option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit">Confirm New Schedule</button>
      </form>
    <?php else: ?>
      <p style="text-align: center;">Booking not found or invalid Booking ID.</p>
    <?php endif; ?>

    <a href="hero.php" class="back-link"> Back to Home</a>
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
