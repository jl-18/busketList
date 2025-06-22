<?php 
include 'db_connect.php';
session_start();
$locations = getUniqueLocations($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <!-- Your external pink theme CSS -->
  <link rel="stylesheet" href="styling/style.css" />
  <title>Busket List</title>
  <style>
    .error-message {
      color: red;
      margin-top: 10px;
      text-align: center;
    }
    nav ul li a {
      text-decoration: none;
      color: inherit;
    }
    nav ul li a:hover {
      color: #555;
    }
    /* -----------------------------
       Modal Styles (Consistent with your pink theme)
       ----------------------------- */
    .modal {
      display: none;
      position: fixed;
      z-index: 150;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      align-items: center;
      justify-content: center;
      background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
      background-color: #fff;
      padding: 20px;
      border: 1px solidrgb(167, 13, 64); /* pink border */
      border-radius: 6px;
      width: 90%;
      max-width: 500px;
      position: relative;
    }
    .close-button {
      position: absolute;
      top: 10px;
      right: 14px;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      color: #f06292;
    }
    .tab-buttons button {
      margin: 5px;
      padding: 8px 12px;
      cursor: pointer;
      background-color: transparent;
      border: 1px solid #f06292;
      color: #f06292;
      border-radius: 4px;
    }
    .tab-buttons button.active {
      background-color: #f06292;
      color: #fff;
      border: none;
    }
    .modal-form-group {
      margin: 10px 0;
    }
    .modal-submit-button {
      margin-top: 10px;
      padding: 8px 16px;
      background-color: #f06292;
      border: none;
      border-radius: 4px;
      color: #fff;
      cursor: pointer;
    }
    .modal-submit-button:hover {
      background-color: #ec407a;
    }
    /* Custom Delete Confirmation Modal */
    #deleteConfirmModal {
      display: none;
      position: fixed;
      z-index: 200;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      align-items: center;
      justify-content: center;
    }
    #deleteConfirmModal .modal-content {
      background-color: #fff;
      border: 1px solid #f06292;
      border-radius: 4px;
      padding: 20px;
      text-align: center;
      width: 90%;
      max-width: 400px;
    }
    /* Delete Success Modal */
    #deleteSuccessModal {
      display: none;
      position: fixed;
      z-index: 210;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      align-items: center;
      justify-content: center;
    }
    #deleteSuccessModal .modal-content {
      background-color: #ffeef8;
      border: 1px solid #f06292;
      border-radius: 4px;
      padding: 20px;
      text-align: center;
      width: 90%;
      max-width: 400px;
    }
  </style>
</head>
<body>
<header>
  <nav>
    <h1>Busket List</h1>
    <ul>
      <li><a href="hero.php">Home</a></li>
      <li><a href="#about-section">About</a></li>
    </ul>
  </nav>
  <section>
    <img src="/busketList/images/img1.jpg" alt="Bus travel image" />
    <div class="overlay"></div>
    <div class="header-text">
      <h1>Busket List</h1>
      <p>Ride your way through life's bucket list!</p>
    </div>
  </section>
</header>

<main>
  <div class="trip-info">
    <form id="tripForm" action="bookSelection.php" method="GET">
      <div class="trip-types">
        <input type="radio" id="one-way" name="trip-type" value="one-way" checked required />
        <label for="one-way">One-way</label>
      </div>

      <div class="trip-route">
        <div class="trip-origin">
          <select name="origin" id="origin" required>
            <option value="" selected disabled hidden>Origin</option>
            <?php foreach ($locations as $location): ?>
              <option value="<?php echo htmlspecialchars($location); ?>">
                  <?php echo htmlspecialchars($location); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="trip-destination">
          <select name="destination" id="destination" required>
            <option value="" selected disabled hidden>Destination</option>
          </select>
        </div>
      </div>

      <?php $conn->close(); ?>

      <div class="trip-schedules">
        <div class="trip-schedule">
          <label for="depart">Depart</label>
          <input type="date" id="depart" name="depart" required />
        </div>
      </div>

      <br />
      <div id="dateError" class="error-message"></div>
      <input type="submit" value="Submit" />
      <button type="button" id="manageBookingsBtn">Manage my bookings</button>
    </form>
  </div>
  <a href="index.html" class="back-link" style="margin-left: 50px;">Back to navigation page</a>
</main>

