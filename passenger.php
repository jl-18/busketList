<?php
session_start();

function get_safe_param($key) {
    return htmlspecialchars(urldecode($_GET[$key] ?? ''));
}

$_SESSION['trip'] = [
    'time' => get_safe_param('time'),
    'class' => get_safe_param('class'),
    'seats' => get_safe_param('seats'),
    'fare' => get_safe_param('fare'),
    'origin' => get_safe_param('origin'),
    'destination' => get_safe_param('destination'),
    'depart' => get_safe_param('depart'),
    'trip_type' => get_safe_param('trip-type'),
    'passengers' => get_safe_param('passengers'),
    'return' => get_safe_param('return'),
];
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
            <form action="seatSelection.php" method="POST" class="customer-info-form">
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
                            <select name="discount" id="discount">
                                <option value="" selected disabled hidden id="select-hidden">Choose the appropriate discount</option>
                                <option value="pwd">PWD</option>
                                <option value="senior">Senior Citizen</option>
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
                    <a href="bookSelection.php?<?php echo http_build_query($_SESSION['trip']); ?>" class="back-link"> Back to Step 1</a>
                    <button type="submit" class="next-button">Next</button>
                </div>
            </form>
        </div>
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
