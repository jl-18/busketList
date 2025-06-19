<?php
session_start();
require_once 'db_connect.php';

$addSuccess = $addError = $updateSuccess = $updateError = "";

// Handle ADD Schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addSchedule'])) {
    $origin = $_POST['add_origin'] ?? '';
    $destination = $_POST['add_destination'] ?? '';
    $date = $_POST['add_date'] ?? '';
    $time = $_POST['add_time'] ?? '';
    $busid = $_POST['add_busid'] ?? '';

    if ($origin && $destination && $date && $time && is_numeric($busid)) {
        $stmt = $conn->prepare("SELECT routeid FROM routes WHERE origin = ? AND destination = ?");
        $stmt->bind_param("ss", $origin, $destination);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $routeid = $result->fetch_assoc()['routeid'];

            // ✅ Join to get bustype.description
            $busRes = $conn->query("
                SELECT bustype.description 
                FROM bus 
                JOIN bustype ON bus.bustypeid = bustype.bustypeid 
                WHERE bus.busid = $busid
            ");
            $busClass = ($busRes->num_rows > 0) ? $busRes->fetch_assoc()['description'] : '';

            if ($busClass) {
                $stmt2 = $conn->prepare("INSERT INTO schedmatrix (busid, routeid, depart_date, depart_time, class) VALUES (?, ?, ?, ?, ?)");
                $stmt2->bind_param("iisss", $busid, $routeid, $date, $time, $busClass);
                if ($stmt2->execute()) {
                    $addSuccess = "Schedule successfully added.";
                } else {
                    $addError = "Insert error: " . $stmt2->error;
                }
                $stmt2->close();
            } else {
                $addError = "Bus type not found.";
            }
        } else {
            $addError = "Route not found.";
        }
        $stmt->close();
    } else {
        $addError = "All fields are required.";
    }
}

// Handle UPDATE Schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateSchedule'])) {
    $schedid = $_POST['update_schedid'] ?? '';
    $date = $_POST['update_date'] ?? '';
    $time = $_POST['update_time'] ?? '';

    if ($schedid && $date && $time) {
        $stmt = $conn->prepare("UPDATE schedmatrix SET depart_date = ?, depart_time = ? WHERE schedid = ?");
        $stmt->bind_param("ssi", $date, $time, $schedid);
        if ($stmt->execute()) {
            $updateSuccess = $stmt->affected_rows > 0 ? "Schedule updated." : "No changes made or invalid Schedule ID.";
        } else {
            $updateError = "Update error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $updateError = "All fields are required.";
    }
}

// Load locations and buses
$locations = function_exists('getUniqueLocations') ? getUniqueLocations($conn) : [];

$buses = $conn->query("
    SELECT bus.busid, bustype.description 
    FROM bus 
    JOIN bustype ON bus.bustypeid = bustype.bustypeid
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Schedule Matrix - Busket List</title>
  <link rel="stylesheet" href="styling/recordManagement.css?v=<?php echo time(); ?>" />
  <style>
    html, body { height: auto; min-height: 100%; overflow-x: hidden; }
    .message-success { color: green; margin-bottom: 10px; }
    .message-error { color: red; margin-bottom: 10px; }
    nav ul li a { text-decoration: none; color: inherit; }
    nav ul li a:hover { color: #555; }
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
  <form method="POST" action="schedMatrix.php">
    <div class="form-groups">
      <h1>Add a Schedule</h1>
      <?php if ($addSuccess) echo "<p class='message-success'>$addSuccess</p>"; ?>
      <?php if ($addError) echo "<p class='message-error'>$addError</p>"; ?>

      <div class="form-group">
        <label for="add_origin">Origin:</label>
        <select name="add_origin" id="add_origin" required>
          <option value="" disabled selected>Select origin</option>
          <?php foreach ($locations as $loc) echo "<option value=\"$loc\">".ucfirst($loc)."</option>"; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="add_destination">Destination:</label>
        <select name="add_destination" id="add_destination" required>
          <option value="" disabled selected>Select destination</option>
          <?php foreach ($locations as $loc) echo "<option value=\"$loc\">".ucfirst($loc)."</option>"; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="add_date">For Date:</label>
        <input type="date" name="add_date" id="add_date" required>
      </div>

      <div class="form-group">
        <label for="add_time">Time:</label>
        <input type="time" name="add_time" id="add_time" required>
      </div>

      <div class="form-group">
        <label for="add_busid">Bus:</label>
        <select name="add_busid" id="add_busid" required>
          <option value="" disabled selected>Select bus</option>
          <?php while ($bus = $buses->fetch_assoc()) {
              echo "<option value=\"{$bus['busid']}\">Bus {$bus['busid']} - {$bus['description']}</option>";
          } ?>
        </select>
      </div>

      <button type="submit" name="addSchedule" class="submit-button">Submit</button>
    </div>
  </form>

  <form method="POST" action="schedMatrix.php">
    <div class="form-groups">
      <h1>Update a Schedule</h1>
      <?php if ($updateSuccess) echo "<p class='message-success'>$updateSuccess</p>"; ?>
      <?php if ($updateError) echo "<p class='message-error'>$updateError</p>"; ?>

      <div class="form-group">
        <label for="update_schedid">Schedule ID:</label>
        <input type="number" name="update_schedid" id="update_schedid" required>
      </div>

      <div class="form-group">
        <label for="update_date">New Date:</label>
        <input type="date" name="update_date" id="update_date" required>
      </div>

      <div class="form-group">
        <label for="update_time">New Time:</label>
        <input type="time" name="update_time" id="update_time" required>
      </div>

      <button type="submit" name="updateSchedule" class="submit-button">Submit</button>
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