<footer id="about-section">
  <div class="footerBoxes">
    <div class="footerBox">
      <h3>Privacy Policy</h3>
      <hr>
      <p>
        We are committed to protecting your privacy. We will only use the information we collect about you lawfully (in accordance with the Data Protection Act 1998). Please read on if you wish to learn more about our privacy policy.
      </p>
    </div>
    <div class="footerBox">
      <h3>Terms of Service</h3>
      <hr>
      <p>
        By using our service, you agree to provide accurate booking information and comply with our travel and cancellation policies. We are not liable for delays or missed trips caused by user error or third-party issues.
      </p>
    </div>
    <div class="footerBox">
      <h3>Help &amp; Support</h3>
      <hr>
      <p>
        If you have any questions or need assistance, our support team is here to help. Contact us via email or visit our help center for answers to frequently asked questions.
      </p>
    </div>
  </div>
  <hr>
  <p class="copy-right">2025 Busket List</p>
</footer>

<!-- Manage Bookings Modal -->
<div id="manageBookingsModal" class="modal">
  <div class="modal-content">
    <span class="close-button">&times;</span>
    <h2>Manage my bookings</h2>
    <div class="tab-buttons">
      <button id="changeScheduleBtn" class="active">Change Schedule</button>
      <button id="seeInvoiceBtn">See invoice</button>
      <button id="deleteBookingBtn">Cancel Booking</button>
    </div>

    <!-- Change Schedule Form -->
    <form id="changeScheduleForm">
      <div class="modal-form-group">
        <label for="trip-ID-change">Trip ID:</label>
        <small>(No. from the invoice presented to you)</small>
        <input type="text" id="trip-ID-change" name="bookingid" required />
      </div>
      <p id="changeError" class="error-message" style="display: none;"></p>
      <button type="submit" class="modal-submit-button">Submit</button>
    </form>

    <!-- See Invoice Form -->
    <form id="seeInvoiceForm" style="display: none;">
      <div class="modal-form-group">
        <label for="trip-ID-invoice">Trip ID:</label>
        <small>(No. from the invoice presented to you)</small>
        <input type="text" id="trip-ID-invoice" name="bookingid" required />
      </div>
      <p id="invoiceError" class="error-message" style="display: none;"></p>
      <button type="submit" class="modal-submit-button">Submit</button>
    </form>
    
    <!-- Delete Booking Form -->
    <form id="deleteBookingForm" style="display: none;">
      <div class="modal-form-group">
        <label for="trip-ID-delete">Trip ID:</label>
        <small>(No. from the invoice presented to you)</small>
        <input type="text" id="trip-ID-delete" name="bookingid" required />
      </div>
      <p id="deleteError" class="error-message" style="display: none;"></p>
      <button type="submit" class="modal-submit-button">Cancel Booking</button>
    </form>
  </div>
</div>

<!-- Custom Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal">
  <div class="modal-content">
    <h2>Confirm Deletion</h2>
    <p>Are you sure you want to cancel your booking?<br>This cannot be undone.</p>
    <button id="confirmDeleteBtn" class="modal-submit-button">Yes, cancel booking</button>
    <button id="cancelDeleteBtn" class="modal-submit-button" style="margin-left: 10px;">Cancel</button>
  </div>
</div>

<!-- Delete Success Modal -->
<div id="deleteSuccessModal" class="modal">
  <div class="modal-content">
    <h2>Deletion Successful</h2>
    <p id="deleteSuccessMessage"></p>
    <button id="closeSuccessModalBtn" class="modal-submit-button">OK</button>
  </div>
</div>

