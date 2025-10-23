<?php
session_start();
include('connection.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];


// Get data from POST
$passenger_name = $_POST['firstName'] . ' ' . $_POST['lastName'];
$passenger_phone = $_POST['phone'];
$passenger_email = $_POST['email'];
$number_of_seats = $_POST['seatCount'];
$bus_plaque = $_POST['plate_nbr'];
$bus_name = $_POST['bus_name'];
$route_name = $_POST['route_name'];
$price = $_POST['price'];
$travel_date = $_POST['travel_date'];

$total_amount = ($price * $number_of_seats);

// Fetch additional bus details to get trip_id
$trip_id = $_POST['trip_id'];
$bus_details = [];
if (!empty($bus_plaque)) {
    $stmt = $conn->prepare("SELECT * FROM buses WHERE plates_number = ?");
    $stmt->bind_param("s", $bus_plaque);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $bus_details = $result->fetch_assoc();
    }
    $stmt->close();
}

// Store booking details in session ONLY - don't create booking yet
$_SESSION['pending_booking'] = [
    'customer_id' => $userId,
    'trip_id' => $trip_id,
    'number_of_seats' => $number_of_seats,
    'passenger_name' => $passenger_name,
    'passenger_phone' => $passenger_phone,
    'passenger_email' => $passenger_email,
    'bus_plaque' => $bus_plaque,
    'bus_name' => $bus_name,
    'route_name' => $route_name,
    'travel_date' => $travel_date,
    'total_amount' => $total_amount,
    'price_per_seat' => $price
];

