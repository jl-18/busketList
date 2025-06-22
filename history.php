<?php
session_start();
require_once 'db_connect.php';

function get_safe_param($key) {
    return htmlspecialchars(urldecode($_GET[$key] ?? ''));
}

$selected_date  = get_safe_param('date');
$selected_month = get_safe_param('month');

$topRoutes = [];
$filterCondition = "";

if (!empty($selected_month)) {
    $start = $selected_month . "-01";
    $end = date("Y-m-t", strtotime($start));
    $filterCondition = "WHERE DATE(b.bookingdate) BETWEEN '$start' AND '$end'";
} elseif (!empty($selected_date)) {
    $filterCondition = "WHERE DATE(b.bookingdate) = '$selected_date'";
}

$query = "
    SELECT r.origin, r.destination, COUNT(*) AS frequency
    FROM booking b
    JOIN schedmatrix s ON b.schedid = s.schedid
    JOIN routes r ON s.routeid = r.routeid
    $filterCondition
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
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  <style>
    html, body {
      height: auto;
      min-height: 100%;
      overflow-x: hidden;
      margin: 0;
      padding: 0;
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
    .calendar-group {
      background-color: #f9f9f9;
      padding: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin: 20px;
      width: 280px;
    }
    #inline-datepicker {
      border: 1px solid #ccc;
      border-radius: 4px;
      padding: 10px;
      display: inline-block;
      margin-top: 10px;
    }
    .month-button {
      margin-top: 10px;
      padding: 8px 16px;
      background-color: #007BFF;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .month-button:hover {
      background-color: #0056b3;
    }
    .travel-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
    }
    .routes-group {
      background-color: #f9f9f9;
      padding: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin: 20px;
      min-width: 300px;
      flex: 1;
    }
    .route-groups {
      margin-top: 15px;
    }
    .route-group-title {
      display: flex;
      justify-content: space-between;
      font-weight: bold;
      padding: 10px 0;
      border-bottom: 2px solid #ccc;
    }
    .route-group {
      display: flex;
      justify-content: space-between;
      padding: 8px;
      border-bottom: 1px solid #eee;
    }
    .back-link {
      display: inline-block;
      margin-left: 50px;
      margin-top: 20px;
      text-decoration: none;
      color: red;
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
        <li><a href="history.php">Most Travelled Route</a></li>
        <li><a href="sales.php">Sales</a></li>
        <li><a href="transactionLogs.php">Transaction Logs</a></li>
      </ul>
    </nav>
  </section>
</header>

<main>
  <div class="travel-container">
    <div class="travel-group calendar-group">
      <h2>Choose Date</h2>
      <div id="inline-datepicker"></div>
      <button id="monthView" class="month-button">Show Whole Month</button>
    </div>

    <div class="travel-group routes-group">
      <h2>Top 3 Most Travelled Routes</h2>
      
      <?php if (!empty($selected_month)): ?>
        <p>Showing Top Routes for: <strong><?php echo date("F Y", strtotime($selected_month)); ?></strong></p>
      <?php elseif (!empty($selected_date)): ?>
        <p>Showing Top Routes for: <strong><?php echo date("F j, Y", strtotime($selected_date)); ?></strong></p>
      <?php endif; ?>

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

<script>
  $(function () {
    $("#inline-datepicker").datepicker({
      dateFormat: "yy-mm-dd",
      onSelect: function (dateText) {
        window.location.href = 'history.php?date=' + encodeURIComponent(dateText);
      }
    });

    $("#monthView").click(function () {
      let date = $("#inline-datepicker").datepicker("getDate");
      if (date) {
        let year = date.getFullYear();
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        window.location.href = "history.php?month=" + year + "-" + month;
      } else {
        alert("Please select a date first to use its month.");
      }
    });
  });
</script>
</body>
</html>
