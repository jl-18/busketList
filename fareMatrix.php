<?php
session_start();
require_once 'db_connect.php';

$busID = $_GET['busid'] ?? null;
$busDetails = null;
$currentFare = null;
$routeOrigins = [];
$routeDestinations = [];
$notice = "";
$origin = $_POST['origin'] ?? null;
$destination = $_POST['destination'] ?? null;
$newFare = $_POST['fare'] ?? null;

if ($busID) {
    $busQuery = $conn->prepare("SELECT b.busid, bt.description, bt.seatcap, r.routeid, r.origin, r.destination, bt.bustypeid FROM bus b JOIN bustype bt ON b.bustypeid = bt.bustypeid JOIN routes r ON b.routeid = r.routeid WHERE b.busid = ?");
    $busQuery->bind_param("i", $busID);
    $busQuery->execute();
    $busDetails = $busQuery->get_result()->fetch_assoc();
    $busQuery->close();

    if ($busDetails) {
        $routeOrigins[] = $busDetails['origin'];
        $routeDestinations[] = $busDetails['destination'];
    }
}

if ($origin && $destination && $busID) {
    $getFareQuery = $conn->prepare("SELECT fm.fareamount FROM farematrix fm JOIN routes r ON fm.routeid = r.routeid JOIN bus b ON b.routeid = r.routeid AND b.bustypeid = fm.bustypeid WHERE r.origin = ? AND r.destination = ? AND b.busid = ?");
    $getFareQuery->bind_param("ssi", $origin, $destination, $busID);
    $getFareQuery->execute();
    $result = $getFareQuery->get_result();
    if ($row = $result->fetch_assoc()) {
        $currentFare = $row['fareamount'];
    }
    $getFareQuery->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $origin && $destination && $busID && $newFare !== null && $newFare !== '') {
    $fareQuery = $conn->prepare("SELECT fm.fareid FROM farematrix fm JOIN routes r ON fm.routeid = r.routeid JOIN bus b ON b.routeid = r.routeid AND b.bustypeid = fm.bustypeid WHERE r.origin = ? AND r.destination = ? AND b.busid = ?");
    $fareQuery->bind_param("ssi", $origin, $destination, $busID);
    $fareQuery->execute();
    $result = $fareQuery->get_result();
    if ($row = $result->fetch_assoc()) {
        $fareid = $row['fareid'];
        $updateQuery = $conn->prepare("UPDATE farematrix SET fareamount = ? WHERE fareid = ?");
        $updateQuery->bind_param("di", $newFare, $fareid);
        $updateQuery->execute();
        $updateQuery->close();
        $notice = "Fare updated successfully.";
        $currentFare = $newFare;
    }
    $fareQuery->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fare Matrix - Busket List</title>
  <link rel="stylesheet" href="styling/recordManagement.css?v=<?php echo time(); ?>">
  <style>
    html, body { height: auto; min-height: 100%; overflow-x: hidden; }
    nav ul li a { text-decoration: none; color: inherit; }
    nav ul li a:hover { color: #555; }
    footer { margin-top: 40px; }
    .form-groups { max-width: 500px; margin: 0 auto; text-align: left; }
    .form-group label { display: block; margin-bottom: 5px; }
    .form-group select, .form-group input[type="number"], .form-group input[type="text"] {
      width: 100%; padding: 8px; margin-bottom: 15px;
    }
    .submit-button { display: block; margin-top: 10px; padding: 10px 20px; }
    .notice { text-align: center; margin-top: 10px; color: green; }
      .center-tab {
        text-align: center;
        font-size: 1.3em;
        padding: 15px;
        background-color:rgba(255, 244, 27, 0.99);
        margin-top: -3px;
        font-weight: bold;
    }  
    .nav-links {
      display: flex;
      justify-content: center;
      gap: 40px;
      margin-top: 30px;
    }
    .nav-links a {
      text-decoration: none;
      color: #333;
    }
  </style>
</head>
<body>
<header>
  <nav class="main-navbar">
    <h1>Busket List</h1>
    <ul>
      <li><a href="index.html">Home</a></li>
      <li><a href="#about-section">About</a></li>
    </ul>
  </nav>
  <div class="img-placeholder"></div>
</header>

<div class="center-tab">Fare Matrix</div>

<main>
  <form method="POST">
    <div class="form-groups">
      <h1>Update Fare</h1>

      <?php if ($busDetails): ?>
        <p><strong>For Bus #<?php echo htmlspecialchars($busDetails['busid']); ?> - <?php echo htmlspecialchars($busDetails['description']); ?> (<?php echo htmlspecialchars($busDetails['seatcap']); ?> Seats)</strong></p>
      <?php endif; ?>

      <?php if (!empty($notice)): ?>
        <p class="notice"><?php echo $notice; ?></p>
      <?php endif; ?>

      <div class="form-group">
        <label for="origin">Origin:</label>
        <select name="origin" id="origin" required onchange="this.form.submit()">
          <option value="" disabled selected hidden>Select origin</option>
          <?php foreach ($routeOrigins as $o): ?>
            <option value="<?php echo $o; ?>" <?php if ($origin === $o) echo 'selected'; ?>><?php echo ucfirst($o); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="destination">Destination:</label>
        <select name="destination" id="destination" required onchange="this.form.submit()">
          <option value="" disabled selected hidden>Select destination</option>
          <?php foreach ($routeDestinations as $d): ?>
            <option value="<?php echo $d; ?>" <?php if ($destination === $d) echo 'selected'; ?>><?php echo ucfirst($d); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <?php if ($currentFare !== null): ?>
        <div class="form-group">
          <label>Current Fare:</label>
          <input type="text" value="₱<?php echo number_format($currentFare, 2); ?>" readonly />
        </div>
      <?php endif; ?>

      <div class="form-group">
        <label for="fare">New Fare:</label>
        <input type="number" step="0.01" id="fare" name="fare">
      </div>

      <button type="submit" class="submit-button">Submit</button>
      <div class="nav-links">
        <a href="recordManagement.php" class="back-link"> Back to Bus Records</a>
        <a href="admin.php" class="back-link"> Back to admin page</a>
      </div>
    </div>
  </form>
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
</body>
</html>
