<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sales Summary - Busket List</title>
  <link rel="stylesheet" href="styling/recordManagement.css?v=<?php echo time(); ?>" />
  <style>
    html, body { height: auto; min-height: 100%; overflow-x: hidden; }
    footer { margin-top: 40px; }
    nav ul li a { text-decoration: none; color: inherit; }
    nav ul li a:hover { color: #555; }
    #inline-datepicker {
      border: 1px solid #ccc;
      border-radius: 4px;
      padding: 10px;
      display: inline-block; 
      margin-top: 10px; 
    }

    .route-group-title {
      display: grid;
      grid-template-columns: 100px repeat(4, 1fr) 100px; /* First and last columns are 100px wide */
      align-items: center;
      width: 100%;
    }

    .route-group-title p {
      margin: 0;
      text-align: center; /* Default center alignment for middle items */
    }

    /* Optional: adjust text alignment for the first and last items */
    .route-group-title p:first-child {
      text-align: left;
    }

    .route-group-title p:last-child {
      text-align: right;
    }

        .transaction-group {
      display: grid;
      grid-template-columns: 100px repeat(4, 1fr) 100px; /* First and last columns are 100px wide */
      align-items: center;
      width: 100%;
    }

    .transaction-group p {
      margin: 0;
      text-align: center; /* Default center alignment for middle items */
    }

    /* Optional: adjust text alignment for the first and last items */
    .transaction-group p:first-child {
      text-align: left;
    }

    .transaction-group p:last-child {
      text-align: right;
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
      <div class="travel-group routes-group">
        <h2>Transaction Logs</h2>
        <div class="transaction-calendar">
            <div class="trip-schedule">
              <label for="date">Choose a date</label>
              <input type="date" id="date" name="date" required />
            </div>
        </div>
        <div class="route-groups">
          <div class="route-group-title">
            <p>Booking ID</p>
            <p>Passenger</p>
            <p>Origin</p>
            <p>Destination</p>
            <p>Bus Type</p>
            <p>Payment</p>
          </div>
          <div id="transaction-container">
            <div class="transaction-group">
              <!-- here, replace the values of p -->
              <p>Booking ID</p>
              <p>Passenger</p>
              <p>Origin</p>
              <p>Destination</p>
              <p>Bus Type</p>
              <p>Payment</p>
            </div>
          </div>
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
  $("#date").datepicker({
    dateFormat: "yy-mm-dd",  // Use text input for jQuery UI datepicker
    onSelect: function (dateText, inst) {
      console.log("Selected date: " + dateText);
      $.ajax({
        url: "filter_bookings.php",
        method: "GET",
        data: { date: dateText },
        success: function (response) {
          // Replace the content inside the transaction container
          $("#transaction-container").html(response);
        },
        error: function (xhr, status, error) {
          console.error("Error retrieving booking details: ", error);
        }
      });
    }
  });
});

</script>


  
</body>
</html>