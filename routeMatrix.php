<?php
session_start();
require_once 'db_connect.php';

$addSuccess = $addError = $updateSuccess = $updateError = "";
$busID = $_GET['busid'] ?? null;
$busRoutes = [];

if ($busID) {
    $stmt = $conn->prepare("SELECT r.routeid, r.origin, r.destination, r.km_distance FROM bus b JOIN routes r ON b.routeid = r.routeid WHERE b.busid = ?");
    $stmt->bind_param("i", $busID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $busRoutes[] = $row;
    }
    $stmt->close();
}

// Add Route
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addRoute'])) {
    $origin = trim($_POST['origin'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $distance = $_POST['distance'] ?? '';

    if ($origin && $destination && is_numeric($distance)) {
        $checkStmt = $conn->prepare("SELECT * FROM routes WHERE origin = ? AND destination = ?");
        $checkStmt->bind_param("ss", $origin, $destination);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $addError = "This route already exists.";
        } else {
            $insertStmt = $conn->prepare("INSERT INTO routes (origin, destination, km_distance) VALUES (?, ?, ?)");
            $insertStmt->bind_param("ssi", $origin, $destination, $distance);
            if ($insertStmt->execute()) {
                $addSuccess = "Route successfully added.";
            } else {
                $addError = "Failed to add route.";
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    } else {
        $addError = "All fields are required and distance must be a number.";
    }
}

// Update Route
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateRoute'])) {
    $routeid = $_POST['routeid'] ?? '';
    $newOrigin = trim($_POST['new_origin'] ?? '');
    $newDestination = trim($_POST['new_destination'] ?? '');
    $newDistance = $_POST['new_distance'] ?? '';

    if ($routeid && $newOrigin && $newDestination && is_numeric($newDistance)) {
        $stmt = $conn->prepare("UPDATE routes SET origin = ?, destination = ?, km_distance = ? WHERE routeid = ?");
        $stmt->bind_param("ssii", $newOrigin, $newDestination, $newDistance, $routeid);
        if ($stmt->execute()) {
            $updateSuccess = "BusId $busID - RouteId $routeid was updated successfully.";
        } else {
            $updateError = "Update failed.";
        }
        $stmt->close();
    } else {
        $updateError = "All fields are required and distance must be a number.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Route Matrix - Busket List</title>
  <link rel="stylesheet" href="styling/recordManagement.css?v=<?php echo time(); ?>" />
  <style>
    html, body { height: auto; min-height: 100%; overflow-x: hidden; }
    .message-success { color: green; margin-bottom: 10px; }
    .message-error { color: red; margin-bottom: 10px; }
    nav ul li a { text-decoration: none; color: inherit; }
    nav ul li a:hover { color: #555; }
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

<div class="center-tab">Route Matrix</div>

<main>
  <form method="POST" action="routeMatrix.php?busid=<?php echo $busID; ?>">
    <div class="form-groups">
      <h1>Add a Route</h1>
      <?php if ($addSuccess) echo "<p class='message-success'>$addSuccess</p>"; ?>
      <?php if ($addError) echo "<p class='message-error'>$addError</p>"; ?>
      <div class="form-group">
        <label for="origin" class="required">Origin:</label>
        <input type="text" id="origin" name="origin" required>
      </div>
      <div class="form-group">
        <label for="destination" class="required">Destination:</label>
        <input type="text" id="destination" name="destination" required>
      </div>
      <div class="form-group">
        <label for="distance" class="required">Distance (in km):</label>
        <input type="number" id="distance" name="distance" required>
      </div>
      <button type="submit" name="addRoute" class="submit-button">Submit</button>
    </div>
  </form>

  <form method="POST" action="routeMatrix.php?busid=<?php echo $busID; ?>">
    <div class="form-groups">
      <h1>Update a Route</h1>
      <?php if ($updateSuccess) echo "<p class='message-success'>$updateSuccess</p>"; ?>
      <?php if ($updateError) echo "<p class='message-error'>$updateError</p>"; ?>
      <?php if ($busID): ?>
        <p><strong>For Bus #<?php echo htmlspecialchars($busID); ?></strong></p>
      <?php endif; ?>
      <div class="form-group">
        <label for="routeid">Select Route:</label>
        <select name="routeid" required>
          <option value="" disabled selected>Select a route</option>
          <?php foreach ($busRoutes as $route): ?>
            <option value="<?php echo $route['routeid']; ?>">
              <?php echo $route['origin'] . " to " . $route['destination']; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="new_origin">New Origin:</label>
        <input type="text" name="new_origin" required>
      </div>
      <div class="form-group">
        <label for="new_destination">New Destination:</label>
        <input type="text" name="new_destination" required>
      </div>
      <div class="form-group">
        <label for="new_distance">New Distance:</label>
        <input type="number" name="new_distance" required>
      </div>
      <button type="submit" name="updateRoute" class="submit-button">Submit</button>
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
</footer>
</body>
</html>
