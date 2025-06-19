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
    <link rel="stylesheet" href="styling/recordManagement.css?v=<?php echo time(); ?>">
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
        <nav class="main-navbar">
            <h1>Busket List</h1>
            <ul>
                <li><a href="hero.php">Home</a></li>
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
            <form>
                <div class="form-groups">
                    <h1>Add a schedule</h1>
                    <div class="form-group">
                        <label for="origin">Origin:</label>
                            <select name="origin" id="origin">
                                <option value="" selected disabled hidden id="select-hidden">Select origin</option>
                                <option value="manila">Manila</option>
                                <option value="laguna">Laguna</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="destination">Destination:</label>
                            <select name="destination" id="destination">
                                <option value="" selected disabled hidden id="select-hidden">Select destination</option>
                                <option value="manila">Manila</option>
                                <option value="laguna">Laguna</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date" class="required">For date:</label>
                        <input type="date" id="date" name="fare" required>
                    </div>

                    <div class="form-group">
                        <label for="time" class="required">Time:</label>
                        <input type="time" id="time" name="time" required>
                    </div>
                    <button type="submit" class="submit-button">Submit</button>

                </div>

                <div class="form-groups">
                    <h1>Update a schedule</h1>
                    <div class="form-group">
                        <label for="schedule" class="required">Schedule ID:</label>
                        <input type="text" id="busID" name="schedule" required>
                    </div>

                    <div class="form-group">
                        <label for="date" class="required">New date:</label>
                        <input type="date" id="date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="time" class="required">New time:</label>
                        <input type="time" id="time" name="time" required>
                    </div>
                    <button type="submit" class="submit-button">Submit</button>
                    <a href="admin.php" class="back-link"> Back to admin page</a>
                </div>

            
            </form>
            
        </main>
    </header>

    <main>
        
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
