<?php
session_start();
include('connection.php');

// Get ticket ID from URL
$ticket_id = isset($_GET['ticket_id']) ? $_GET['ticket_id'] : '';
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : '';

// If no ticket ID or booking ID, redirect to home
if (empty($ticket_id) && empty($booking_id)) {
    header("Location: homepage.php");
    exit;
}

// Fetch complete ticket details with all related information
$ticket_details = [];
$booking_details = [];
$passenger_details = [];
$payment_details = [];
$bus_details = [];
$trip_details = [];

if (!empty($ticket_id) || !empty($booking_id)) {
    // Build the query based on what ID we have
    if (!empty($ticket_id)) {
        // Get complete ticket information with all joins using ticket_id
        $stmt = $conn->prepare("
            SELECT 
                t.ticket_id, t.booking_id, t.checked, t.checked_at, t.created_at as ticket_created,
                b.booking_id, b.customer_id, b.trip_id, b.number_of_seats, b.booking_date,
                c.firstname, c.lastname, c.contact, c.email,
                p.amount, p.payment_method, p.transaction_id, p.time_paid,
                bus.model, bus.plates_number, 
                r.departure, r.destination, r.price_per_seat,
                trip.departure_datetime, trip.estimated_arrival
            FROM tickets t 
            LEFT JOIN bookings b ON t.booking_id = b.booking_id 
            LEFT JOIN customers c ON b.customer_id = c.customer_id 
            LEFT JOIN payments p ON b.booking_id = p.booking_id 
            LEFT JOIN trips trip ON b.trip_id = trip.trip_id
            LEFT JOIN buses bus ON trip.bus_id = bus.bus_id
            LEFT JOIN routes r ON trip.route_id = r.route_id
            WHERE t.ticket_id = ?
        ");
        $stmt->bind_param("s", $ticket_id);
    } else {
        // Get complete booking information with all joins using booking_id
        $stmt = $conn->prepare("
            SELECT 
                t.ticket_id, t.booking_id, t.checked, t.checked_at, t.created_at as ticket_created,
                b.booking_id, b.customer_id, b.trip_id, b.number_of_seats, b.booking_date,
                c.firstname, c.lastname, c.contact, c.email,
                p.amount, p.payment_method, p.transaction_id, p.time_paid,
                bus.model, bus.plates_number, 
                r.departure, r.destination, r.price_per_seat,
                trip.departure_datetime, trip.estimated_arrival
            FROM bookings b 
            LEFT JOIN tickets t ON b.booking_id = t.booking_id 
            LEFT JOIN customers c ON b.customer_id = c.customer_id 
            LEFT JOIN payments p ON b.booking_id = p.booking_id 
            LEFT JOIN trips trip ON b.trip_id = trip.trip_id
            LEFT JOIN buses bus ON trip.bus_id = bus.bus_id
            LEFT JOIN routes r ON trip.route_id = r.route_id
            WHERE b.booking_id = ?
        ");
        $stmt->bind_param("i", $booking_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // Organize the data
        $ticket_details = [
            'ticket_id' => $data['ticket_id'],
            'checked' => $data['checked'],
            'checked_at' => $data['checked_at'],
            'created_at' => $data['ticket_created']
        ];

        $booking_details = [
            'booking_id' => $data['booking_id'],
            'number_of_seats' => $data['number_of_seats'],
            'booking_date' => $data['booking_date']
        ];

        $passenger_details = [
            'name' => $data['firstname'] . ' ' . $data['lastname'],
            'phone' => $data['contact'],
            'email' => $data['email']
        ];

        $payment_details = [
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'transaction_id' => $data['transaction_id'],
            'time_paid' => $data['time_paid']
        ];

        $bus_details = [
            'model' => $data['model'],
            'plates_number' => $data['plates_number']
        ];

        $trip_details = [
            'departure' => $data['departure'],
            'destination' => $data['destination'],
            'departure_datetime' => $data['departure_datetime'],
            'estimated_arrival' => $data['estimated_arrival'],
            'price_per_seat' => $data['price_per_seat']
        ];
    }
    $stmt->close();
}

// If no data found, show error message
if (empty($ticket_details) && empty($booking_details)) {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ticket Not Found - SwiftPass</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                background: #f8f9fa;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .error-container {
                text-align: center;
                max-width: 500px;
                padding: 2rem;
            }
        </style>
    </head>

    <body>
        <div class="error-container">
            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
            <h2 class="text-danger">Ticket Not Found</h2>
            <p class="text-muted mb-4">The ticket or booking you're looking for doesn't exist or has been removed.</p>
            <a href="homepage.php" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Back to Home
            </a>
        </div>
    </body>

    </html>
<?php
    exit;
}

// Handle QR code scanning - update ticket status to 'yes' when scanned
if (isset($_GET['scan']) && $_GET['scan'] == 'true' && !empty($ticket_id)) {
    // Check if ticket is not already scanned
    if (($ticket_details['checked'] ?? 'no') === 'no') {
        $update_stmt = $conn->prepare("UPDATE tickets SET checked = 'yes', checked_at = NOW() WHERE ticket_id = ?");
        $update_stmt->bind_param("s", $ticket_id);

        if ($update_stmt->execute()) {
            // Update local ticket details
            $ticket_details['checked'] = 'yes';
            $ticket_details['checked_at'] = date('Y-m-d H:i:s');

            // Log the scan event (optional)
            $log_stmt = $conn->prepare("INSERT INTO ticket_scans (ticket_id, scanned_at, scanner_info) VALUES (?, NOW(), ?)");
            $scanner_info = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            $log_stmt->bind_param("ss", $ticket_id, $scanner_info);
            $log_stmt->execute();
            $log_stmt->close();
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Ticket - SwiftPass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #1abc9c;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ticket-container {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .ticket-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .ticket-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--success), var(--info), var(--warning));
        }

        .ticket-header::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .ticket-body {
            padding: 2.5rem;
        }

        .ticket-section {
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 2px dashed #e9ecef;
            position: relative;
        }

        .ticket-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .section-icon {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: white;
            font-size: 1.2rem;
        }

        .qr-code-container {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            margin: 1rem 0;
            border: 2px dashed #dee2e6;
        }

        .qr-code-wrapper {
            display: inline-block;
            padding: 15px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 220px;
            margin: 0 auto;
        }

        .qr-code-wrapper img {
            max-width: 100%;
            height: auto;
            display: block;
            border-radius: 8px;
        }

        .ticket-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.2rem;
            margin-top: 1rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            min-width: 140px;
            font-size: 0.95rem;
        }

        .info-value {
            color: #2c3e50;
            font-weight: 600;
            text-align: right;
            font-size: 0.95rem;
        }

        .security-notice {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border: 2px solid #ffd43b;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: center;
            font-size: 0.9rem;
            position: relative;
        }

        .security-notice::before {
            content: '⚠️';
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0 10px;
            font-size: 1.2rem;
        }

        .print-only {
            display: none;
        }

        .action-buttons {
            text-align: center;
            margin-top: 3rem;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-download {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
            color: white;
        }

        .btn-print {
            background: linear-gradient(135deg, var(--success), #27ae60);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 204, 113, 0.4);
        }

        .btn-home {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
            color: white;
        }

        .status-badge {
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .scan-info {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border: 2px solid #2196f3;
            border-radius: 12px;
            padding: 1rem;
            margin: 1rem 0;
            text-align: center;
            font-size: 0.85rem;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }

            .ticket-container {
                box-shadow: none !important;
                margin: 0 !important;
                max-width: none !important;
                border-radius: 0 !important;
                border: none !important;
            }

            .action-buttons {
                display: none !important;
            }

            .qr-code-wrapper {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
                display: block;
            }

            .ticket-container {
                margin: 0;
            }

            .ticket-header {
                padding: 2rem 1rem;
            }

            .ticket-body {
                padding: 1.5rem;
            }

            .ticket-info-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                padding: 1rem;
                flex-direction: column;
            }

            .btn-download,
            .btn-print,
            .btn-home {
                width: 100%;
                justify-content: center;
            }

            .qr-code-wrapper {
                max-width: 180px;
            }
        }

        /* Success animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .ticket-container {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Scan animation */
        @keyframes scanPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .scanning {
            animation: scanPulse 0.5s ease-in-out;
        }
    </style>
</head>

<body>
    <div class="ticket-container">
        <!-- Ticket Header -->
        <div class="ticket-header">
            <div class="row align-items-center">
                <div class="col-md-8 text-start">
                    <h1 class="mb-2"><i class="fas fa-bus me-2"></i>SwiftPass</h1>
                    <h2 class="mb-0">E-Ticket Confirmed! 🎉</h2>
                    <p class="mb-0 mt-2 opacity-75">Your journey is booked and ready</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="bg-white text-dark rounded-pill p-3 d-inline-block">
                        <small class="text-muted d-block mb-1">Ticket Status</small>
                        <span class="status-badge bg-<?php echo ($ticket_details['checked'] ?? 'no') === 'yes' ? 'warning' : 'success'; ?> text-white">
                            <?php echo ($ticket_details['checked'] ?? 'no') === 'yes' ? '🟡 USED' : '🟢 ACTIVE'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket Body -->
        <div class="ticket-body">
            <!-- Passenger & Journey Details -->
            <div class="ticket-section">
                <div class="section-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h4 class="text-primary mb-3">Passenger & Journey Details</h4>

                <div class="row">
                    <div class="col-md-8">
                        <div class="ticket-info-grid">
                            <div class="info-item">
                                <span class="info-label">Passenger Name:</span>
                                <span class="info-value"><?php echo htmlspecialchars($passenger_details['name']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone Number:</span>
                                <span class="info-value"><?php echo htmlspecialchars($passenger_details['phone']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email Address:</span>
                                <span class="info-value"><?php echo htmlspecialchars($passenger_details['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Bus Name:</span>
                                <span class="info-value"><?php echo htmlspecialchars($bus_details['model']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">License Plate:</span>
                                <span class="info-value"><?php echo htmlspecialchars($bus_details['plates_number']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Route:</span>
                                <span class="info-value"><?php echo htmlspecialchars($trip_details['departure'] ?? ''); ?> → <?php echo htmlspecialchars($trip_details['destination'] ?? ''); ?></span>
                            </div>
                            <?php if (!empty($trip_details['departure_datetime'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Departure Time:</span>
                                    <span class="info-value"><?php echo date('M j, Y g:i A', strtotime($trip_details['departure_datetime'])); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($trip_details['estimated_arrival'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Arrival Time:</span>
                                    <span class="info-value"><?php echo date('M j, Y g:i A', strtotime($trip_details['estimated_arrival'])); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="info-item">
                                <span class="info-label">Seats Booked:</span>
                                <span class="info-value text-success fw-bold"><?php echo $booking_details['number_of_seats']; ?> seat(s)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Booking Date:</span>
                                <span class="info-value"><?php echo date('M j, Y g:i A', strtotime($booking_details['booking_date'])); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- QR Code -->
                        <div class="qr-code-container">
                            <h5 class="text-muted mb-3"><i class="fas fa-qrcode me-2"></i>Scan to Verify</h5>
                            <div class="qr-code-wrapper">
                                <div id="qrCodeContainer"></div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-ticket-alt me-1"></i>
                                    Ticket ID: <?php echo $ticket_details['ticket_id'] ?? 'N/A'; ?>
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-hashtag me-1"></i>
                                    Booking Ref: #<?php echo $booking_details['booking_id']; ?>
                                </small>
                            </div>

                            <?php if (($ticket_details['checked'] ?? 'no') === 'yes' && !empty($ticket_details['checked_at'])): ?>
                                <div class="scan-info mt-3">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    <strong>Scanned:</strong> <?php echo date('M j, Y g:i A', strtotime($ticket_details['checked_at'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="ticket-section">
                <div class="section-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h4 class="text-primary mb-3">Payment Information</h4>

                <div class="ticket-info-grid">
                    <div class="info-item">
                        <span class="info-label">Total Amount Paid:</span>
                        <span class="info-value text-success fw-bold fs-5"><?php echo number_format($payment_details['amount']); ?> FRW</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value">
                            <i class="fas fa-<?php echo $payment_details['payment_method'] === 'momo' ? 'mobile-alt' : 'wifi'; ?> me-1"></i>
                            <?php echo strtoupper($payment_details['payment_method']); ?> Money
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Transaction ID:</span>
                        <span class="info-value small font-monospace"><?php echo $payment_details['transaction_id']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Payment Date:</span>
                        <span class="info-value"><?php echo date('M j, Y g:i A', strtotime($payment_details['time_paid'])); ?></span>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <strong>Security Notice:</strong> This QR code will automatically expire once scanned at the boarding point.
                Please keep this ticket secure until your journey. Do not share this ticket with anyone.
            </div>

            <!-- Print-only section -->
            <div class="print-only text-center mt-4">
                <p class="text-muted small">
                    <i class="fas fa-print me-1"></i>
                    Generated on <?php echo date('M j, Y \a\t g:i A'); ?> | SwiftPass E-Ticket System
                </p>
            </div>
        </div>

        <!-- Action Buttons (Hidden during print) -->
        <div class="action-buttons no-print">
            <button onclick="downloadTicket()" class="btn-download">
                <i class="fas fa-download"></i>
                Download Ticket
            </button>
            <button onclick="printTicket()" class="btn-print">
                <i class="fas fa-print"></i>
                Print Ticket
            </button>
            <a href="homepage.php" class="btn-home">
                <i class="fas fa-home"></i>
                Back to Home
            </a>
        </div>
    </div>

    <!-- QR Code Generator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Generate QR code with actual data
        function generateQRCode() {
            const qr = qrcode(0, 'M');

            const ticket_id = '<?php echo $ticket_details['ticket_id'] ?? ''; ?>';
            const booking_id = '<?php echo $booking_details['booking_id']; ?>';
            const security_hash = '<?php echo md5(($ticket_details['ticket_id'] ?? '') . $booking_details['booking_id'] . 'swiftpass_secret'); ?>';

            // Create a secure verification URL - FIXED the URL
            const verificationUrl = `<?php echo 'https://swifftpass.cleverapps.io/'; ?>verify-ticket.php?ticket_id=${ticket_id}&booking_id=${booking_id}&hash=${security_hash}`;

            qr.addData(verificationUrl);
            qr.make();

            const qrContainer = document.getElementById('qrCodeContainer');
            qrContainer.innerHTML = qr.createImgTag(4);
        }

        // Print function
        function printTicket() {
            window.print();
        }

        // Download as image function
        function downloadTicket() {
            const ticketElement = document.querySelector('.ticket-container');
            const downloadBtn = document.querySelector('.btn-download');

            // Show loading state
            const originalText = downloadBtn.innerHTML;
            downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
            downloadBtn.disabled = true;

            html2canvas(ticketElement, {
                scale: 2,
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const image = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.download = 'swiftpass-ticket-<?php echo $ticket_details['ticket_id'] ?? $booking_details['booking_id']; ?>.png';
                link.href = image;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Restore button state
                downloadBtn.innerHTML = originalText;
                downloadBtn.disabled = false;
            }).catch(error => {
                console.error('Error generating ticket image:', error);
                downloadBtn.innerHTML = originalText;
                downloadBtn.disabled = false;
                alert('Error downloading ticket. Please try again.');
            });
        }

        // Scan ticket function - UPDATED TO USE EXPRESS SERVER
        function scanTicket() {
            <?php if (!empty($ticket_details['ticket_id'])): ?>
                // Show confirmation dialog
                if (confirm('Are you sure you want to scan this ticket? This will mark it as used and cannot be undone.')) {
                    // Show loading state
                    const qrWrapper = document.querySelector('.qr-code-wrapper');
                    const qrImage = document.querySelector('#qrCodeContainer img');

                    if (qrWrapper) qrWrapper.classList.add('scanning');
                    if (qrImage) qrImage.style.opacity = '0.7';

                    // Send scan request to Express backend
                    fetch('http://localhost:3000/ticket/scan', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                ticket_id: '<?php echo $ticket_details['ticket_id']; ?>'
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Update UI to show ticket is used
                                const statusBadge = document.querySelector('.status-badge');
                                if (statusBadge) {
                                    statusBadge.className = 'status-badge bg-warning text-white';
                                    statusBadge.textContent = '🟡 USED';
                                }

                                // Add scan info
                                const qrContainer = document.querySelector('.qr-code-container');
                                const existingScanInfo = qrContainer.querySelector('.scan-info');

                                if (!existingScanInfo) {
                                    const scanInfo = document.createElement('div');
                                    scanInfo.className = 'scan-info mt-3';
                                    scanInfo.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i><strong>Scanned:</strong> Just now';
                                    qrContainer.appendChild(scanInfo);
                                }

                                // Update QR code appearance to show it's used
                                if (qrImage) {
                                    qrImage.style.filter = 'grayscale(100%)';
                                }

                                alert('✅ Ticket scanned successfully!');

                                // Stop checking status since it's now used
                                if (typeof statusCheckInterval !== 'undefined') {
                                    clearInterval(statusCheckInterval);
                                }
                            } else {
                                alert('❌ ' + (data.message || 'Failed to scan ticket'));
                            }
                        })
                        .catch(error => {
                            console.error('Error scanning ticket:', error);
                            alert('❌ Error scanning ticket. Please make sure the server is running on port 3000.');
                        })
                        .finally(() => {
                            if (qrWrapper) qrWrapper.classList.remove('scanning');
                            if (qrImage) qrImage.style.opacity = '1';
                        });
                }
            <?php else: ?>
                alert('Ticket ID not found. Cannot scan.');
            <?php endif; ?>
        }

        // Check ticket status periodically (if ticket exists and is active) - UPDATED TO USE EXPRESS SERVER
        function checkTicketStatus() {
            <?php if (!empty($ticket_details['ticket_id']) && ($ticket_details['checked'] ?? 'no') === 'no'): ?>
                fetch('http://localhost:3000/ticket/<?php echo $ticket_details['ticket_id']; ?>')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.ticket.checked === 'yes') {
                            // Update status display
                            const statusBadge = document.querySelector('.status-badge');
                            if (statusBadge) {
                                statusBadge.className = 'status-badge bg-warning text-white';
                                statusBadge.textContent = '🟡 USED';
                            }

                            // Add scan info
                            const qrContainer = document.querySelector('.qr-code-container');
                            const existingScanInfo = qrContainer.querySelector('.scan-info');
                            const qrImage = document.querySelector('#qrCodeContainer img');

                            if (!existingScanInfo) {
                                const scanInfo = document.createElement('div');
                                scanInfo.className = 'scan-info mt-3';
                                scanInfo.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i><strong>Scanned:</strong> ' + new Date().toLocaleString();
                                qrContainer.appendChild(scanInfo);
                            }

                            // Update QR code appearance
                            if (qrImage) {
                                qrImage.style.filter = 'grayscale(100%)';
                            }

                            // Stop checking status
                            if (typeof statusCheckInterval !== 'undefined') {
                                clearInterval(statusCheckInterval);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking ticket status:', error);
                        // Don't show alert for status check errors to avoid annoying users
                    });
            <?php endif; ?>
        }

        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Generate QR code
            generateQRCode();

            const buttons = document.querySelectorAll('.btn-download, .btn-print, .btn-home');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Add click event to QR code for scanning - UPDATED
            const qrContainer = document.getElementById('qrCodeContainer');
            if (qrContainer) {
                qrContainer.style.cursor = 'pointer';
                qrContainer.title = 'Click to scan ticket (mark as used)';
                qrContainer.addEventListener('click', scanTicket);

                // Also make the QR code image clickable if it's loaded later
                const qrImage = qrContainer.querySelector('img');
                if (qrImage) {
                    qrImage.style.cursor = 'pointer';
                    qrImage.title = 'Click to scan ticket (mark as used)';
                }
            }

            // If ticket is already used, update the appearance
            <?php if (($ticket_details['checked'] ?? 'no') === 'yes'): ?>
                const qrImage = document.querySelector('#qrCodeContainer img');
                if (qrImage) {
                    qrImage.style.filter = 'grayscale(100%)';
                    qrImage.style.opacity = '0.7';
                }
            <?php endif; ?>
        });

        // Check status every 5 seconds (only if ticket is active)
        <?php if (($ticket_details['checked'] ?? 'no') === 'no' && !empty($ticket_details['ticket_id'])): ?>
            const statusCheckInterval = setInterval(checkTicketStatus, 5000);
        <?php endif; ?>
    </script>
</body>

</html>
<?php
$conn->close();
?>