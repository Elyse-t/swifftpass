<?php
session_start();
include('connection.php');

// Get parameters from URL
$ticket_id = isset($_GET['ticket_id']) ? $_GET['ticket_id'] : '';
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : '';
$hash = isset($_GET['hash']) ? $_GET['hash'] : '';

// Verify the hash for security
$expected_hash = md5($ticket_id . $booking_id . 'swiftpass_secret');

if ($ticket_id && $booking_id && $hash === $expected_hash) {
    // Check if ticket exists
    $stmt = $conn->prepare("SELECT * FROM tickets WHERE ticket_id = ? AND booking_id = ?");
    $stmt->bind_param("si", $ticket_id, $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $ticket = $result->fetch_assoc();
        
        if ($ticket['checked'] === 'no') {
            // Update the database
            $update_stmt = $conn->prepare("UPDATE tickets SET checked = 'yes', checked_at = NOW() WHERE ticket_id = ? AND booking_id = ?");
            $update_stmt->bind_param("si", $ticket_id, $booking_id);
            
            if ($update_stmt->execute()) {
                $message = "✅ Ticket verified successfully!";
                $status = "success";
            } else {
                $message = "❌ Error updating ticket status";
                $status = "error";
            }
            $update_stmt->close();
        } else {
            $message = "ℹ️ Ticket was already verified on: " . date('M j, Y g:i A', strtotime($ticket['checked_at']));
            $status = "info";
        }
    } else {
        $message = "❌ Ticket not found.";
        $status = "error";
    }
    $stmt->close();
} else {
    $message = "❌ Invalid verification link.";
    $status = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Verification - SwiftPass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .verification-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="mb-4">
            <i class="fas fa-<?php echo $status === 'success' ? 'check-circle' : ($status === 'error' ? 'exclamation-circle' : 'info-circle'); ?> fa-4x text-<?php echo $status; ?> mb-3"></i>
            <h2 class="text-<?php echo $status; ?>">Ticket Verification</h2>
        </div>
        
        <p class="lead mb-4"><?php echo $message; ?></p>
        
        <?php if ($status === 'success'): ?>
            <div class="alert alert-success">
                <i class="fas fa-check me-2"></i>
                This ticket has been successfully verified and marked as used.
            </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <a href="homepage.php" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Back to Home
            </a>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Close Window
            </button>
        </div>
        
        <?php if (isset($ticket_id) && !empty($ticket_id)): ?>
            <div class="mt-4 text-muted small">
                <p>Ticket ID: <?php echo $ticket_id; ?></p>
                <p>Booking ID: <?php echo $booking_id; ?></p>
                <p>Verified: <?php echo date('M j, Y g:i A'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>