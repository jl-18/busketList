<?php
session_start();
require_once 'db_connect.php';

function get_safe_param($key) {
    return htmlspecialchars(urldecode($_GET[$key] ?? ''));
}

// Retrieve GET parameters for date/month filtering and origin/destination filtering
$selected_date      = get_safe_param('date');
$selected_month     = get_safe_param('month');
$originFilter       = get_safe_param('origin');
$destinationFilter  = get_safe_param('destination');

// Build an array of filter conditions for the SQL WHERE clause
$filters = [];

if (!empty($selected_month)) {
    // Expecting month parameter in YYYY-MM format
    $monthStart = $selected_month . '-01'; 
    $monthEnd   = date("Y-m-t", strtotime($monthStart)); // Get last day of the month
    $filters[]  = "DATE(b.bookingdate) BETWEEN '" . $conn->real_escape_string($monthStart) . "' AND '" . $conn->real_escape_string($monthEnd) . "'";
} elseif (!empty($selected_date)) {
    // Filter by a single day (converted to MySQL date format YYYY-MM-DD)
    $date_mysql = date("Y-m-d", strtotime($selected_date));
    $filters[]  = "DATE(b.bookingdate) = '" . $conn->real_escape_string($date_mysql) . "'";
}

if (!empty($originFilter)) {
    $filters[] = "r.origin = '" . $conn->real_escape_string($originFilter) . "'";
}
if (!empty($destinationFilter)) {
    $filters[] = "r.destination = '" . $conn->real_escape_string($destinationFilter) . "'";
}

$condition = "";
if (count($filters) > 0) {
    $condition = "WHERE " . implode(" AND ", $filters);
}

// Query to compute total sales per route using the filters
$sales = [];
$query = "
    SELECT r.origin, r.destination, COUNT(b.passengerid) * f.fareamount AS total_sales
    FROM booking b
    JOIN schedmatrix s ON b.schedid = s.schedid
    JOIN bus bs ON s.busid = bs.busid
    JOIN farematrix f ON s.routeid = f.routeid AND bs.bustypeid = f.bustypeid
    JOIN routes r ON s.routeid = r.routeid
    $condition
    GROUP BY r.routeid, r.origin, r.destination, f.fareamount
    ORDER BY total_sales DESC
";

$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sales[] = $row;
    }
}

// Fetch distinct origins from routes for the filter dropdown
$origins = [];
$result_origins = $conn->query("SELECT DISTINCT origin FROM routes ORDER BY origin ASC");
if ($result_origins && $result_origins->num_rows > 0) {
    while ($row = $result_origins->fetch_assoc()) {
        $origins[] = $row['origin'];
    }
}

// Fetch distinct destinations from routes for the filter dropdown
$destinations = [];
$result_destinations = $conn->query("SELECT DISTINCT destination FROM routes ORDER BY destination ASC");
if ($result_destinations && $result_destinations->num_rows > 0) {
    while ($row = $result_destinations->fetch_assoc()) {
        $destinations[] = $row['destination'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sales Summary - Busket List</title>
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
    .month-button, .apply-filter-button {
      margin-top: 10px;
      padding: 8px 16px;
      background-color: #007BFF;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .month-button:hover, .apply-filter-button:hover {
      background-color: #0056b3;
    }
    .filter-group {
      margin-top: 20px;
      padding: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
      background-color: #f9f9f9;
      max-width: 300px; /* Narrower container */
    }
    .filter-group label {
      margin-right: 10px;
    }
    .filter-group select {
      width: 100%;
      max-width: 200px; /* Adjust as needed */
      box-sizing: border-box;
      padding: 6px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .route-group {
      display: flex;
      justify-content: space-between;
      padding: 8px;
      border-bottom: 1px solid #eee;
    }
    .route-group-title {
      display: flex;
      justify-content: space-between;
      font-weight: bold;
      padding: 10px 0;
      border-bottom: 2px solid #ccc;
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
        </ul>
      </nav>
    </section>
  </header>
  <main>
    <div class="travel-container">
      <!-- Date and Month Filtering -->
      <div class="travel-group calendar-group">
        <h2>Choose Date</h2>
        <div id="inline-datepicker"></div>
        <button id="monthView" class="month-button">Show Whole Month</button>
      </div>
      
      <!-- Origin & Destination Filters -->
      <div class="travel-group filter-group">
        <h2>Filter by Origin &amp; Destination</h2>
        <form method="GET" action="sales.php">
          <!-- Preserve the date or month parameters if already applied -->
          <?php if (!empty($selected_date)): ?>
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($selected_date); ?>">
          <?php endif; ?>
          <?php if (!empty($selected_month)): ?>
            <input type="hidden" name="month" value="<?php echo htmlspecialchars($selected_month); ?>">
          <?php endif; ?>
          <div>
            <label for="origin">Origin:</label>
            <select name="origin" id="origin">
              <option value="">-- All Origins --</option>
              <?php foreach ($origins as $origin): ?>
                <option value="<?php echo htmlspecialchars($origin); ?>" <?php if($origin === $originFilter) echo "selected"; ?>>
                  <?php echo ucfirst($origin); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="destination">Destination:</label>
            <select name="destination" id="destination">
              <option value="">-- All Destinations --</option>
              <?php foreach ($destinations as $dest): ?>
                <option value="<?php echo htmlspecialchars($dest); ?>" <?php if($dest === $destinationFilter) echo "selected"; ?>>
                  <?php echo ucfirst($dest); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="apply-filter-button">Apply Filter</button>
        </form>
      </div>
      
      <!-- Sales Summary -->
      <div class="travel-group routes-group">
        <h2>Summary of Sales</h2>
        <div class="route-groups">
          <div class="route-group-title">
            <p>ROUTES</p>
            <p>SALES</p>
          </div>
          <?php if (count($sales) > 0): ?>
            <?php foreach ($sales as $sale): ?>
              <div class="route-group">
                <p><?php echo strtoupper($sale['origin']) . " to " . strtoupper($sale['destination']); ?></p>
                <p>₱<?php echo number_format($sale['total_sales'], 2); ?></p>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="route-group">
              <p colspan="2">No sales data available.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <a href="admin.php" class="back-link" style="margin-left: 50px;">Back to admin page</a>
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
        <h3>Help &amp; Support</h3>
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
      // Initialize the datepicker widget
      $("#inline-datepicker").datepicker({
        onSelect: function (dateText, inst) {
          // Redirect to the daily view when a date is selected
          window.location.href = 'sales.php?date=' + encodeURIComponent(dateText);
        }
      });
      
      // "Show Whole Month" button functionality
      $("#monthView").click(function () {
        // Get the currently selected date from the datepicker
        let date = $("#inline-datepicker").datepicker("getDate");
        if (date) {
          let year = date.getFullYear();
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          // Redirect with the month parameter formatted as YYYY-MM
          window.location.href = "sales.php?month=" + year + "-" + month;
        } else {
          alert("Please select a date first to use its month for filtering.");
        }
      });
    });
  </script>
</body>
</html>
