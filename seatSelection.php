<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Selection - Busket List</title>
    <link rel="stylesheet" href="style2.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css?v=<?php echo time(); ?>">
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
                <p><span>Step 3: </span>Seat Selection</p>
            </div>
        </section>
    </header>

    <main>
        <?php
        // Retrieve ALL necessary data from POST (from passenger.php)
        // You still need htmlspecialchars for security, but add urldecode for display.
        $origin = htmlspecialchars(urldecode($_POST['originalOrigin'] ?? ''));
        $destination = htmlspecialchars(urldecode($_POST['originalDestination'] ?? ''));
        $depart = htmlspecialchars(urldecode($_POST['originalDepart'] ?? ''));
        $tripType = htmlspecialchars(urldecode($_POST['originalTripType'] ?? ''));
        $passengers = htmlspecialchars(urldecode($_POST['originalPassengers'] ?? '1'));
        $returnDate = htmlspecialchars(urldecode($_POST['originalReturnDate'] ?? ''));

        // Trip details from the previous step
        $selectedTime = htmlspecialchars(urldecode($_POST['selectedTime'] ?? ''));
        $selectedClass = htmlspecialchars(urldecode($_POST['selectedClass'] ?? ''));
        $selectedSeatsFromBookSelection = htmlspecialchars(urldecode($_POST['selectedSeats'] ?? '')); // This was available seats, not actual selected seats
        $selectedFare = htmlspecialchars(urldecode($_POST['selectedFare'] ?? ''));

        // Customer details (for confirmation or further processing)
        $firstName = htmlspecialchars($_POST['firstName'] ?? '');
        $lastName = htmlspecialchars($_POST['lastName'] ?? '');
        $email = htmlspecialchars($_POST['email'] ?? '');
        $mobileNo = htmlspecialchars($_POST['mobileNo'] ?? '');
        $fullAddress = htmlspecialchars($_POST['fullAddress'] ?? '');

        // --- Seat Data Logic ---
        // In a real application, 'status' would come from a database query
        // based on the specific trip ID.
        // For demonstration, let's create dummy seat data that resembles your image.
        $busSeatData = [];
        $seatLabels = ['B', 'B', 'A', 'A']; // Corresponds to the rows in your image
        $seatCounter = 1;

        // Simulate booking some seats for the example as per image_233c55.png
        $bookedSeatIds = ['B3', 'B7', 'B17', 'A2', 'A6', 'A10', 'A14', 'A18'];
        $selectedSeatIds = []; // This will be populated by JS

        // Generate seats for B rows (B1-B18)
        for ($row = 0; $row < 2; $row++) { // Two 'B' rows
            for ($i = 1; $i <= 9; $i++) {
                $seatId = 'B' . ($row * 9 + $i);
                $status = in_array($seatId, $bookedSeatIds) ? 'booked' : 'available';
                $busSeatData[] = ['id' => $seatId, 'status' => $status /* REMOVED: 'gender' => null */];
            }   
        }

        // Generate seat for 19 (single seat at end of A row)
        $busSeatData[] = ['id' => '19', 'status' => 'available' /* REMOVED: 'gender' => null */]; // Seat 19 is available in image

        // Generate seats for A rows (A1-A18)
        for ($row = 0; $row < 2; $row++) { // Two 'A' rows
            for ($i = 1; $i <= 9; $i++) {
                $seatId = 'A' . ($row * 9 + $i);
                $status = in_array($seatId, $bookedSeatIds) ? 'booked' : 'available';
                $busSeatData[] = ['id' => $seatId, 'status' => $status /* REMOVED: 'gender' => null */];
            }
        }


        // Function to determine seat class based on status and gender for display
        function getSeatClass($seat, $currentSelectedSeats) {
            $class = 'seat-item'; // Base class
            if (in_array($seat['id'], $currentSelectedSeats)) {
                $class .= ' seat--selected';
            } elseif ($seat['status'] === 'booked') {
                $class .= ' seat--booked';
                // REMOVED: gender-specific classes
            } else {
                $class .= ' seat--available';
            }
            return $class;
        }
        ?>

        <div class="book-selection">
            <div class="book-header">
                <div class="origin-destination">
                    <h1><?php echo strtoupper($origin); ?> <span class="arrow-separator">►</span> <?php echo strtoupper($destination); ?></h1>
                </div>
                <div class="book-info">
                    <p class="book-details">
                        <?php echo $depart ?: 'N/A'; ?> <span class="separator">|</span>
                        <?php echo $tripType ? ucwords(str_replace('-', ' ', $tripType)) : 'N/A'; ?> <span class="separator">|</span>
                        Total Passenger: <?php echo $passengers ?: '1'; ?>
                        <?php if (!empty($returnDate)) { echo '<span class="separator">|</span> Return Date: ' . htmlspecialchars($returnDate); } ?>
                    </p>
                    
                    <div class="book-details-schedule">
                        <p><span>Departure Time:</span> <?php echo $selectedTime; ?></p>
                        <p><span>Class:</span> <?php echo $selectedClass; ?></p> 
                        <p><span>Fare:</span> ₱<?php echo number_format((float)$selectedFare, 2); ?></p>
                    </div> 
                </div>
            </div>

            <div class="seat-selection-container">
                <div class="seat-instruction-text">
                    <p>Click on available seats to reserve your seat.</p>
                </div>

                <form id="seatSelectionForm" action="processPayment.php" method="POST">
                    <input type="hidden" name="originalOrigin" value="<?php echo htmlspecialchars(urlencode($origin)); ?>">
                    <input type="hidden" name="originalDestination" value="<?php echo htmlspecialchars(urlencode($destination)); ?>">
                    <input type="hidden" name="originalDepart" value="<?php echo htmlspecialchars(urlencode($depart)); ?>">
                    <input type="hidden" name="originalTripType" value="<?php echo htmlspecialchars(urlencode($tripType)); ?>">
                    <input type="hidden" name="originalPassengers" value="<?php echo htmlspecialchars(urlencode($passengers)); ?>">
                    <input type="hidden" name="originalReturnDate" value="<?php echo htmlspecialchars(urlencode($returnDate)); ?>">
                    <input type="hidden" name="selectedTime" value="<?php echo htmlspecialchars(urlencode($selectedTime)); ?>">
                    <input type="hidden" name="selectedClass" value="<?php echo htmlspecialchars(urlencode($selectedClass)); ?>">
                    <input type="hidden" name="selectedFare" value="<?php echo htmlspecialchars(urlencode($selectedFare)); ?>">
                    <input type="hidden" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>">
                    <input type="hidden" name="middleName" value="<?php echo htmlspecialchars($_POST['middleName'] ?? ''); ?>">
                    <input type="hidden" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="hidden" name="confirmEmail" value="<?php echo htmlspecialchars($_POST['confirmEmail'] ?? ''); ?>">
                    <input type="hidden" name="mobileNo" value="<?php echo htmlspecialchars($mobileNo); ?>">
                    <input type="hidden" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                    <input type="hidden" name="fullAddress" value="<?php echo htmlspecialchars($fullAddress); ?>">

                    <input type="hidden" name="selected_seats_ids" id="selectedSeatsInput" value="">


                    <div class="seat-map-grid-wrapper">
                        <div class="seat-rows-container">
                            <div class="seat-row seat-row-b">
                                <?php
                                // Row B1 to B9 (busSeatData indices 0-8)
                                for ($i = 0; $i < 9; $i++) {
                                    $seat = $busSeatData[$i];
                                    $seat_class = getSeatClass($seat, $selectedSeatIds);
                                    echo '<div class="' . $seat_class . '" data-seat-id="' . htmlspecialchars($seat['id']) . '" data-seat-status="' . htmlspecialchars($seat['status']) . '">';
                                    echo '<span class="seat-id">' . htmlspecialchars($seat['id']) . '</span>';
                                    echo '</div>';
                                }
                                ?>
                            </div>

                            <div class="seat-row seat-row-b">
                                <?php
                                // Row B10 to B18 (busSeatData indices 9-17)
                                for ($i = 9; $i < 18; $i++) {
                                    $seat = $busSeatData[$i];
                                    $seat_class = getSeatClass($seat, $selectedSeatIds);
                                    echo '<div class="' . $seat_class . '" data-seat-id="' . htmlspecialchars($seat['id']) . '" data-seat-status="' . htmlspecialchars($seat['status']) . '">';
                                    echo '<span class="seat-id">' . htmlspecialchars($seat['id']) . '</span>';
                                    echo '</div>';
                                }
                                ?>
                            </div>

                            <div class="seat-row seat-row-19">
                                <div class="empty-col-span-9"></div><?php
                                // Seat 19 (busSeatData index 18)
                                $seat = $busSeatData[18];
                                $seat_class = getSeatClass($seat, $selectedSeatIds);
                                echo '<div class="' . $seat_class . '" data-seat-id="' . htmlspecialchars($seat['id']) . '" data-seat-status="' . htmlspecialchars($seat['status']) . '">';
                                echo '<span class="seat-id">' . htmlspecialchars($seat['id']) . '</span>';
                                echo '</div>';
                                ?>
                            </div>

                            <div class="seat-row seat-row-a">
                                <?php
                                // Row A1 to A9 (busSeatData indices 19-27)
                                for ($i = 19; $i < 28; $i++) {
                                    $seat = $busSeatData[$i];
                                    $seat_class = getSeatClass($seat, $selectedSeatIds);
                                    echo '<div class="' . $seat_class . '" data-seat-id="' . htmlspecialchars($seat['id']) . '" data-seat-status="' . htmlspecialchars($seat['status']) . '">';
                                    echo '<span class="seat-id">' . htmlspecialchars($seat['id']) . '</span>';
                                    echo '</div>';
                                }
                                ?>
                            </div>

                            <div class="seat-row seat-row-a">
                                <?php
                                // Row A10 to A18 (busSeatData indices 28-36)
                                for ($i = 28; $i < 37; $i++) {
                                    $seat = $busSeatData[$i];
                                    $seat_class = getSeatClass($seat, $selectedSeatIds);
                                    echo '<div class="' . $seat_class . '" data-seat-id="' . htmlspecialchars($seat['id']) . '" data-seat-status="' . htmlspecialchars($seat['status']) . '">';
                                    echo '<span class="seat-id">' . htmlspecialchars($seat['id']) . '</span>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="seat-legend">
                        <div class="legend-item">
                            <div class="legend-square seat--available"></div> Available
                        </div>
                        <div class="legend-item">
                            <div class="legend-square seat--booked"></div> Booked
                        </div>
                        <div class="legend-item">
                            <div class="legend-square seat--selected"></div> Selected
                        </div>
                        <div class="legend-item"> <span>Fair:</span> 1600</div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="next-button">Proceed to Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seats = document.querySelectorAll('.seat-item');
            const selectedSeatsInput = document.getElementById('selectedSeatsInput');
            const totalPassengers = parseInt(<?php echo json_encode($passengers); ?>); // Get total passengers from PHP
            let selectedSeats = [];

            function updateSelectedSeatsInput() {
                selectedSeatsInput.value = selectedSeats.join(',');
            }

            // Add event listeners to seats for selection
            seats.forEach(seat => {
                if (seat.dataset.seatStatus !== 'booked') { // Only allow selection if not booked
                    seat.addEventListener('click', function() {
                        const seatId = this.dataset.seatId;

                        if (this.classList.contains('seat--selected')) {
                            // Deselect seat
                            this.classList.remove('seat--selected');
                            this.classList.add('seat--available');
                            selectedSeats = selectedSeats.filter(id => id !== seatId);
                        } else {
                            // Select seat, but only if not exceeding passenger count
                            if (selectedSeats.length < totalPassengers) {
                                this.classList.remove('seat--available');
                                this.classList.add('seat--selected');
                                selectedSeats.push(seatId);
                            } else {
                                alert('You can only select up to ' + totalPassengers + ' seat(s).');
                            }
                        }
                        updateSelectedSeatsInput();
                    });
                }
            });

            // Initialize the hidden input
            updateSelectedSeatsInput();
        });
    </script>
</body>
</html>