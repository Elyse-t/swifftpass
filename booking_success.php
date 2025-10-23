<?php
session_start();
include('connection.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get ticket ID from URL
$ticket_id = isset($_GET['ticket_id']) ? $_GET['ticket_id'] : '';

// Fetch ticket and related details
$ticket_details = [];
$booking_details = [];
$payment_details = [];
$bus_details = [];

if (!empty($ticket_id)) {
    // Get ticket information with booking and customer details
    $stmt = $conn->prepare("
        SELECT t.*, b.*, c.firstname, c.lastname, c.contact, c.email, 
               p.amount, p.payment_method, p.transaction_id,
               bus.bus_name, bus.plates_number, bus.route_name
        FROM tickets t 
        LEFT JOIN bookings b ON t.booking_id = b.booking_id 
        LEFT JOIN customers c ON b.customer_id = c.customer_id 
        LEFT JOIN payments p ON b.booking_id = p.booking_id 
        LEFT JOIN buses bus ON b.bus_plaque = bus.plates_number 
        WHERE t.ticket_id = ?
    ");
    $stmt->bind_param("s", $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $ticket_details = $data;
        $booking_details = $data;
        $payment_details = $data;
        $bus_details = $data;
        
        // Create full passenger name
        $booking_details['passenger_name'] = $data['firstname'] . ' ' . $data['lastname'];
        $booking_details['passenger_phone'] = $data['contact'];
        $booking_details['passenger_email'] = $data['email'];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Ticket - SwiftPass</title>
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
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        /* Printable Ticket Styles */
        .ticket-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        .ticket-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .ticket-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--success), var(--info));
        }

        .ticket-body {
            padding: 2rem;
        }

        .ticket-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px dashed #e9ecef;
        }

        .ticket-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .qr-code-container {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 1rem 0;
        }

        .ticket-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .info-item {
            display: flex;
            justify-content: between;
            margin-bottom: 0.5rem;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            min-width: 120px;
        }

        .info-value {
            color: #2c3e50;
            font-weight: 500;
        }

        .security-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            text-align: center;
            font-size: 0.9rem;
        }

        .print-only {
            display: none;
        }

        .action-buttons {
            text-align: center;
            margin-top: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .btn-download {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            margin: 0 0.5rem;
        }

        .btn-print {
            background: linear-gradient(135deg, var(--success), #27ae60);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            margin: 0 0.5rem;
        }

        /* Hide elements during print */
        @media print {
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block !important;
            }
            
            body {
                background: white;
                padding: 0;
            }
            
            .ticket-container {
                box-shadow: none;
                margin: 0;
                max-width: none;
            }
            
            .action-buttons {
                display: none;
            }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .ticket-container {
                margin: 10px;
            }
            
            .ticket-header {
                padding: 1.5rem 1rem;
            }
            
            .ticket-body {
                padding: 1.5rem;
            }
            
            .ticket-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <!-- Ticket Header -->
        <div class="ticket-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2"><i class="fas fa-bus me-2"></i>SwiftPass</h1>
                    <h2 class="mb-0">E-Ticket</h2>
                    <p class="mb-0 mt-2">Digital Bus Ticket</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="bg-white text-dark rounded p-2 d-inline-block">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-<?php echo $ticket_details['checked'] === 'yes' ? 'warning' : 'success'; ?>">
                            <?php echo $ticket_details['checked'] === 'yes' ? 'USED' : 'ACTIVE'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket Body -->
        <div class="ticket-body">
            <!-- Passenger & Bus Information Section -->
            <div class="ticket-section">
                <h4 class="text-primary mb-3">
                    <i class="fas fa-user me-2"></i>Passenger & Journey Details
                </h4>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="ticket-info-grid">
                            <div class="info-item">
                                <span class="info-label">Passenger:</span>
                                <span class="info-value"><?php echo htmlspecialchars($booking_details['passenger_name'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone:</span>
                                <span class="info-value"><?php echo htmlspecialchars($booking_details['passenger_phone'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value"><?php echo htmlspecialchars($booking_details['passenger_email'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Bus:</span>
                                <span class="info-value"><?php echo htmlspecialchars($bus_details['bus_name'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Plaque:</span>
                                <span class="info-value"><?php echo htmlspecialchars($bus_details['plates_number'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Route:</span>
                                <span class="info-value"><?php echo htmlspecialchars($bus_details['route_name'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Seats:</span>
                                <span class="info-value"><?php echo $booking_details['number_of_seats'] ?? '1'; ?> seat(s)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Booking Date:</span>
                                <span class="info-value"><?php echo isset($booking_details['booking_date']) ? date('M j, Y', strtotime($booking_details['booking_date'])) : date('M j, Y'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- QR Code -->
                        <div class="qr-code-container">
                            <h5 class="text-muted mb-3">Scan to Verify</h5>
                            <div id="qrCodeContainer" class="mb-3"></div>
                            <small class="text-muted d-block">Ticket ID: <?php echo $ticket_details['ticket_id'] ?? 'N/A'; ?></small>
                            <small class="text-muted">Booking Ref: #<?php echo $booking_details['booking_id'] ?? 'N/A'; ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="ticket-section">
                <h4 class="text-primary mb-3">
                    <i class="fas fa-credit-card me-2"></i>Payment Information
                </h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <span class="info-label">Amount Paid:</span>
                            <span class="info-value text-success fw-bold"><?php echo number_format($payment_details['amount'] ?? 0); ?> FRW</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Payment Method:</span>
                            <span class="info-value"><?php echo $payment_details['payment_method'] ?? 'Mobile Money'; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <span class="info-label">Transaction ID:</span>
                            <span class="info-value small"><?php echo $payment_details['transaction_id'] ?? 'N/A'; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Payment Date:</span>
                            <span class="info-value"><?php echo isset($payment_details['time_paid']) ? date('M j, Y g:i A', strtotime($payment_details['time_paid'])) : date('M j, Y g:i A'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                <strong>Security Notice:</strong> This QR code will automatically expire once scanned at the boarding point. 
                Please keep this ticket secure until your journey.
            </div>

            <!-- Print-only section -->
            <div class="print-only text-center mt-4">
                <p class="text-muted small">Generated on <?php echo date('M j, Y \a\t g:i A'); ?> | SwiftPass E-Ticket System</p>
            </div>
        </div>

        <!-- Action Buttons (Hidden during print) -->
        <a href="ticket_download.php?ticket_id=<?php echo $ticket_details['ticket_id']; ?>" class="btn btn-success w-100">
    <i class="fas fa-download me-2"></i>Download Ticket
</a>
            <button onclick="printTicket()" class="btn-print">
                <i class="fas fa-print me-2"></i>Print Ticket
            </button>
            <a href="booking_success.php?booking_id=<?php echo $booking_details['booking_id'] ?? ''; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Booking
            </a>
        </div>
    </div>

    <!-- QR Code Generator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Generate QR code
        <?php if (!empty($ticket_details)): ?>
        const qr = qrcode(0, 'M');
        qr.addData(JSON.stringify({
            ticket_id: '<?php echo $ticket_details['ticket_id']; ?>',
            booking_id: '<?php echo $booking_details['booking_id'] ?? ''; ?>',
            passenger: '<?php echo htmlspecialchars($booking_details['passenger_name'] ?? ''); ?>',
            timestamp: <?php echo time(); ?>
        }));
        qr.make();
        document.getElementById('qrCodeContainer').innerHTML = qr.createImgTag(5);
        <?php endif; ?>

        // Print function
        function printTicket() {
            window.print();
        }

        // Download as image function
        function downloadTicket() {
            const ticketElement = document.querySelector('.ticket-container');
            
            html2canvas(ticketElement, {
                scale: 2, // Higher quality
                useCORS: true,
                logging: false
            }).then(canvas => {
                // Convert canvas to image
                const image = canvas.toDataURL('image/png');
                
                // Create download link
                const link = document.createElement('a');
                link.download = 'swiftpass-ticket-<?php echo $ticket_details['ticket_id'] ?? ''; ?>.png';
                link.href = image;
                link.click();
            });
        }

        // Auto-print option (uncomment if needed)
        // window.onload = function() {
        //     setTimeout(printTicket, 1000);
        // };
    </script>
</body>
</html>
<?php
$conn->close();
?>