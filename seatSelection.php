<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Selection - Busket List</title>
    <link rel="stylesheet" href="styling/style2.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css?v=<?php echo time(); ?>">
</head>
<body>
    <header>
        <nav>
            <h1>Busket List</h1>
            <ul>
                <li>Home</li>
                <li>About</li>
            </ul>
        </nav>

        <div class="img-placeholder"></div>

        <section>
            <div class="book-steps">
                <p><span>Step 3: </span>Seat Selection</p>
            </div>
        </section>
    </header>

    <main>
        <?php
        // Retrieve ALL necessary data from POST (from passenger.php)
        $origin = htmlspecialchars(urldecode($_POST['originalOrigin'] ?? ''));
        $destination = htmlspecialchars(urldecode($_POST['originalDestination'] ?? ''));
        $depart = htmlspecialchars(urldecode($_POST['originalDepart'] ?? ''));
        $tripType = htmlspecialchars(urldecode($_POST['originalTripType'] ?? ''));
        $passengers = htmlspecialchars(urldecode($_POST['originalPassengers'] ?? '1'));
        $returnDate = htmlspecialchars(urldecode($_POST['originalReturnDate'] ?? ''));

        $selectedTime = htmlspecialchars(urldecode($_POST['selectedTime'] ?? ''));
        $selectedClass = htmlspecialchars(urldecode($_POST['selectedClass'] ?? ''));
        $selectedSeatsFromBookSelection = htmlspecialchars(urldecode($_POST['selectedSeats'] ?? '')); // available seats
        $selectedFare = htmlspecialchars(urldecode($_POST['selectedFare'] ?? ''));

        $firstName = htmlspecialchars(urldecode($_POST['firstName'] ?? ''));
        $middleName = htmlspecialchars(urldecode($_POST['middleName'] ?? ''));
        $lastName = htmlspecialchars(urldecode($_POST['lastName'] ?? ''));
        $email = htmlspecialchars(urldecode($_POST['email'] ?? ''));
        $mobileNo = htmlspecialchars(urldecode($_POST['mobileNo'] ?? ''));
        $fullAddress = htmlspecialchars(urldecode($_POST['fullAddress'] ?? ''));

        // --- Seat Data Logic ---
        // DUMMY DATA; change this once database is ready
        $busSeatData = [];
        $bookedSeatIds = ['B3', 'B7', 'B17', 'A2', 'A6', 'A10', 'A14', 'A18'];
        $selectedSeatIds = []; // This will be populated by JS

        for ($row = 0; $row < 2; $row++) { 
            for ($i = 1; $i <= 9; $i++) {
                $seatId = 'B' . ($row * 9 + $i);
                $status = in_array($seatId, $bookedSeatIds) ? 'booked' : 'available';
                $busSeatData[] = ['id' => $seatId, 'status' => $status];
            }   
        }

        $busSeatData[] = ['id' => '19', 'status' => 'available']; 

        for ($row = 0; $row < 2; $row++) { 
            for ($i = 1; $i <= 9; $i++) {
                $seatId = 'A' . ($row * 9 + $i);
                $status = in_array($seatId, $bookedSeatIds) ? 'booked' : 'available';
                $busSeatData[] = ['id' => $seatId, 'status' => $status];
            }
        }

        // Function to determine seat class based on status
        function getSeatClass($seat, $currentSelectedSeats) {
            $class = 'seat-item';
            if (in_array($seat['id'], $currentSelectedSeats)) {
                $class .= ' seat--selected';
            } elseif ($seat['status'] === 'booked') {
                $class .= ' seat--booked';
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

                <form id="seatSelectionForm" action="payment.php" method="POST">
                    <input type="hidden" name="originalOrigin" value="<?php echo htmlspecialchars($origin); ?>">
                    <input type="hidden" name="originalDestination" value="<?php echo htmlspecialchars($destination); ?>">
                    <input type="hidden" name="originalDepart" value="<?php echo htmlspecialchars($depart); ?>">
                    <input type="hidden" name="originalTripType" value="<?php echo htmlspecialchars($tripType); ?>">
                    <input type="hidden" name="originalPassengers" value="<?php echo htmlspecialchars($passengers); ?>">
                    <input type="hidden" name="originalReturnDate" value="<?php echo htmlspecialchars($returnDate); ?>">
                    <input type="hidden" name="selectedTime" value="<?php echo htmlspecialchars($selectedTime); ?>">
                    <input type="hidden" name="selectedClass" value="<?php echo htmlspecialchars($selectedClass); ?>">
                    <input type="hidden" name="selectedFare" value="<?php echo htmlspecialchars($selectedFare); ?>">
                    
                    <input type="hidden" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>">
                    <input type="hidden" name="middleName" value="<?php echo htmlspecialchars($middleName); ?>">
                    <input type="hidden" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="hidden" name="mobileNo" value="<?php echo htmlspecialchars($mobileNo); ?>">
                    <input type="hidden" name="fullAddress" value="<?php echo htmlspecialchars($fullAddress); ?>">
                    
                    <input type="hidden" name="selected_seats_ids" id="selectedSeatsInput" value="">


                    <div class="seat-map-grid-wrapper">
                        <div class="seat-rows-container">
                            <div class="seat-row seat-row-b">
                                <?php
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
                                $seat = $busSeatData[18];
                                $seat_class = getSeatClass($seat, $selectedSeatIds);
                                echo '<div class="' . $seat_class . '" data-seat-id="' . htmlspecialchars($seat['id']) . '" data-seat-status="' . htmlspecialchars($seat['status']) . '">';
                                echo '<span class="seat-id">' . htmlspecialchars($seat['id']) . '</span>';
                                echo '</div>';
                                ?>
                            </div>

                            <div class="seat-row seat-row-a">
                                <?php
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
                        <div class="legend-item"> <span>Fair:</span>₱<?php echo number_format((float)$selectedFare, 2); ?> </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="next-button">Proceed to Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="footerBoxes">
            <div class="footerBox">
                <h3>Privacy Policy</h3>
                <hr>
                <p>We are committed to protecting your privacy. We will only use the information we collect about you lawfully (in accordance with the Data Protection Act 1998). Please read on if you wish to learn more about our privacy policy.</p>
            </div>

            <div class="footerBox">
                <h3>Terms of Service</h3>
                <hr>
                <p>By using our service, you agree to provide accurate booking information and comply with our travel and cancellation policies. We are not liable for delays or missed trips caused by user error or third-party issues.</p>
            </div>

            <div class="footerBox">
                <h3>Help & Support</h3>
                <hr>
                <p>If you have any questions or need assistance, our support team is here to help. Contact us via email or visit our help center for answers to frequently asked questions.</p>
            </div>
        </div>
        <hr>
        <p class="copy-right">2025 Busket List</p> 
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seats = document.querySelectorAll('.seat-item');
            const selectedSeatsInput = document.getElementById('selectedSeatsInput');
            const totalPassengers = parseInt(<?php echo json_encode($passengers); ?>); 
            let selectedSeats = [];

            function updateSelectedSeatsInput() {
                selectedSeatsInput.value = selectedSeats.join(',');
            }

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