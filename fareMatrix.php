<?php
session_start();
require_once 'db_connect.php';

$fareSuccess = $fareError = "";

// Handle fare update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $origin = $_POST['origin'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $fare = $_POST['fare'] ?? '';

    if ($origin && $destination && is_numeric($fare)) {
        // Get routeid from routes table
        $stmt = $conn->prepare("SELECT routeid FROM routes WHERE origin = ? AND destination = ?");
        $stmt->bind_param("ss", $origin, $destination);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $route = $result->fetch_assoc();
            $routeid = $route['routeid'];

            // Update fare in schedmatrix
            $update = $conn->prepare("UPDATE schedmatrix SET fare = ? WHERE routeid = ?");
            $update->bind_param("di", $fare, $routeid);
            if ($update->execute()) {
                $fareSuccess = "Fare updated successfully.";
            } else {
                $fareError = "Failed to update fare.";
            }
            $update->close();
        } else {
            $fareError = "Route not found. Please make sure the origin and destination exist.";
        }

        $stmt->close();
    } else {
        $fareError = "Please provide valid origin, destination, and fare.";
    }
}

// Optional: use function if available
$locations = function_exists('getUniqueLocations') ? getUniqueLocations($conn) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fare Matrix - Busket List</title>
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
    <form method="POST" action="fareMatrix.php">
      <div class="form-groups">
        <h1>Update Fare</h1>
        <?php if ($fareSuccess) echo "<p class='message-success'>$fareSuccess</p>"; ?>
        <?php if ($fareError) echo "<p class='message-error'>$fareError</p>"; ?>

        <div class="form-group">
          <label for="origin">Origin:</label>
          <select name="origin" id="origin" required>
            <option value="" selected disabled>Select origin</option>
            <?php
            foreach ($locations as $loc) {
                echo "<option value=\"$loc\">" . ucfirst($loc) . "</option>";
            }
            ?>
          </select>
        </div>

        <div class="form-group">
          <label for="destination">Destination:</label>
          <select name="destination" id="destination" required>
            <option value="" selected disabled>Select destination</option>
            <?php
            foreach ($locations as $loc) {
                echo "<option value=\"$loc\">" . ucfirst($loc) . "</option>";
            }
            ?>
          </select>
        </div>

        <div class="form-group">
          <label for="fare" class="required">New Fare:</label>
          <input type="number" id="fare" name="fare" required>
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

