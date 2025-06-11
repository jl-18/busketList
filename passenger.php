<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="style2.css"> 
</head>
<body>
    <header>
        <nav>
            <h1>Busket List</h1>
            <ul>
                <li>Home</li>
                <li>Services</li>
                <li>About</li>
            </ul>
        </nav>

        <div>pic placeholder</div>

        <section>
            <div class="book-steps">
                <p><span>Step 2: </span>Passenger Information</p>
            </div>
        </section>

    </header>

    <main>
        <div class="customer-form-container">
            <form action="seatSelection.php" method="POST" class="customer-info-form">
                <?php
                // Retrieve trip details from URL parameters passed from bookSelection.php
                $time = htmlspecialchars($_GET['time'] ?? '');
                $class = htmlspecialchars($_GET['class'] ?? '');
                $seats = htmlspecialchars($_GET['seats'] ?? '');
                $fare = htmlspecialchars($_GET['fare'] ?? '');
                ?>

                <input type="hidden" name="selectedTime" value="<?php echo $time; ?>">
                <input type="hidden" name="selectedClass" value="<?php echo $class; ?>">
                <input type="hidden" name="selectedSeats" value="<?php echo $seats; ?>">
                <input type="hidden" name="selectedFare" value="<?php echo $fare; ?>">

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
                    <a href="bookSelection.php" class="back-link"> Back to Step 1</a>
                    <button type="submit" class="next-button">Next</button>
                </div>
            </form>

        </div>

    
    </main>

    
</body>
</html>