// Generate a temporary reference ID for this booking attempt
$temp_booking_ref = 'TEMP_' . time() . '_' . rand(1000, 9999);
$_SESSION['temp_booking_ref'] = $temp_booking_ref;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftPass | Confirm Booking & Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Your existing CSS remains the same */
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

        body {
            background: linear-gradient(135deg, #191e32ff 0%, #1a151fff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            overflow-x: hidden;
        }

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

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: all 0.3s;
            min-height: 100vh;
        }

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

        .booking-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
            color: #333;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
            border-left: 5px solid var(--secondary);
        }

        .payment-option {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            border-color: var(--secondary);
            transform: translateY(-2px);
        }

        .payment-option.selected {
            border-color: var(--success);
            background: rgba(46, 204, 113, 0.05);
        }

        .payment-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

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
        }

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
            <a href="homepage.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="bookingpage.php" class="nav-link active">
                <i class="fas fa-ticket-alt"></i>
                <span>Bookings</span>
            </a>
            <a href="setting.php" class="nav-link">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Log Out</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div>
                <h2>Confirm Booking & Payment 💳</h2>
                <p class="text-muted mb-0">Review your details and choose payment method</p>
            </div>
            <div class="user-info">
                <div class="text-end">
                    <div class="fw-bold text-dark">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></div>
                    <small class="text-muted">Passenger</small>
                </div>
                <div class="user-avatar"><?php echo substr($_SESSION['username'] ?? 'U', 0, 1); ?></div>
            </div>
        </div>

        <div class="booking-container">
            <div class="row">
                <!-- Passenger Information -->
                <div class="col-md-6">
                    <div class="info-card">
                        <h5 class="fw-bold text-primary mb-4"><i class="fas fa-user me-2"></i>Passenger Information</h5>
                        <div class="row">
                            <div class="col-6">
                                <strong>Full Name:</strong>
                                <p class="text-muted"><?php echo htmlspecialchars($passenger_name); ?></p>
                            </div>
                            <div class="col-6">
                                <strong>Phone:</strong>
                                <p class="text-muted"><?php echo htmlspecialchars($passenger_phone); ?></p>
                            </div>
                            <div class="col-12">
                                <strong>Email:</strong>
                                <p class="text-muted"><?php echo htmlspecialchars($passenger_email); ?></p>
                            </div>
                            <div class="col-6">
                                <strong>Seats:</strong>
                                <p class="text-muted"><?php echo $number_of_seats; ?> seat(s)</p>
                            </div>
                            <div class="col-6">
                                <strong>Total Amount:</strong>
                                <p class="text-success fw-bold"><?php echo number_format($total_amount); ?> FRW</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bus Information -->
                <div class="col-md-6">
                    <div class="info-card">
                        <h5 class="fw-bold text-primary mb-4"><i class="fas fa-bus me-2"></i>Bus Information</h5>
                        <div class="row">
                            <div class="col-6">
                                <strong>Bus Name:</strong>
                                <p class="text-muted"><?php echo htmlspecialchars($bus_name); ?></p>
                            </div>
                            <div class="col-6">
                                <strong>Plaque:</strong>
                                <p class="text-muted"><?php echo htmlspecialchars($bus_plaque); ?></p>
                            </div>
                            <div class="col-12">
                                <strong>Route:</strong>
                                <p class="text-muted"><?php echo htmlspecialchars($route_name); ?></p>
                            </div>
                            <div class="col-6">
                                <strong>Travel Date:</strong>
                                <p class="text-muted"><?php echo date('M j, Y', strtotime($travel_date)); ?></p>
                            </div>
                            <div class="col-12">
                                <strong>Price per Seat:</strong>
                                <p class="text-muted"><?php echo number_format($price); ?> FRW</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method Selection -->
            <div class="info-card">
                <h5 class="fw-bold text-primary mb-4"><i class="fas fa-credit-card me-2"></i>Choose Payment Method</h5>
                <form id="paymentForm">
                    <!-- Hidden fields -->
                    <input type="hidden" name="firstname" id="firstname" value="<?php echo htmlspecialchars($_POST['firstName']); ?>">
                    <input type="hidden" name="lastname" id="lastname" value="<?php echo htmlspecialchars($_POST['lastName']); ?>">
                    <input type="hidden" name="email" id="email" value="<?php echo htmlspecialchars($passenger_email); ?>">
                    <input type="hidden" name="user_id" id="user_id" value="<?php echo htmlspecialchars($userId); ?>">
                    <input type="hidden" name="trip_id" id="trip_id" value="<?php echo htmlspecialchars($trip_id); ?>">
                    <input type="hidden" name="nbr_of_seats" id="nbr_of_seats" value="<?php echo htmlspecialchars($number_of_seats); ?>">
                    <input type="hidden" name="phoneNumber" id="phoneNumber" value="<?php echo htmlspecialchars($passenger_phone); ?>">
                    <input type="hidden" name="amount" id="amount" value="<?php echo htmlspecialchars($total_amount); ?>">
                    <input type="hidden" name="tempBookingRef" id="tempBookingRef" value="<?php echo htmlspecialchars($temp_booking_ref); ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="payment-option" onclick="selectPayment('momo')">
                                <div class="payment-icon text-danger">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <h6 class="fw-bold">MoMo Pay</h6>
                                <p class="text-muted small">Pay with Mobile Money</p>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" value="momo" id="momo" required>
                                    <label class="form-check-label" for="momo">
                                        Select MoMo Pay
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="payment-option" onclick="selectPayment('airtel')">
                                <div class="payment-icon text-warning">
                                    <i class="fas fa-wifi"></i>
                                </div>
                                <h6 class="fw-bold">Airtel Money</h6>
                                <p class="text-muted small">Pay with Airtel Money</p>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" value="airtel" id="airtel" required>
                                    <label class="form-check-label" for="airtel">
                                        Select Airtel Money
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status Display -->
                    <div id="paymentStatus" class="mt-4" style="display: none;">
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-3" role="status" id="statusSpinner">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div>
                                    <h6 class="mb-1" id="statusTitle">Processing Payment</h6>
                                    <p class="mb-0 small" id="statusMessage">Initializing payment request...</p>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="statusProgress" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="bookingpage.php?bus_plaque=<?php echo $bus_plaque; ?>&route=<?php echo urlencode($route_name); ?>&price=<?php echo $price; ?>&bus_name=<?php echo urlencode($bus_name); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Edit
                            </a>
                            <button type="submit" class="btn btn-primary px-4" id="confirmBtn" disabled>
                                <i class="fas fa-lock me-2"></i> Confirm & Pay <?php echo number_format($total_amount); ?> FRW
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <style>
                .payment-option {
                    border: 2px solid #e9ecef;
                    border-radius: 10px;
                    padding: 20px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    margin-bottom: 15px;
                }

                .payment-option:hover {
                    border-color: #007bff;
                    background-color: #f8f9fa;
                }

                .payment-option.selected {
                    border-color: #007bff;
                    background-color: #e7f3ff;
                }

                .payment-icon {
                    font-size: 2rem;
                    margin-bottom: 10px;
                }

                .payment-status {
                    transition: all 0.3s ease;
                }

                .status-pending {
                    border-left: 4px solid var(--warning);
                }

                .status-processing {
                    border-left: 4px solid var(--info);
                }

                .status-success {
                    border-left: 4px solid var(--success);
                }

                .status-error {
                    border-left: 4px solid var(--danger);
                }

                .progress-bar {
                    transition: width 0.5s ease;
                }

                /* Ensure form check doesn't interfere with click */
                .form-check {
                    pointer-events: none;
                    margin-top: 10px;
                }
            </style>

            <script>
                // Debug: Check if all variables are loaded
                console.log('Payment Form Data:', {
                    user_id: document.getElementById('user_id').value,
                    trip_id: document.getElementById('trip_id').value,
                    nbr_of_seats: document.getElementById('nbr_of_seats').value,
                    phoneNumber: document.getElementById('phoneNumber').value,
                    amount: document.getElementById('amount').value,
                    tempBookingRef: document.getElementById('tempBookingRef').value,
                    firstname: document.getElementById('firstname').value,
                    lastname: document.getElementById('lastname').value,
                    email: document.getElementById('email').value
                });

                // Payment selection function
                function selectPayment(method) {
                    // Remove selected class from all options
                    document.querySelectorAll('.payment-option').forEach(option => {
                        option.classList.remove('selected');
                    });

                    // Add selected class to clicked option
                    const selectedOption = document.querySelector(`[onclick="selectPayment('${method}')"]`);
                    if (selectedOption) {
                        selectedOption.classList.add('selected');
                    }

                    // Check the radio button
                    document.getElementById(method).checked = true;

                    // Enable confirm button
                    document.getElementById('confirmBtn').disabled = false;

                    console.log('Selected payment method:', method);
                }

                // Add click handlers to payment options
                document.querySelectorAll('.payment-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const radio = this.querySelector('input[type="radio"]');
                        if (radio) {
                            selectPayment(radio.value);
                        }
                    });
                });

                // Main payment form handler
                document.getElementById('paymentForm').addEventListener('submit', async (e) => {
                    e.preventDefault();

                    // Get all form values
                    const user_id = document.getElementById('user_id').value.trim();
                    const trip_id = document.getElementById('trip_id').value.trim();
                    const nbr_of_seats = document.getElementById('nbr_of_seats').value.trim();
                    const phoneNumber = document.getElementById('phoneNumber').value.trim();
                    const amount = document.getElementById('amount').value.trim();
                    const tempBookingRef = document.getElementById('tempBookingRef').value.trim();
                    const firstname = document.getElementById('firstname').value.trim();
                    const lastname = document.getElementById('lastname').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');

                    // Debug log all values
                    console.log('Form submission data:', {
                        user_id,
                        trip_id,
                        nbr_of_seats,
                        phoneNumber,
                        amount,
                        tempBookingRef,
                        firstname,
                        lastname,
                        email,
                        paymentMethod: paymentMethod?.value
                    });

                    // Validation
                    if (!paymentMethod) {
                        showError('Please select a payment method');
                        return;
                    }

                    if (!user_id || !trip_id || !nbr_of_seats) {
                        showError('Missing booking information. Please try again.');
                        return;
                    }

                    if (!phoneNumber) {
                        showError('Phone number is required');
                        return;
                    }

                    if (!amount || amount === '0') {
                        showError('Invalid amount');
                        return;
                    }

                    // Validate customer data
                    if (!firstname || !lastname || !email) {
                        showError('Missing customer information. Please try again.');
                        return;
                    }

                    // Disable button to prevent multiple submissions
                    document.getElementById('confirmBtn').disabled = true;

                    // Show payment status
                    showPaymentStatus('Processing Payment', 'Initializing payment request...', 10);

                    try {
                        // Send payment request (NO BOOKING CREATED YET)
                        const response = await fetch('http://localhost:3000/process_payment', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                user_id: user_id,
                                trip_id: trip_id,
                                number_of_seats: parseInt(nbr_of_seats),
                                phoneNumber: phoneNumber,
                                amount: parseFloat(amount),
                                payment_method: paymentMethod.value,
                                temp_booking_ref: tempBookingRef,
                                // Customer data
                                firstname: firstname,
                                lastname: lastname,
                                email: email,
                                contact: phoneNumber
                            }),
                        });

                        const data = await response.json();
                        console.log('Payment API response:', data);

                        if (!response.ok) {
                            throw new Error(data.message || 'Payment request failed');
                        }

                        // Update status
                        showPaymentStatus('Payment Initiated', 'Please check your phone to approve the payment...', 40);

                        // Start polling for status - Pass customer and booking data for creation on success
                        if (data.referenceId) {
                            await pollPaymentStatus(
                                data.referenceId,
                                data.amount,
                                user_id,
                                trip_id,
                                nbr_of_seats,
                                paymentMethod.value,
                                firstname,
                                lastname,
                                email,
                                phoneNumber
                            );
                        } else {
                            throw new Error('No reference ID received');
                        }

                    } catch (error) {
                        console.error('Payment error:', error);
                        showError(error.message || 'An error occurred while processing your payment');
                        // Re-enable button on error
                        document.getElementById('confirmBtn').disabled = false;
                    }
                });

                // Poll payment status - PASS CUSTOMER AND BOOKING DATA FOR CREATION ON SUCCESS
                async function pollPaymentStatus(referenceId, amount, user_id, trip_id, nbr_of_seats, payment_method, firstname, lastname, email, contact) {
                    let attempts = 0;
                    const maxAttempts = 30;

                    const checkStatus = async () => {
                        attempts++;

                        try {
                            // Pass customer and booking data as query parameters for creation on success
                            const queryParams = new URLSearchParams({
                                user_id: user_id,
                                trip_id: trip_id,
                                number_of_seats: nbr_of_seats,
                                payment_method: payment_method,
                                amount: amount, // Add amount for payment record
                                // Add customer data for booking creation
                                firstname: document.getElementById('firstname').value,
                                lastname: document.getElementById('lastname').value,
                                email: document.getElementById('email').value,
                                contact: document.getElementById('phoneNumber').value
                            });

                            const response = await fetch(`http://localhost:3000/payment_status/${referenceId}?${queryParams}`);

                            if (!response.ok) {
                                throw new Error('Failed to check payment status');
                            }

                            const statusData = await response.json();
                            console.log('Payment status check:', statusData);

                            // Update progress based on attempts
                            const progress = Math.min(40 + (attempts * 2), 90);
                            showPaymentStatus('Checking Status', `Waiting for payment confirmation... (Attempt ${attempts}/${maxAttempts})`, progress);

                            switch (statusData.status) {
                                case 'SUCCESSFUL':
                                    showPaymentStatus('Payment Successful', 'Creating customer record and booking...', 95);

                                    // Wait a moment for the backend to process the booking
                                    setTimeout(() => {
                                        showPaymentStatus('Booking Confirmed!', 'Your booking has been confirmed and ticket generated!', 100);
                                        handleSuccessfulPayment(statusData);
                                    }, 1500);
                                    return true;

                                case 'FAILED':
                                    throw new Error('Payment was declined or failed');

                                case 'PENDING':
                                    if (attempts >= maxAttempts) {
                                        throw new Error('Payment timeout. Please try again.');
                                    }
                                    setTimeout(checkStatus, 5000);
                                    break;

                                default:
                                    if (attempts >= maxAttempts) {
                                        throw new Error('Payment timeout. Please try again.');
                                    }
                                    setTimeout(checkStatus, 5000);
                                    break;
                            }
                        } catch (error) {
                            console.error('Status check error:', error);
                            showError(error.message || 'Failed to verify payment status');
                            // Re-enable button on error
                            document.getElementById('confirmBtn').disabled = false;
                        }
                    };

                    await checkStatus();
                }

                // Handle successful payment and redirect
                function handleSuccessfulPayment(paymentResult) {
                    // Show final success message
                    showPaymentStatus('Booking Confirmed!', 'Your booking has been confirmed and ticket generated!', 100);

                    // Redirect to success page with the booking ID
                    setTimeout(() => {
                        let redirectUrl = 'ticket_download.php';

                        // Use bookingId from payment result
                        if (paymentResult.booking_id && paymentResult.booking_id !== 'undefined') {
                            redirectUrl += '?booking_id=' + paymentResult.booking_id;
                        } else if (paymentResult.ticketInfo?.ticket_id) {
                            redirectUrl += '?ticket_id=' + paymentResult.ticketInfo.ticket_id;
                        } else {
                            // Fallback - redirect to booking success page
                            redirectUrl = 'booking_success.php';
                        }

                        console.log('Redirecting to:', redirectUrl);
                        console.log('Booking ID:', paymentResult.booking_id);
                        console.log('Customer ID:', paymentResult.customer_id);

                        window.location.href = redirectUrl;
                    }, 2000);
                }

                // Show payment status
                function showPaymentStatus(title, message, progress) {
                    const statusDiv = document.getElementById('paymentStatus');
                    const statusTitle = document.getElementById('statusTitle');
                    const statusMessage = document.getElementById('statusMessage');
                    const statusProgress = document.getElementById('statusProgress');
                    const statusSpinner = document.getElementById('statusSpinner');

                    statusDiv.style.display = 'block';
                    statusTitle.textContent = title;
                    statusMessage.textContent = message;
                    statusProgress.style.width = progress + '%';

                    // Update alert color based on progress
                    const alert = statusDiv.querySelector('.alert');
                    alert.className = 'alert ';
                    if (progress < 40) alert.className += 'alert-info';
                    else if (progress < 80) alert.className += 'alert-warning';
                    else alert.className += 'alert-success';

                    // Hide spinner when complete
                    if (progress >= 100) {
                        statusSpinner.style.display = 'none';
                    } else {
                        statusSpinner.style.display = 'inline-block';
                    }
                }

                // Show error message
                function showError(message) {
                    // Create a more user-friendly error display
                    const statusDiv = document.getElementById('paymentStatus');
                    statusDiv.innerHTML = `
        <div class="alert alert-danger">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3"></i>
                <div>
                    <h6 class="mb-1">Payment Error</h6>
                    <p class="mb-0 small">${message}</p>
                </div>
            </div>
        </div>
    `;
                    statusDiv.style.display = 'block';

                    // Re-enable button on error
                    document.getElementById('confirmBtn').disabled = false;
                }

                // Number formatting helper
                function numberFormat(number) {
                    return new Intl.NumberFormat().format(number);
                }


                // After payment is successful
if (updateAvailableSeats($conn, $trip_id, $number_of_seats_booked)) {
    // Proceed with creating the booking record
    // Show success message to user
} else {
    // Handle error
}
            </script>
</body>

</html>
<?php
$conn->close();
?>