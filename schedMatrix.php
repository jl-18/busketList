<?php
session_start();
require_once 'db_connect.php';

$addSuccess = $addError = $updateSuccess = $updateError = "";
$busID = $_GET['busid'] ?? null;
$busDetails = null;
$origin = $destination = '';
$schedules = [];

if ($busID) {
    $busQuery = $conn->prepare("SELECT b.busid, bt.description, bt.seatcap, r.origin, r.destination, b.routeid FROM bus b JOIN bustype bt ON b.bustypeid = bt.bustypeid JOIN routes r ON b.routeid = r.routeid WHERE b.busid = ?");
    $busQuery->bind_param("i", $busID);
    $busQuery->execute();
    $busDetails = $busQuery->get_result()->fetch_assoc();
    $busQuery->close();

    if ($busDetails) {
        $origin = $busDetails['origin'];
        $destination = $busDetails['destination'];
    }
}

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

            $stmt2 = $conn->prepare("INSERT INTO schedmatrix (busid, routeid, scheddate, departtime) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("iiss", $busid, $routeid, $date, $time);
            if ($stmt2->execute()) {
                $addSuccess = "New schedule for Bus #$busid added.";
            } else {
                $addError = "Insert error: " . $stmt2->error;
            }
            $stmt2->close();
        } else {
            $addError = "Route not found.";
        }
        $stmt->close();
    } else {
        $addError = "All fields are required.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateSchedule'])) {
    $schedid = $_POST['update_schedid'] ?? '';
    $date = $_POST['update_date'] ?? '';
    $time = $_POST['update_time'] ?? '';

    if ($schedid && $date && $time) {
        $stmt = $conn->prepare("UPDATE schedmatrix SET scheddate = ?, departtime = ? WHERE schedid = ?");
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

if ($busID) {
    $schedStmt = $conn->prepare("SELECT schedid, scheddate, departtime FROM schedmatrix WHERE busid = ?");
    $schedStmt->bind_param("i", $busID);
    $schedStmt->execute();
    $schedResult = $schedStmt->get_result();
    while ($row = $schedResult->fetch_assoc()) {
        $schedules[] = $row;
    }
    $schedStmt->close();
}

$buses = $conn->query("SELECT bus.busid, bustype.description FROM bus JOIN bustype ON bus.bustypeid = bustype.bustypeid");
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

<div class="center-tab">Schedule Matrix</div>

<main>
  <form method="POST" action="schedMatrix.php?busid=<?php echo $busID; ?>">
    <div class="form-groups">
      <h1>Add a Schedule</h1>
      <?php if ($addSuccess) echo "<p class='message-success'>$addSuccess</p>"; ?>
      <?php if ($addError) echo "<p class='message-error'>$addError</p>"; ?>

      <div class="form-group">
        <label for="add_origin">Origin:</label>
        <input type="text" name="add_origin" id="add_origin" required value="<?php echo htmlspecialchars($origin); ?>" readonly>
      </div>

      <div class="form-group">
        <label for="add_destination">Destination:</label>
        <input type="text" name="add_destination" id="add_destination" required value="<?php echo htmlspecialchars($destination); ?>" readonly>
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
              $selected = ($bus['busid'] == $busID) ? 'selected' : '';
              echo "<option value=\"{$bus['busid']}\" $selected>Bus {$bus['busid']} - {$bus['description']}</option>";
          } ?>
        </select>
      </div>

      <button type="submit" name="addSchedule" class="submit-button">Submit</button>
    </div>
  </form>

  <form method="POST" action="schedMatrix.php?busid=<?php echo $busID; ?>">
    <div class="form-groups">
      <h1>Update a Schedule</h1>
      <?php if ($updateSuccess) echo "<p class='message-success'>$updateSuccess</p>"; ?>
      <?php if ($updateError) echo "<p class='message-error'>$updateError</p>"; ?>

      <?php if ($busDetails): ?>
        <p><strong>For Bus #<?php echo htmlspecialchars($busDetails['busid']); ?> - <?php echo htmlspecialchars($busDetails['description']); ?> (<?php echo htmlspecialchars($busDetails['seatcap']); ?> Seats)</strong></p>
      <?php endif; ?>

      <div class="form-group">
        <label for="update_schedid">Schedule ID:</label>
        <select name="update_schedid" id="update_schedid" required>
          <option value="" disabled selected>Select a schedule</option>
          <?php foreach ($schedules as $sched): ?>
            <option value="<?php echo $sched['schedid']; ?>">
              ID #<?php echo $sched['schedid']; ?> - <?php echo $sched['scheddate']; ?> @ <?php echo $sched['departtime']; ?>
            </option>
          <?php endforeach; ?>
        </select>
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

