<?php
session_start();
include 'db_connect.php'; // Only needed here for the discount dropdown query

// Helper function to safely retrieve GET parameters.
function get_safe_param($key) {
    return htmlspecialchars(urldecode($_GET[$key] ?? ''));
}

// ----------------------------------------------------------------------
// If the page is reached via GET (when the user clicks "Book" on bookingSelection.php),
// merge the schedule-specific data into the session's 'trip' array.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !empty($_GET)) {
    $_SESSION['trip'] = array_merge($_SESSION['trip'] ?? [], [
        'sched_id' => get_safe_param('sched_id'),  // if passed
        'time'     => get_safe_param('time'),
        'class'    => get_safe_param('class'),
        'fare'     => get_safe_param('fare')
    ]);
}

// Query discount types to populate the discount drop-down.
$sql = "SELECT discounttypeid, description FROM discounttype";
$result = $conn->query($sql);

// ----------------------------------------------------------------------
// When the form is submitted, simply store the passenger details in the session.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $firstName   = trim($_POST['firstName'] ?? '');
    $middleName  = trim($_POST['middleName'] ?? '');
    $lastName    = trim($_POST['lastName'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $mobileNo    = trim($_POST['mobileNo'] ?? '');
    $fullAddress = trim($_POST['fullAddress'] ?? '');
    $discount    = trim($_POST['discount'] ?? '');

    // Optionally, generate a unique passenger ID (stored in the session) for reference.
    // This is now kept in the session only.
    $_SESSION['passenger_id'] = "P" . strtoupper(substr(md5(uniqid()), 0, 5));

    // Save all passenger details into the session.
    $_SESSION['passenger'] = [
        'firstName'   => $firstName,
        'middleName'  => $middleName,
        'lastName'    => $lastName,
        'email'       => $email,
        'mobileNo'    => $mobileNo,
        'fullAddress' => $fullAddress,
        'discount'    => $discount  // discount type id from the dropdown.
    ];

    // Redirect to the receipt page (or next step) where all session data will be displayed.
    header("Location: payment.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Information - Busket List</title>
    <link rel="stylesheet" href="styling/style2.css?v=<?php echo time(); ?>">
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
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <h1>Busket List</h1>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="#about-section">About</a></li>
            </ul>
        </nav>
        <div class="img-placeholder"></div>
        <section>
            <div class="book-steps">
                <p><span>Step 2: </span>Passenger Information</p>
            </div>
        </section>
    </header>

    <main>
        <div class="customer-form-container">
            <!-- (Optional: Display any errors if you add error handling) -->
            <form action="passenger.php" method="POST" class="customer-info-form">
                <div class="form-columns">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="firstName" class="required">First Name:</label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="middleName">Middle Name:</label>
                            <input type="text" id="middleName" name="middleName">
                        </div>
                        <div class="form-group">
                            <label for="lastName" class="required">Last Name:</label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                        <div class="form-group">
                            <label for="discount">Discount:</label>
                            <select name="discount" id="discount" required>
                                <option value="" selected disabled hidden id="select-hidden">Choose the appropriate discount</option>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Output each discount type option
                                        echo '<option value="' . $row['discounttypeid'] . '">' . htmlspecialchars($row['description']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-column">
                        <div class="form-group">
                            <label for="email" class="required">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="mobileNo" class="required">Mobile No.:</label>
                            <input type="tel" id="mobileNo" name="mobileNo" pattern="[0-9]{10,12}" title="Enter a valid mobile number (10-12 digits)" required>
                        </div>
                        <div class="form-group">
                            <label for="fullAddress" class="required">Full Address:</label>
                            <input type="text" id="fullAddress" name="fullAddress" required>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <!-- Link back to bookingSelection.php using the saved trip session data -->
                    <a href="bookSelection.php?<?php echo http_build_query($_SESSION['trip']); ?>" class="back-link">Back to Step 1</a>
                    <button type="submit" class="next-button">Next</button>
                </div>
            </form>
        </div>
    </main>
</body>
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