<script>
  // Manage Bookings Modal controls
  const manageModal = document.getElementById("manageBookingsModal");
  const manageBtn = document.getElementById("manageBookingsBtn");
  const closeBtn = document.getElementsByClassName("close-button")[0];
  
  manageBtn.onclick = () => { manageModal.style.display = "flex"; };
  closeBtn.onclick = () => { manageModal.style.display = "none"; };
  window.onclick = event => {
    if (event.target == manageModal) {
      manageModal.style.display = "none";
    }
    if (event.target == deleteConfirmModal) {
      deleteConfirmModal.style.display = "none";
    }
    if (event.target == deleteSuccessModal) {
      deleteSuccessModal.style.display = "none";
    }
  };

  // Tab toggling for modal
  const changeScheduleBtn = document.getElementById('changeScheduleBtn');
  const seeInvoiceBtn = document.getElementById('seeInvoiceBtn');
  const deleteBookingBtn = document.getElementById('deleteBookingBtn');
  const changeScheduleForm = document.getElementById('changeScheduleForm');
  const seeInvoiceForm = document.getElementById('seeInvoiceForm');
  const deleteBookingForm = document.getElementById('deleteBookingForm');

  changeScheduleBtn.addEventListener('click', () => {
    changeScheduleBtn.classList.add('active');
    seeInvoiceBtn.classList.remove('active');
    deleteBookingBtn.classList.remove('active');
    changeScheduleForm.style.display = 'block';
    seeInvoiceForm.style.display = 'none';
    deleteBookingForm.style.display = 'none';
  });
  seeInvoiceBtn.addEventListener('click', () => {
    seeInvoiceBtn.classList.add('active');
    changeScheduleBtn.classList.remove('active');
    deleteBookingBtn.classList.remove('active');
    seeInvoiceForm.style.display = 'block';
    changeScheduleForm.style.display = 'none';
    deleteBookingForm.style.display = 'none';
  });
  deleteBookingBtn.addEventListener('click', () => {
    deleteBookingBtn.classList.add('active');
    changeScheduleBtn.classList.remove('active');
    seeInvoiceBtn.classList.remove('active');
    deleteBookingForm.style.display = 'block';
    changeScheduleForm.style.display = 'none';
    seeInvoiceForm.style.display = 'none';
  });

  // Change Schedule form validation
  document.getElementById("changeScheduleForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const bookingid = document.getElementById("trip-ID-change").value.trim();
    const error = document.getElementById("changeError");
    fetch("validate_bookingid.php?bookingid=" + encodeURIComponent(bookingid))
      .then(res => res.json())
      .then(data => {
        if (data.valid) {
          window.location.href = "reschedule.php?bookingid=" + bookingid;
        } else {
          error.style.display = "block";
          error.textContent = "Please enter a valid Trip ID.";
        }
      });
  });

  // See Invoice form validation
  document.getElementById("seeInvoiceForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const bookingid = document.getElementById("trip-ID-invoice").value.trim();
    const error = document.getElementById("invoiceError");
    fetch("validate_bookingid.php?bookingid=" + encodeURIComponent(bookingid))
      .then(res => res.json())
      .then(data => {
        if (data.valid) {
          window.location.href = "receipt.php?bookingid=" + bookingid;
        } else {
          error.style.display = "block";
          error.textContent = "Please enter a valid Trip ID.";
        }
      });
  });

  // Delete Booking form validation and trigger confirmation modal
  document.getElementById("deleteBookingForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const bookingid = document.getElementById("trip-ID-delete").value.trim();
    const error = document.getElementById("deleteError");
    fetch("validate_bookingid.php?bookingid=" + encodeURIComponent(bookingid))
      .then(res => res.json())
      .then(data => {
        if (data.valid) {
          // Store booking id and show confirmation modal
          window.bookingIdToDelete = bookingid;
          deleteConfirmModal.style.display = "flex";
        } else {
          error.style.display = "block";
          error.textContent = "Please enter a valid Trip ID.";
        }
      });
  });

  // Delete confirmation modal actions using AJAX
  const deleteConfirmModal = document.getElementById("deleteConfirmModal");
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
  const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
  const deleteSuccessModal = document.getElementById("deleteSuccessModal");
  const deleteSuccessMessage = document.getElementById("deleteSuccessMessage");
  const closeSuccessModalBtn = document.getElementById("closeSuccessModalBtn");

  confirmDeleteBtn.addEventListener("click", function(){
    fetch("delete_booking.php?bookingid=" + window.bookingIdToDelete)
      .then(response => response.text())
      .then(message => {
        deleteConfirmModal.style.display = "none";
        deleteSuccessMessage.textContent = message;
        deleteSuccessModal.style.display = "flex";
      })
      .catch(error => console.error("Deletion error:", error));
  });

  cancelDeleteBtn.addEventListener("click", function(){
    deleteConfirmModal.style.display = "none";
  });

  closeSuccessModalBtn.addEventListener("click", function(){
    deleteSuccessModal.style.display = "none";
    // Optionally refresh the page or update the UI.
  });

  // Load destination options based on origin selection
  document.getElementById("origin").addEventListener("change", function () {
    const origin = this.value;
    fetch("get_destinations.php?origin=" + encodeURIComponent(origin))
      .then(res => res.json())
      .then(data => {
        const destinationSelect = document.getElementById("destination");
        destinationSelect.innerHTML = '<option value="" selected disabled hidden>Destination</option>';
        data.forEach(destination => {
          const option = document.createElement("option");
          option.value = destination;
          option.textContent = destination;
          destinationSelect.appendChild(option);
        });
      });
  });

  // Disable past dates for depart
  document.getElementById("depart").setAttribute("min", new Date().toISOString().split("T")[0]);
</script>
</body>
</html>
