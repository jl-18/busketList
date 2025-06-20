<?php
session_start();
require_once 'db_connect.php';

// Fetch bustypes
$bustypes = [];
$typeResult = $conn->query("SELECT bustypeid, description, seatcap FROM bustype");
while ($row = $typeResult->fetch_assoc()) {
    $bustypes[] = $row;
}

// Fetch routes
$routes = [];
$routeResult = $conn->query("SELECT routeid, origin, destination FROM routes");
while ($row = $routeResult->fetch_assoc()) {
    $routes[] = $row;
}

// Fetch buses with bustype info
$buses = [];
$busResult = $conn->query("
    SELECT 
        b.busid, 
        bt.bustypeid, 
        bt.description AS bus_type, 
        bt.seatcap 
    FROM bus b
    JOIN bustype bt ON b.bustypeid = bt.bustypeid
");
while ($row = $busResult->fetch_assoc()) {
    $buses[] = $row;
}

$successMessage = "";
$errorMessage = "";

// Handle Add Bus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bus'])) {
    $bustypeid = $_POST['bustype'] ?? null;
    $routeid = $_POST['routeid'] ?? null;

    if (!$bustypeid || !$routeid) {
        $errorMessage = "Error: Please select both a bus type and route before submitting.";
    } else {
        $typeQuery = $conn->prepare("SELECT description FROM bustype WHERE bustypeid = ?");
        $typeQuery->bind_param("i", $bustypeid);
        $typeQuery->execute();
        $typeQuery->bind_result($typeDesc);
        $typeQuery->fetch();
        $typeQuery->close();

        $insertQuery = $conn->prepare("INSERT INTO bus (bustypeid, routeid) VALUES (?, ?)");
        $insertQuery->bind_param("ii", $bustypeid, $routeid);
        if ($insertQuery->execute()) {
            $successMessage = "New " . $typeDesc . " bus added successfully!";
        } else {
            $errorMessage = "Error: Failed to add bus. Please try again.";
        }
        $insertQuery->close();
    }
}

// Redirect logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_action'])) {
    $selectedBusID = $_POST['busID'] ?? null;
    $updateTarget = $_POST['update_target'] ?? null;

    $redirectMap = [
        "Schedule" => "schedMatrix.php",
        "Fare" => "fareMatrix.php",
        "Route" => "routeMatrix.php"
    ];

    if (isset($redirectMap[$updateTarget])) {
        header("Location: " . $redirectMap[$updateTarget] . "?busid=" . urlencode($selectedBusID));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Bus Records - Busket List</title>
  <link rel="stylesheet" href="styling/recordManagement.css?v=<?php echo time(); ?>">
  <style>
    html, body { height: auto; min-height: 100%; overflow-x: hidden; }
    nav ul li a { text-decoration: none; color: inherit; }
    nav ul li a:hover { color: #555; }
    .form-group { margin-bottom: 20px; }
    select, input[type="number"], input[type="text"] {
        padding: 6px;
        margin-top: 5px;
        width: 100%;
    }
    .notice {
        padding: 10px;
        text-align: center;
        margin-bottom: 15px;
        display: none;
    }
    #notice-success { background-color: #4CAF50; color: white; }
    #notice-error { background-color: #e74c3c; color: white; }
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

  <main>
    <?php if ($successMessage): ?>
      <div class="notice" id="notice-success"><?php echo $successMessage; ?></div>
      <script>
        setTimeout(() => document.getElementById("notice-success").style.display = 'none', 3000);
        document.getElementById("notice-success").style.display = 'block';
      </script>
    <?php elseif ($errorMessage): ?>
      <div class="notice" id="notice-error"><?php echo $errorMessage; ?></div>
      <script>
        setTimeout(() => document.getElementById("notice-error").style.display = 'none', 4000);
        document.getElementById("notice-error").style.display = 'block';
      </script>
    <?php endif; ?>

    <form method="POST" action="recordManagement.php">
      <div class="form-groups">
        <h1>Add a Bus</h1>
        <div class="form-group">
          <label for="bustype">Bus Type:</label>
          <select name="bustype" id="bustype" required>
            <option value="" selected disabled hidden>Select bus type</option>
            <?php foreach ($bustypes as $type): ?>
              <option value="<?php echo $type['bustypeid']; ?>">
                <?php echo $type['description']; ?> (<?php echo $type['seatcap']; ?> seats)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="routeid">Route:</label>
          <select name="routeid" id="routeid" required>
            <option value="" selected disabled hidden>Select a route</option>
            <?php foreach ($routes as $route): ?>
              <option value="<?php echo $route['routeid']; ?>">
                <?php echo strtoupper($route['origin']) . " → " . strtoupper($route['destination']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="submit-button" name="add_bus">Submit</button>
      </div>
    </form>

    <form method="POST" action="recordManagement.php">
      <div class="form-group">
        <h1>Update a Bus</h1>
        <div class="form-group">
          <label for="busID" class="required">Bus ID:</label>
          <select name="busID" id="busID" required onchange="populateBusInfo()">
            <option value="" selected disabled hidden>Select a Bus</option>
            <?php foreach ($buses as $bus): ?>
              <option value="<?php echo $bus['busid']; ?>"
                      data-bustypeid="<?php echo $bus['bustypeid']; ?>"
                      data-seatcap="<?php echo $bus['seatcap']; ?>">
                Bus #<?php echo $bus['busid']; ?> - <?php echo $bus['bus_type']; ?> (<?php echo $bus['seatcap']; ?> seats)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="bustype">Bus Type:</label>
          <select name="bustype" id="update-bustype" required>
            <option value="" selected disabled hidden>Select bus type</option>
            <?php foreach ($bustypes as $type): ?>
              <option value="<?php echo $type['bustypeid']; ?>">
                <?php echo $type['description']; ?> (<?php echo $type['seatcap']; ?> seats)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="seatcap" class="required">Seating Capacity:</label>
          <input type="number" id="update-seatcap" name="seatcap" readonly>
        </div>
        <div class="form-group">
          <label for="update_target">Choose what to update:</label>
          <select name="update_target" id="update_target" required>
            <option value="" disabled selected hidden>Select an option</option>
            <option value="Fare">Fare</option>
            <option value="Schedule">Schedule</option>
            <option value="Route">Route</option>
          </select>
        </div>
        <button type="submit" class="submit-button" name="update_action">Go to update</button>
      </div>
    </form>

    <a href="admin.php" class="back-link">Back to admin page</a>
  </main>
</header>

<footer id="about-section">
  <div class="footerBoxes">
    <div class="footerBox"><h3>Privacy Policy</h3><hr>
    <p>
      We are committed to protecting your privacy. We will only use the
      information we collect about you lawfully (in accordance with the
      Data Protection Act 1998). Please read on if you wish to learn more
      about our privacy policy.
    </p>
  </div>
    <div class="footerBox"><h3>Terms of Service</h3><hr>
    <p>
      By using our service, you agree to provide accurate booking
      information and comply with our travel and cancellation policies. We
      are not liable for delays or missed trips caused by user error or
      third-party issues.
    </p>
  </div>
    <div class="footerBox"><h3>Help & Support</h3><hr>
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

<script>
function populateBusInfo() {
  const select = document.getElementById("busID");
  const selected = select.options[select.selectedIndex];

  const bustypeid = selected.getAttribute("data-bustypeid");
  const seatcap = selected.getAttribute("data-seatcap");

  document.getElementById("update-bustype").value = bustypeid;
  document.getElementById("update-seatcap").value = seatcap;
}
</script>
</body>
</html>
