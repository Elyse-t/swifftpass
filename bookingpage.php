<?php
session_start();
include('connection.php'); // Make sure $conn is your mysqli connection

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Default username
$username = '';

// Get trip information from URL parameters
$trip_id = $_GET['trip_id'] ?? '';
$price = $_GET['price'] ?? '';

// Fetch trip details from database
$trip_details = [];
$customer_details = [];

if (!empty($trip_id)) {
  // Fetch trip details
  $trip_stmt = $conn->prepare("
        SELECT t.*, b.plates_number, b.model, r.departure, r.destination, 
               r.delay_time, d.name as driver_name
        FROM trips t
        JOIN buses b ON t.bus_id = b.bus_id
        JOIN routes r ON t.route_id = r.route_id
        JOIN drivers d ON t.driver_id = d.driver_id
        WHERE t.trip_id = ?
    ");
  $trip_stmt->bind_param("i", $trip_id);
  $trip_stmt->execute();
  $trip_result = $trip_stmt->get_result();

  if ($trip_result->num_rows > 0) {
    $trip_details = $trip_result->fetch_assoc();
  }
  $trip_stmt->close();

  // Fetch customer details if they exist
  $customer_stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
  $customer_stmt->bind_param("i", $userId);
  $customer_stmt->execute();
  $customer_result = $customer_stmt->get_result();

  if ($customer_result->num_rows > 0) {
    $customer_details = $customer_result->fetch_assoc();
  }
  $customer_stmt->close();
}

/* Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $firstname = $_POST['firstName'];
  $lastname = $_POST['lastName'];
  $contact = $_POST['phone'];
  $email = $_POST['email'];
  $number_of_seats = $_POST['seatCount'];
  $total_amount = $price * $number_of_seats;
  $trip_id=$_GET['trip_id'];
  // Check if customer already exists
  $check_customer = $conn->prepare("SELECT customer_id FROM customers WHERE email = ?");
  $check_customer->bind_param("s", $email);
  $check_customer->execute();
  $customer_result = $check_customer->get_result();

  if ($customer_result->num_rows > 0) {
    // Use existing customer
    $customer = $customer_result->fetch_assoc();
    $customer_id = $customer['customer_id'];

    // Update customer details
    $update_customer = $conn->prepare("UPDATE customers SET firstname = ?, lastname = ?, contact = ? WHERE customer_id = ?");
    $update_customer->bind_param("sssi", $firstname, $lastname, $contact, $customer_id);
    $update_customer->execute();
    $update_customer->close();
  } else {
    // Create new customer
    $insert_customer = $conn->prepare("INSERT INTO customers (firstname, lastname, contact, email) VALUES (?, ?, ?, ?)");
    $insert_customer->bind_param("ssss", $firstname, $lastname, $contact, $email);
    $insert_customer->execute();
    $customer_id = $insert_customer->insert_id;
    $insert_customer->close();
  }

  // Insert booking into database
  $booking_stmt = $conn->prepare("INSERT INTO bookings (customer_id, trip_id, number_of_seats, booking_date) VALUES (?, ?, ?, NOW())");
  $booking_stmt->bind_param("iii", $customer_id, $trip_id, $number_of_seats);

  if ($booking_stmt->execute()) {
    $booking_success = true;
    $booking_id = $booking_stmt->insert_id;

    // Update available seats in the trip
    $update_seats = $conn->prepare("UPDATE trips SET available_seats = available_seats - ? WHERE trip_id = ?");
    $update_seats->bind_param("ii", $number_of_seats, $trip_id);
    $update_seats->execute();
    $update_seats->close();
  } else {
    $booking_error = "Error creating booking: " . $booking_stmt->error;
  }
  $booking_stmt->close();
}
*/
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SwiftPass | Complete Your Booking</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2c3e50;
      --secondary: #3498db;
      --success: #2ecc71;
      --warning: #f39c12;
      --danger: #e74c3c;
      --info: #1abc9c;
      --dark: #191e32;
      --light: #ecf0f1;
      --sidebar-width: 280px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #191e32ff 0%, #1a151fff 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #fff;
      overflow-x: hidden;
    }

    /* Sidebar Styling */
    .sidebar {
      width: var(--sidebar-width);
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      height: 100vh;
      position: fixed;
      transition: all 0.3s;
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      border-right: 1px solid rgba(255, 255, 255, 0.2);
    }

    .sidebar-brand {
      padding: 2rem 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      margin: -1px -1px 0 -1px;
    }

    .sidebar-brand h4 {
      margin: 0;
      font-weight: 700;
      display: flex;
      align-items: center;
      color: white;
    }

    .sidebar-brand i {
      margin-right: 12px;
      font-size: 1.8rem;
      color: var(--success);
    }

    .nav-container {
      padding: 1rem 0;
    }

    .sidebar .nav-link {
      color: var(--primary);
      padding: 1rem 1.5rem;
      margin: 0.3rem 1rem;
      border-radius: 12px;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      cursor: pointer;
      font-weight: 500;
      border: none;
      text-decoration: none;
    }

    .sidebar .nav-link:hover {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: white;
      transform: translateX(5px);
      box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }

    .sidebar .nav-link.active {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: white;
      box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
    }

    .sidebar .nav-link i {
      margin-right: 12px;
      width: 20px;
      text-align: center;
      font-size: 1.1rem;
    }

    /* Main Content */
    .main-content {
      margin-left: var(--sidebar-width);
      padding: 2rem;
      transition: all 0.3s;
      min-height: 100vh;
    }

    /* Header */
    .header {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 1.5rem 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      backdrop-filter: blur(10px);
    }

    .header h2 {
      margin: 0;
      color: var(--primary);
      font-weight: 700;
      font-size: 1.8rem;
    }

    .header p {
      margin: 0.5rem 0 0 0;
      color: #6c757d;
      font-size: 1rem;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .user-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 1.2rem;
      border: 3px solid var(--success);
    }

    /* Cards */
    .stat-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      border: none;
      height: 100%;
      backdrop-filter: blur(10px);
      border-left: 5px solid var(--secondary);
    }

    .stat-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    /* Table Containers */
    .table-container {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      margin-top: 2rem;
      backdrop-filter: blur(10px);
    }

    /* Booking Form */
    .booking-form {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
      backdrop-filter: blur(10px);
      color: #333;
    }

    /* Bus Info Card */
    .bus-info-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 1.5rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
      backdrop-filter: blur(10px);
      border-left: 5px solid var(--success);
      color: #333;
    }

    .bus-price {
      color: var(--success);
      font-weight: 700;
      font-size: 1.1rem;
    }

    .bus-departure {
      color: #6c757d;
      font-size: 0.85rem;
    }

    .bus-company {
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    /* Status Badge */
    .status-badge {
      padding: 0.5rem 1rem;
      border-radius: 50px;
      font-size: 0.8rem;
      font-weight: 600;
      display: inline-block;
      color: white !important;
    }

    .badge-available {
      background: linear-gradient(135deg, var(--success), #27ae60);
    }

    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      border: none;
      border-radius: 10px;
      padding: 0.75rem 2rem;
      font-weight: 600;
      transition: all 0.3s ease;
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
      color: white;
    }

    .btn-success {
      background: linear-gradient(135deg, var(--success), #27ae60);
      border: none;
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
      color: white;
    }

    /* Form Controls */
    .form-control,
    .form-select {
      border-radius: 10px;
      border: 2px solid #e9ecef;
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
      color: #333;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--secondary);
      box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
      color: #333;
    }

    /* Success Message */
    .success-message {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
      backdrop-filter: blur(10px);
      text-align: center;
      color: #333;
    }

    /* Badges */
    .badge {
      padding: 0.6rem 1rem;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.8rem;
    }

    .badge-success {
      background: linear-gradient(135deg, var(--success), #27ae60) !important;
    }

    .badge-warning {
      background: linear-gradient(135deg, var(--warning), #e67e22) !important;
    }

    .badge-danger {
      background: linear-gradient(135deg, var(--danger), #c0392b) !important;
    }

    .badge-info {
      background: linear-gradient(135deg, var(--info), #16a085) !important;
    }

    .badge-secondary {
      background: linear-gradient(135deg, #95a5a6, #7f8c8d) !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        width: 80px;
      }

      .sidebar-brand h4 span,
      .sidebar .nav-link span {
        display: none;
      }

      .sidebar .nav-link i {
        margin-right: 0;
        font-size: 1.3rem;
      }

      .sidebar .nav-link {
        padding: 1rem;
        justify-content: center;
      }

      .main-content {
        margin-left: 80px;
        padding: 1rem;
      }

      .header {
        padding: 1rem;
      }

      .stat-card {
        padding: 1.5rem;
      }

      .action-buttons {
        flex-direction: column;
      }
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 6px;
    }

    ::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.1);
    }

    ::-webkit-scrollbar-thumb {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      border-radius: 3px;
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-brand">
      <h4><i class="fas fa-bus"></i> <span>SwiftPass</span></h4>
    </div>
    <div class="nav-container">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="homepage.php" class="nav-link">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <!-- Removed Bookings link from sidebar -->
        <li class="nav-item">
          <a href="setting.php" class="nav-link">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="nav-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Log Out</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="main-content">
    <!-- Header -->
    <div class="header">
      <div>
        <h2>Complete Your Booking 🎫</h2>
        <p>Fill in your details to secure your seat</p>
      </div>
      <div class="user-info">
        <div class="text-end">
          <div class="fw-bold text-dark">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></div>
          <small class="text-muted">Passenger</small>
        </div>
        <div class="user-avatar"><?php echo substr($_SESSION['username'] ?? 'U', 0, 1); ?></div>
      </div>
    </div>

    <?php if (isset($booking_success) && $booking_success): ?>
      <!-- Success Message -->
      <div class="success-message">
        <div class="text-center">
          <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
          <h3 class="text-success">Booking Confirmed! ✅</h3>
          <p class="mb-3">Your booking has been successfully created.</p>
          <div class="alert alert-success">
            <strong>Booking ID:</strong> #BK<?php echo str_pad($booking_id, 5, '0', STR_PAD_LEFT); ?><br>
            <strong>Bus:</strong> <?php echo htmlspecialchars($trip_details['model'] ?? ''); ?> (<?php echo htmlspecialchars($trip_details['plates_number'] ?? ''); ?>)<br>
            <strong>Route:</strong> <?php echo htmlspecialchars($trip_details['departure'] ?? ''); ?> → <?php echo htmlspecialchars($trip_details['destination'] ?? ''); ?><br>
            <strong>Seats Booked:</strong> <?php echo $number_of_seats; ?><br>
            <strong>Total Amount:</strong> <?php echo number_format($total_amount); ?> FRW
          </div>
          <div class="mt-4">
            <a href="homepage.php" class="btn btn-primary me-2">
              <i class="fas fa-home me-2"></i>Back to Home
            </a>
            <a href="bookingpage.php?trip_id=<?php echo $trip_id; ?>&price=<?php echo $price; ?>" class="btn btn-outline-primary">
              <i class="fas fa-ticket-alt me-2"></i>Book Another Seat
            </a>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- Trip Information Card -->
      <?php if (!empty($trip_details)): ?>
        <div class="bus-info-card">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <h6 class="fw-bold text-primary mb-0"><?php echo htmlspecialchars($trip_details['model']); ?></h6>
            <span class="status-badge badge-available">
              <i class="fas fa-check-circle me-1"></i>Available
            </span>
          </div>

          <div class="mb-3">
            <p class="mb-2"><i class="fas fa-route me-2 text-muted"></i>Route: <span class="fw-semibold"><?php echo htmlspecialchars($trip_details['departure']); ?> → <?php echo htmlspecialchars($trip_details['destination']); ?></span></p>
            <p class="mb-2"><i class="far fa-clock me-2 text-muted"></i>Departure: <span class="fw-semibold"><?php echo date('M j, Y H:i', strtotime($trip_details['departure_datetime'])); ?></span></p>
            <p class="mb-2"><i class="fas fa-bus me-2 text-muted"></i>Bus: <span class="fw-semibold"><?php echo htmlspecialchars($trip_details['plates_number']); ?></span></p>
            <p class="mb-2"><i class="fas fa-user me-2 text-muted"></i>Driver: <span class="fw-semibold"><?php echo htmlspecialchars($trip_details['driver_name']); ?></span></p>
            <p class="mb-0"><i class="fas fa-chair me-2 text-muted"></i>Available Seats: <span class="fw-semibold"><?php echo $trip_details['available_seats']; ?></span></p>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
              <h5 class="text-success mb-0"><?php echo number_format($price); ?> FRW</h5>
              <small class="text-muted">per seat</small>
            </div>
            <div class="text-end">
              <small class="text-muted d-block">Ready for your journey</small>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Booking Form -->
      <div class="booking-form">
        <h5 class="mb-3 fw-semibold"><i class="fas fa-user me-2"></i> Passenger Information</h5>

        <?php if (isset($booking_error)): ?>
          <div class="alert alert-danger"><?php echo $booking_error; ?></div>
        <?php endif; ?>

      <form method="POST" class="row g-3" action="confirm_booking.php" id="bookingForm" novalidate>
    <div class="col-md-6">
        <label for="firstName" class="form-label">First Name</label>
        <input type="text" class="form-control" id="firstName" name="firstName"
            value="<?php echo htmlspecialchars($customer_details['firstname'] ?? ''); ?>" 
            required
            minlength="2"
            maxlength="50"
            pattern="[A-Za-z\s]+"
            oninput="validateFirstName()">
        <div class="invalid-feedback" id="firstNameError">
            Please enter a valid first name (letters and spaces only, 2-50 characters).
        </div>
    </div>
    
    <div class="col-md-6">
        <label for="lastName" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="lastName" name="lastName"
            value="<?php echo htmlspecialchars($customer_details['lastname'] ?? ''); ?>" 
            required
            minlength="2"
            maxlength="50"
            pattern="[A-Za-z\s]+"
            oninput="validateLastName()">
        <div class="invalid-feedback" id="lastNameError">
            Please enter a valid last name (letters and spaces only, 2-50 characters).
        </div>
    </div>
    
    <div class="col-md-6">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email" name="email"
            value="<?php echo htmlspecialchars($customer_details['email'] ?? ''); ?>" 
            required
            maxlength="100"
            pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
            oninput="validateEmail()">
        <div class="invalid-feedback" id="emailError">
            Please enter a valid email address (e.g., example@domain.com).
        </div>
    </div>
    
   <div class="col-md-6">
    <label for="phone" class="form-label">Phone Number</label>
    <input 
        type="tel" 
        class="form-control" 
        id="phone" 
        name="phone"
        value="<?php echo htmlspecialchars($customer_details['contact'] ?? ''); ?>" 
        required
        pattern="^(?:\+2507\d{8}|07[2389]\d{7})$"
        placeholder="e.g., 0781234567 or +250781234567"
        oninvalid="this.setCustomValidity('Please enter a valid Rwandan phone number starting with 078, 079, 072, 073, or +2507.')"
        oninput="this.setCustomValidity('')">
    <div class="invalid-feedback" id="phoneError">
        Please enter a valid Rwandan phone number (e.g., 0781234567 or +250781234567).
    </div>
</div>

    
    <div class="col-md-6">
        <label for="seatCount" class="form-label">Number of Seats</label>
        <select class="form-select" id="seatCount" name="seatCount" required onchange="validateSeats()">
            <?php for ($i = 1; $i <= min(5, $trip_details['available_seats'] ?? 5); $i++): ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?> Seat<?php echo $i > 1 ? 's' : ''; ?></option>
            <?php endfor; ?>
        </select>
        <div class="invalid-feedback" id="seatCountError">
            Please select a valid number of seats.
        </div>
        <div class="form-text">
            Maximum <?php echo min(5, $trip_details['available_seats'] ?? 5); ?> seats allowed per booking
        </div>
    </div>
    
    <div class="col-md-6">
        <label class="form-label">Total Amount</label>
        <div class="form-control bg-light">
            <strong id="totalAmount"><?php echo number_format($price); ?> FRW</strong>
            <small class="text-muted">(Calculated based on seats)</small>
        </div>
    </div>

    <!-- Hidden fields remain unchanged -->
    <input type="text" name="travel_date" id="" value="<?php echo date('M j, Y H:i', strtotime($trip_details['departure_datetime'])); ?>" hidden>
    <input type="text" name="price" id="" value="<?php echo $price; ?>" hidden>
    <input type="text" name="plate_nbr" id="" value="<?php echo htmlspecialchars($trip_details['plates_number']); ?>" hidden>
    <input type="text" name="bus_name" id="" value="<?php echo htmlspecialchars($trip_details['model']); ?>" hidden>
    <input type="text" name="route_name" id="" value="<?php echo htmlspecialchars($trip_details['departure']); ?> → <?php echo htmlspecialchars($trip_details['destination']); ?>" hidden>
    <input type="text" name="trip_id" id="" value="<?php echo $trip_id; ?>" hidden>
    
    <div class="col-12 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <a href="homepage.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Search
            </a>
            <button type="submit" class="btn btn-success px-4" id="submitBtn">
                <i class="fas fa-check me-2"></i> Confirm Booking
            </button>
        </div>
    </div>
</form>

      </div>
    <?php endif; ?>
  </div>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Calculate total amount based on seat selection
    const pricePerSeat = <?php echo $price; ?>;
    const seatCountSelect = document.getElementById('seatCount');
    const totalAmountElement = document.getElementById('totalAmount');

    function updateTotalAmount() {
      const seatCount = parseInt(seatCountSelect.value);
      const totalAmount = pricePerSeat * seatCount;
      totalAmountElement.textContent = new Intl.NumberFormat().format(totalAmount) + ' FRW';
    }

    seatCountSelect.addEventListener('change', updateTotalAmount);

    // Initialize total amount on page load
    document.addEventListener('DOMContentLoaded', updateTotalAmount);

// Validation functions
function validateFirstName() {
    const firstName = document.getElementById('firstName');
    const error = document.getElementById('firstNameError');
    
    if (firstName.validity.valid) {
        firstName.classList.remove('is-invalid');
        firstName.classList.add('is-valid');
        return true;
    } else {
        firstName.classList.remove('is-valid');
        firstName.classList.add('is-invalid');
        return false;
    }
}

function validateLastName() {
    const lastName = document.getElementById('lastName');
    const error = document.getElementById('lastNameError');
    
    if (lastName.validity.valid) {
        lastName.classList.remove('is-invalid');
        lastName.classList.add('is-valid');
        return true;
    } else {
        lastName.classList.remove('is-valid');
        lastName.classList.add('is-invalid');
        return false;
    }
}

function validateEmail() {
    const email = document.getElementById('email');
    const error = document.getElementById('emailError');
    
    // Additional custom email validation
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if (email.validity.valid && emailRegex.test(email.value)) {
        email.classList.remove('is-invalid');
        email.classList.add('is-valid');
        return true;
    } else {
        email.classList.remove('is-valid');
        email.classList.add('is-invalid');
        return false;
    }
}

function validatePhone() {
    const phone = document.getElementById('phone');
    const error = document.getElementById('phoneError');
    
    // Custom phone validation for Rwanda format
    const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,15}$/;
    
    if (phone.validity.valid && phoneRegex.test(phone.value.replace(/\s/g, ''))) {
        phone.classList.remove('is-invalid');
        phone.classList.add('is-valid');
        return true;
    } else {
        phone.classList.remove('is-valid');
        phone.classList.add('is-invalid');
        return false;
    }
}

function validateSeats() {
    const seatCount = document.getElementById('seatCount');
    const error = document.getElementById('seatCountError');
    
    if (seatCount.validity.valid) {
        seatCount.classList.remove('is-invalid');
        seatCount.classList.add('is-valid');
        return true;
    } else {
        seatCount.classList.remove('is-valid');
        seatCount.classList.add('is-invalid');
        return false;
    }
}

// Real-time validation for all fields
document.getElementById('firstName').addEventListener('blur', validateFirstName);
document.getElementById('lastName').addEventListener('blur', validateLastName);
document.getElementById('email').addEventListener('blur', validateEmail);
document.getElementById('phone').addEventListener('blur', validatePhone);
document.getElementById('seatCount').addEventListener('change', validateSeats);

// Form submission validation
document.getElementById('bookingForm').addEventListener('submit', function(event) {
    // Validate all fields
    const isFirstNameValid = validateFirstName();
    const isLastNameValid = validateLastName();
    const isEmailValid = validateEmail();
    const isPhoneValid = validatePhone();
    const isSeatsValid = validateSeats();
    
    // If any validation fails, prevent form submission
    if (!isFirstNameValid || !isLastNameValid || !isEmailValid || !isPhoneValid || !isSeatsValid) {
        event.preventDefault();
        event.stopPropagation();
        
        // Show all error messages
        if (!isFirstNameValid) validateFirstName();
        if (!isLastNameValid) validateLastName();
        if (!isEmailValid) validateEmail();
        if (!isPhoneValid) validatePhone();
        if (!isSeatsValid) validateSeats();
        
        // Scroll to first error
        const firstError = document.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});

// Initialize validation on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add Bootstrap validation styling
    const forms = document.querySelectorAll('.needs-validation');
});
  </script>

</body>

</html>