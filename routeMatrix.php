<?php
session_start();
require_once 'db_connect.php';

$addSuccess = $addError = "";

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $origin = trim($_POST['origin'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $distance = $_POST['distance'] ?? '';

    if ($origin && $destination && is_numeric($distance)) {
        // Check if route already exists
        $checkStmt = $conn->prepare("SELECT * FROM routes WHERE origin = ? AND destination = ?");
        $checkStmt->bind_param("ss", $origin, $destination);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $addError = "This route already exists.";
        } else {
            // Insert route
            $insertStmt = $conn->prepare("INSERT INTO routes (origin, destination, distance) VALUES (?, ?, ?)");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Route Matrix - Busket List</title>
  <link rel="stylesheet" href="styling/recordManagement.css?v=<?php echo time(); ?>" />
  <style>
    html, body {
      height: auto;
      min-height: 100%;
      overflow-x: hidden;
    }
    .message-success {
      color: green;
      margin-bottom: 10px;
    }
    .message-error {
      color: red;
      margin-bottom: 10px;
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
<header>
  <nav class="main-navbar">
    <h1>Busket List</h1>
    <ul>
      <li><a href="index.html">Home</a></li>
      <li><a href="#about-section">About</a></li>
    </ul>
  </nav>
  <div class="img-placeholder"></div>
  <section>
    <nav class="admin-navbar">
      <ul>
        <li><a href="recordManagement.php">Bus Records</a></li>
        <li><a href="fareMatrix.php">Fare Matrix</a></li>
        <li><a href="schedMatrix.php">Schedule Matrix</a></li>
        <li><a href="routeMatrix.php">Route Matrix</a></li>
      </ul>
    </nav>
  </section>
</header>

<main>
  <form method="POST" action="routeMatrix.php">
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

      <button type="submit" class="submit-button">Submit</button>
      <a href="admin.php" class="back-link">Back to admin page</a>
    </div>
  </form>
</main>

<footer id="about-section">
  <div class="footerBoxes">
    <div class="footerBox">
      <h3>Privacy Policy</h3>
      <hr />
      <p>We are committed to protecting your privacy.</p>
      <p>
        We are committed to protecting your privacy. We will only use the
        information we collect about you lawfully (in accordance with the
        Data Protection Act 1998). Please read on if you wish to learn more
        about our privacy policy.
    </p>
    </div>
    <div class="footerBox">
      <h3>Terms of Service</h3>
      <hr />
      <p>
        By using our service, you agree to provide accurate booking
        information and comply with our travel and cancellation policies. We
        are not liable for delays or missed trips caused by user error or
        third-party issues.
    </p>
    </div>
    <div class="footerBox">
      <h3>Help & Support</h3>
      <hr />
      <p>
        If you have any questions or need assistance, our support team is
        here to help. Contact us via email or visit our help center for
        answers to frequently asked questions.
        </p>
    </div>
  </div>
  <hr />
  <p class="copy-right">2025 Busket List</p>
</footer>
</body>
</html>