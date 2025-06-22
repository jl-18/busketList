<?php
session_start();
require_once 'db_connect.php';

function get_safe_param($key) {
    return htmlspecialchars(urldecode($_GET[$key] ?? ''));
}

// Query for Top 3 Most Travelled Routes
$topRoutes = [];

$query = "
    SELECT r.origin, r.destination, COUNT(*) AS frequency
    FROM booking b
    JOIN schedmatrix s ON b.schedid = s.schedid
    JOIN routes r ON s.routeid = r.routeid
    GROUP BY r.origin, r.destination
    ORDER BY frequency DESC
    LIMIT 3
";

$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topRoutes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Most Travelled Routes - Busket List</title>
  <link rel="stylesheet" href="styling/recordManagement.css?v=<?php echo time(); ?>" />
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
    #inline-datepicker {
      border: 1px solid #ccc;
      border-radius: 4px;
      padding: 10px;
      display: inline-block;
      margin-top: 10px;
    }
  </style>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
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
        <li><a href="history.php">Most Travelled Route</a></li>
        <li><a href="sales.php">Sales</a></li>
        <li><a href="transactionLogs.php">Transaction Logs</a></li>
      </ul>
    </nav>
  </section>

  <main>
    <div class="travel-container">
      <div class="travel-group calendar-group">
        <h2>Choose date</h2>
        <div id="inline-datepicker"></div>
      </div>

      <div class="travel-group routes-group">
        <h2>Top 3 Most Travelled Routes</h2>
        <div class="route-groups">
          <div class="route-group-title">
            <p>ROUTES</p>
            <p>FREQUENCY</p>
          </div>

          <?php if (count($topRoutes) > 0): ?>
            <?php foreach ($topRoutes as $route): ?>
              <div class="route-group">
                <p><?php echo strtoupper($route['origin']) . " to " . strtoupper($route['destination']); ?></p>
                <p><?php echo $route['frequency']; ?></p>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="route-group">
              <p colspan="2">No data available.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <a href="admin.php" class="back-link" style="margin-left: 50px;"> Back to admin page</a>
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

  <script>
    $(function () {
      $("#inline-datepicker").datepicker({
        onSelect: function (dateText, inst) {
          console.log("Selected date: " + dateText);
          // Future: Add AJAX or redirect here if you want to filter by selected date
        },
      });
    });
  </script>
</body>
</html>