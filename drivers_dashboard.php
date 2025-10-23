<?php
include 'connection.php';
session_start();

// Check if driver is logged in
if (!isset($_SESSION['driver_id']) && !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get driver ID from session
$driver_id = $_SESSION['driver_id'] ?? $_SESSION['user_id'];

// Fetch driver details
$driver_query = $conn->query("SELECT * FROM drivers WHERE driver_id = '$driver_id'");
if ($driver_query->num_rows === 0) {
    header("Location: index.php");
    exit();
}
$driver = $driver_query->fetch_assoc();

// Fetch current active trip for this driver using actual database schema
$current_trip_query = $conn->query("
    SELECT t.*, b.plates_number, b.model, r.departure, r.destination, r.price_per_seat,
           r.delay_time,
           DATE_ADD(t.departure_datetime, 
                   INTERVAL (CASE 
                            WHEN r.delay_time LIKE '%h%' THEN REPLACE(REPLACE(r.delay_time, ' hours', ''), 'h', '')
                            WHEN r.delay_time LIKE '%hour%' THEN REPLACE(r.delay_time, ' hour', '')
                            ELSE r.delay_time 
                           END) HOUR) as calculated_estimated_arrival
    FROM trips t 
    JOIN buses b ON t.bus_id = b.bus_id 
    JOIN routes r ON t.route_id = r.route_id 
    WHERE t.driver_id = '$driver_id' 
    AND t.status IN ('available', 'ontrip')
    ORDER BY t.departure_datetime DESC 
    LIMIT 1
");

$current_trip = null;
if ($current_trip_query->num_rows > 0) {
    $current_trip = $current_trip_query->fetch_assoc();
}

// Handle trip completion notification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_arrived'])) {
    if ($current_trip) {
        $trip_id = $current_trip['trip_id'];
        
        // Update trip status to arrived
        $update_trip = $conn->query("UPDATE trips SET status = 'arrived' WHERE trip_id = '$trip_id'");
        
        // Update bus status to active (available)
        $bus_id = $current_trip['bus_id'];
        $update_bus = $conn->query("UPDATE buses SET status = 'active' WHERE bus_id = '$bus_id'");
        
        // Create notification for admin
        $notification_message = "Driver " . $driver['name'] . " has completed trip #" . $trip_id . " from " . $current_trip['departure'] . " to " . $current_trip['destination'];
        
        // Check if admin_notifications table exists, if not create it
        $check_notif_table = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
        if ($check_notif_table->num_rows == 0) {
            $create_notif_table = $conn->query("
                CREATE TABLE admin_notifications (
                    notification_id INT PRIMARY KEY AUTO_INCREMENT,
                    title VARCHAR(255) NOT NULL,
                    message TEXT NOT NULL,
                    type ENUM('info', 'success', 'warning', 'danger') DEFAULT 'info',
                    status ENUM('unread', 'read') DEFAULT 'unread',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        }
        
        $insert_notification = $conn->query("
            INSERT INTO admin_notifications (title, message, type, status, created_at) 
            VALUES ('Trip Completed', '$notification_message', 'success', 'unread', NOW())
        ");
        
        if ($update_trip && $update_bus) {
            $success_message = "✅ Trip completed successfully! Admin has been notified. Bus is now available for new trips.";
            // Refresh current trip data
            $current_trip = null;
        } else {
            $error_message = "❌ Error updating status. Please try again.";
        }
    }
}

// Handle problem report - create problem_reports table if it doesn't exist
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['report_problem'])) {
    $problem_type = $conn->real_escape_string($_POST['problem_type']);
    $description = $conn->real_escape_string($_POST['description']);
    $trip_id = $current_trip ? $current_trip['trip_id'] : 'NULL';
    
    // Check if problem_reports table exists, if not create it
    $check_table = $conn->query("SHOW TABLES LIKE 'problem_reports'");
    if ($check_table->num_rows == 0) {
        // Create problem_reports table
        $create_table = $conn->query("
            CREATE TABLE problem_reports (
                report_id INT PRIMARY KEY AUTO_INCREMENT,
                driver_id INT NOT NULL,
                trip_id INT NULL,
                problem_type VARCHAR(50) NOT NULL,
                description TEXT NOT NULL,
                status ENUM('reported', 'in_progress', 'resolved') DEFAULT 'reported',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (driver_id) REFERENCES drivers(driver_id) ON DELETE CASCADE,
                FOREIGN KEY (trip_id) REFERENCES trips(trip_id) ON DELETE SET NULL
            )
        ");
    }
    
    // Insert problem report into database
    $insert_problem = $conn->query("
        INSERT INTO problem_reports (driver_id, trip_id, problem_type, description, status, created_at) 
        VALUES ('$driver_id', $trip_id, '$problem_type', '$description', 'reported', NOW())
    ");
    
    // Create notification for admin
    $notification_message = "Driver " . $driver['name'] . " reported a problem: " . $problem_type . " - " . $description;
    
    // Check if admin_notifications table exists, if not create it
    $check_notif_table = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
    if ($check_notif_table->num_rows == 0) {
        $create_notif_table = $conn->query("
            CREATE TABLE admin_notifications (
                notification_id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                type ENUM('info', 'success', 'warning', 'danger') DEFAULT 'info',
                status ENUM('unread', 'read') DEFAULT 'unread',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
    
    $insert_notification = $conn->query("
        INSERT INTO admin_notifications (title, message, type, status, created_at) 
        VALUES ('Problem Reported', '$notification_message', 'warning', 'unread', NOW())
    ");
    
    if ($insert_problem) {
        $success_message = "✅ Problem reported successfully! Admin has been notified.";
    } else {
        $error_message = "❌ Error reporting problem. Please try again.";
    }
}

// Fetch recent completed trips with departure and arrival times
$recent_trips_query = $conn->query("
    SELECT t.*, r.departure, r.destination, b.plates_number, b.model,
           t.departure_datetime,
           t.estimated_arrival,
           CASE 
               WHEN t.status = 'arrived' THEN NOW()
               ELSE NULL 
           END as actual_arrival_time,
           TIMEDIFF(
               CASE 
                   WHEN t.status = 'arrived' THEN NOW()
                   ELSE t.estimated_arrival 
               END, 
               t.departure_datetime
           ) as actual_duration
    FROM trips t 
    JOIN routes r ON t.route_id = r.route_id 
    JOIN buses b ON t.bus_id = b.bus_id 
    WHERE t.driver_id = '$driver_id' 
    ORDER BY t.departure_datetime DESC 
    LIMIT 5
");

// Fetch statistics using actual schema
$total_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE driver_id = '$driver_id'")->fetch_assoc()['total'];
$completed_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE driver_id = '$driver_id' AND status = 'arrived'")->fetch_assoc()['total'];
$ongoing_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE driver_id = '$driver_id' AND status IN ('available', 'ontrip')")->fetch_assoc()['total'];

// Calculate total passengers transported
$total_passengers = $conn->query("
    SELECT COALESCE(SUM(b.number_of_seats), 0) as total 
    FROM bookings b 
    JOIN trips t ON b.trip_id = t.trip_id 
    WHERE t.driver_id = '$driver_id' AND t.status = 'arrived'
")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - SwiftPass</title>
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
            background: linear-gradient(135deg, #191e32ff 0%, #1a151fff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            margin-bottom: 2rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success), #27ae60);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #c0392b);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover, .btn-warning:hover, .btn-danger:hover {
            transform: translateY(-2px);
        }
        
        .driver-info-card {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .current-trip-card {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .trip-timeline {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        
        .problem-report-card {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-available { background: linear-gradient(135deg, var(--success), #27ae60); color: white; }
        .badge-ontrip { background: linear-gradient(135deg, var(--primary), #2c3e50); color: white; }
        .badge-arrived { background: linear-gradient(135deg, var(--info), #138496); color: white; }
        .badge-maintenance { background: linear-gradient(135deg, var(--warning), #e67e22); color: white; }
        
        .route-path {
            font-weight: 600;
            color: var(--primary);
            font-size: 1.2rem;
        }
        
        .trip-card {
            border-left: 4px solid var(--secondary);
            padding-left: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(248, 249, 250, 0.8);
            border-radius: 8px;
        }
        
        .no-trip {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .no-trip i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #bdc3c7;
        }
        
        .passenger-count {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            margin: 1rem 0;
        }
        
        .trip-time-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
            padding: 1rem;
            margin: 0.5rem 0;
        }
        
        .time-label {
            font-weight: 600;
            color: var(--primary);
        }
        
        .time-value {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Driver Dashboard</h3>
                            <p class="mb-0 opacity-75">Welcome back, <?php echo htmlspecialchars($driver['name']); ?>!</p>
                        </div>
                        <div>
                            <span class="badge bg-success me-2">License: <?php echo htmlspecialchars($driver['license']); ?></span>
                            <span class="badge bg-info me-2">Contact: <?php echo htmlspecialchars($driver['contact']); ?></span>
                            <a href="logout.php" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number text-primary"><?php echo $total_trips; ?></div>
                    <p class="mb-0 text-muted">Total Trips</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number text-success"><?php echo $completed_trips; ?></div>
                    <p class="mb-0 text-muted">Completed Trips</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number text-warning"><?php echo $ongoing_trips; ?></div>
                    <p class="mb-0 text-muted">Ongoing Trips</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number text-info"><?php echo $total_passengers; ?></div>
                    <p class="mb-0 text-muted">Passengers Served</p>
                </div>
            </div>
        </div>

        <!-- Current Trip Section -->
        <div class="row">
            <div class="col-md-8">
                <?php if ($current_trip): ?>
                    <div class="current-trip-card">
                        <h4><i class="fas fa-route me-2"></i>Current Trip</h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="route-path">
                                    <?php echo htmlspecialchars($current_trip['departure']); ?> 
                                    <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                    <?php echo htmlspecialchars($current_trip['destination']); ?>
                                </div>
                                <p class="mb-1"><strong>Bus:</strong> <?php echo htmlspecialchars($current_trip['plates_number']); ?> - <?php echo htmlspecialchars($current_trip['model']); ?></p>
                                <p class="mb-1"><strong>Price per Seat:</strong> <?php echo number_format($current_trip['price_per_seat']); ?> FRW</p>
                                <p class="mb-1"><strong>Available Seats:</strong> <?php echo $current_trip['available_seats']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <div class="trip-time-info">
                                    <p class="mb-1"><span class="time-label">Departure Time:</span><br>
                                    <span class="time-value"><?php echo date('M j, Y h:i A', strtotime($current_trip['departure_datetime'])); ?></span></p>
                                    
                                    <p class="mb-1"><span class="time-label">Estimated Arrival:</span><br>
                                    <span class="time-value">
                                        <?php 
                                        $arrival_time = isset($current_trip['calculated_estimated_arrival']) ? 
                                                       $current_trip['calculated_estimated_arrival'] : 
                                                       $current_trip['estimated_arrival'];
                                        echo date('M j, Y h:i A', strtotime($arrival_time)); 
                                        ?>
                                    </span></p>
                                    
                                    <p class="mb-0"><span class="time-label">Journey Duration:</span><br>
                                    <span class="time-value"><?php echo htmlspecialchars($current_trip['delay_time']); ?></span></p>
                                </div>
                                
                                <p class="mb-0 mt-2">
                                    <strong>Status:</strong> 
                                    <?php 
                                    $status_class = 'badge-available';
                                    switch($current_trip['status']) {
                                        case 'available': $status_class = 'badge-available'; break;
                                        case 'ontrip': $status_class = 'badge-ontrip'; break;
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo ucfirst($current_trip['status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Trip Progress -->
                        <div class="trip-timeline">
                            <h6><i class="fas fa-map-marker-alt me-2"></i>Trip Progress</h6>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar 
                                    <?php 
                                    if ($current_trip['status'] == 'available') echo 'bg-success';
                                    elseif ($current_trip['status'] == 'ontrip') echo 'bg-primary';
                                    ?>" 
                                    style="width: 
                                    <?php 
                                    if ($current_trip['status'] == 'available') echo '25%';
                                    elseif ($current_trip['status'] == 'ontrip') echo '75%';
                                    ?>">
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-4">
                                    <i class="fas fa-check-circle text-success"></i><br>
                                    <small>Available</small>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-road text-primary"></i><br>
                                    <small>On Trip</small>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-flag-checkered text-info"></i><br>
                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <form method="POST" onsubmit="return confirm('Are you sure you have arrived at the destination? This will notify the admin.');">
                                    <button type="submit" name="mark_arrived" class="btn btn-success w-100">
                                        <i class="fas fa-flag-checkered me-2"></i> Complete Trip
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#problemModal">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Report Problem
                                </button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="no-trip">
                        <i class="fas fa-road"></i>
                        <h4>No Active Trip</h4>
                        <p>You don't have any active trips scheduled at the moment.</p>
                        <p class="text-muted">Check back later or contact admin for new assignments.</p>
                    </div>
                <?php endif; ?>

                <!-- Recent Trips -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Trips</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($recent_trips_query->num_rows > 0): ?>
                            <?php while($trip = $recent_trips_query->fetch_assoc()): ?>
                                <div class="trip-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong><?php echo htmlspecialchars($trip['departure']); ?> → <?php echo htmlspecialchars($trip['destination']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <strong>Departure:</strong> <?php echo date('M j, Y h:i A', strtotime($trip['departure_datetime'])); ?>
                                            </small>
                                            <br>
                                            <?php if ($trip['status'] == 'arrived'): ?>
                                                <small class="text-success">
                                                    <strong>Arrival:</strong> <?php echo date('M j, Y h:i A', strtotime($trip['actual_arrival_time'])); ?>
                                                </small>
                                                <br>
                                                <small class="text-info">
                                                    <strong>Duration:</strong> <?php echo $trip['actual_duration']; ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-muted">
                                                    <strong>Estimated Arrival:</strong> <?php echo date('M j, Y h:i A', strtotime($trip['estimated_arrival'])); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <?php 
                                            $status_class = '';
                                            switch($trip['status']) {
                                                case 'available': $status_class = 'badge-available'; break;
                                                case 'ontrip': $status_class = 'badge-ontrip'; break;
                                                case 'arrived': $status_class = 'badge-arrived'; break;
                                                case 'maintenance': $status_class = 'badge-maintenance'; break;
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <?php echo ucfirst($trip['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted text-center">No recent trips found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Problem Report Section -->
            <div class="col-md-4">
                <div class="problem-report-card">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Emergency & Problems</h5>
                    <p class="text-muted">Report any issues during your trip immediately.</p>
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#problemModal">
                            <i class="fas fa-ambulance me-2"></i> Report Emergency
                        </button>
                        
                        <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#problemModal">
                            <i class="fas fa-tools me-2"></i> Report Mechanical Issue
                        </button>
                    </div>
                    
                    <div class="small text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        All reports are sent directly to admin for immediate action.
                    </div>
                </div>

                <!-- Quick Actions 
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#problemModal">
                                <i class="fas fa-traffic-light me-2"></i> Traffic Delay
                            </button>
                            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#problemModal">
                                <i class="fas fa-user-slash me-2"></i> Passenger Issue
                            </button>
                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#problemModal">
                                <i class="fas fa-route me-2"></i> Route Change
                            </button>
                        </div>
                    </div>
                </div>-->

                <!-- Driver Status -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Driver Status</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($driver['name']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($driver['contact']); ?></p>
                        <p><strong>License:</strong> <?php echo htmlspecialchars($driver['license']); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge <?php echo $driver['status'] == 'active' ? 'bg-success' : 'bg-warning'; ?>">
                                <?php echo ucfirst($driver['status']); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Problem Report Modal -->
    <div class="modal fade" id="problemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Report Problem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Problem Type</label>
                            <select name="problem_type" class="form-select" required>
                                <option value="">Select Problem Type</option>
                                <option value="emergency">Emergency Situation</option>
                                <option value="mechanical">Mechanical Issue</option>
                                <option value="traffic">Traffic Delay</option>
                                <option value="passenger">Passenger Issue</option>
                                <option value="route">Route Problem</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Please describe the problem in detail..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="report_problem" class="btn btn-danger">
                            <i class="fas fa-paper-plane me-2"></i> Send Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Auto-refresh page every 30 seconds to check for new trips
            setTimeout(() => {
                window.location.reload();
            }, 30000);
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>