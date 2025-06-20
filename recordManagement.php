<?php
session_start();
require_once 'db_connect.php'; // ✅ Connect to DB

$addSuccess = $addError = $updateSuccess = $updateError = "";

// Handle Add Bus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addBus'])) {
    $bustype = $_POST['add_bustype'] ?? '';
    $capacity = $_POST['add_seatingcap'] ?? '';

    if ($bustype && is_numeric($capacity) && $capacity > 0) {
        $stmt = $conn->prepare("INSERT INTO bus (class, capacity) VALUES (?, ?)");
        $stmt->bind_param("si", $bustype, $capacity);
        if ($stmt->execute()) {
            $addSuccess = "Bus successfully added.";
        } else {
            $addError = "Error adding bus: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $addError = "Please provide valid bus type and seating capacity.";
    }
}

// Handle Update Bus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateBus'])) {
    $busID = $_POST['update_busID'] ?? '';
    $bustype = $_POST['update_bustype'] ?? '';
    $capacity = $_POST['update_seatingcap'] ?? '';

    if ($busID && is_numeric($busID) && $bustype && is_numeric($capacity) && $capacity > 0) {
        $stmt = $conn->prepare("UPDATE bus SET class = ?, capacity = ? WHERE busid = ?");
        $stmt->bind_param("sii", $bustype, $capacity, $busID);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $updateSuccess = "Bus record updated.";
            } else {
                $updateError = "No bus found with that ID.";
            }
        } else {
            $updateError = "Error updating bus: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $updateError = "Please provide valid Bus ID, type, and seating capacity.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Bus Record Management - Busket List</title>
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
  <form method="POST" action="recordManagement.php">
    <div class="form-groups">
      <h1>Add a Bus</h1>
      <?php if ($addSuccess) echo "<p class='message-success'>$addSuccess</p>"; ?>
      <?php if ($addError) echo "<p class='message-error'>$addError</p>"; ?>

      <div class="form-group">
        <label for="add_bustype">Bus Type:</label>
        <select name="add_bustype" id="add_bustype" required>
          <option value="" selected disabled hidden>Select bus type</option>
          <option value="Royal Class">Royal Class</option>
          <option value="Deluxe">Deluxe</option>
          <option value="AC Express">AC Express</option>
        </select>
      </div>

      <div class="form-group">
        <label for="add_seatingcap" class="required">Seating Capacity:</label>
        <input type="number" id="add_seatingcap" name="add_seatingcap" required>
      </div>

      <button type="submit" name="addBus" class="submit-button">Submit</button>
    </div>
  </form>

  <form method="POST" action="recordManagement.php">
    <div class="form-groups">
      <h1>Update a Bus</h1>
      <?php if ($updateSuccess) echo "<p class='message-success'>$updateSuccess</p>"; ?>
      <?php if ($updateError) echo "<p class='message-error'>$updateError</p>"; ?>

      <div class="form-group">
        <label for="update_busID" class="required">Bus ID:</label>
        <input type="number" id="update_busID" name="update_busID" required>
      </div>

      <div class="form-group">
        <label for="update_bustype">Bus Type:</label>
        <select name="update_bustype" id="update_bustype" required>
          <option value="" selected disabled hidden>Select bus type</option>
          <option value="Royal Class">Royal Class</option>
          <option value="Deluxe">Deluxe</option>
          <option value="AC Express">AC Express</option>
        </select>
      </div>

      <div class="form-group">
        <label for="update_seatingcap" class="required">Seating Capacity:</label>
        <input type="number" id="update_seatingcap" name="update_seatingcap" required>
      </div>

      <button type="submit" name="updateBus" class="submit-button">Submit</button>
    </div>
  </form>

  <a href="admin.php" class="back-link">Back to admin page</a>
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