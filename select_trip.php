<?php
include 'connection.php';

// Function to automatically update trip status based on schedule
function updateAutomaticTripStatus($conn) {
    $current_datetime = date('Y-m-d H:i:s');
    
    // Update trip status based on schedule
    $update_sql = "UPDATE trips SET status = 
        CASE 
            WHEN departure_datetime <= '$current_datetime' AND estimated_arrival > '$current_datetime' THEN 'departed'
            WHEN estimated_arrival <= '$current_datetime' THEN 'arrived'
            WHEN departure_datetime > DATE_ADD('$current_datetime', INTERVAL 30 MINUTE) THEN 'scheduled'
            WHEN departure_datetime <= DATE_ADD('$current_datetime', INTERVAL 30 MINUTE) AND departure_datetime > '$current_datetime' THEN 'boarding'
            ELSE status
        END";
    
    $conn->query($update_sql);
    
    // Update bus status based on trip status
    $bus_status_sql = "UPDATE buses b 
                      JOIN trips t ON b.bus_id = t.bus_id 
                      SET b.status = 
                      CASE 
                          WHEN t.status IN ('scheduled', 'boarding', 'departed') THEN 'On Trip'
                          WHEN t.status = 'arrived' THEN 'available'
                          WHEN t.status = 'cancelled' THEN 'available'
                          ELSE b.status
                      END
                      WHERE t.status IN ('scheduled', 'boarding', 'departed', 'arrived', 'cancelled')";
    
    return $conn->query($bus_status_sql);
}

// Run automatic status update on page load
updateAutomaticTripStatus($conn);

// Handle quick status update
if (isset($_GET['update_status'])) {
    $trip_id = $conn->real_escape_string($_GET['update_status']);
    $new_status = $conn->real_escape_string($_GET['status']);
    
    $update_sql = "UPDATE trips SET status = '$new_status' WHERE trip_id = '$trip_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        // Update bus status based on trip status change
        $trip_query = $conn->query("SELECT bus_id FROM trips WHERE trip_id = '$trip_id'");
        if ($trip_query->num_rows > 0) {
            $trip_data = $trip_query->fetch_assoc();
            $bus_id = $trip_data['bus_id'];
            
            $bus_status = 'available';
            if (in_array($new_status, ['scheduled', 'boarding', 'departed'])) {
                $bus_status = 'On Trip';
            }
            
            $conn->query("UPDATE buses SET status = '$bus_status' WHERE bus_id = '$bus_id'");
        }
        
        $success_message = "Trip #{$trip_id} status updated to {$new_status}!";
    } else {
        $error_message = "Error updating trip status: " . $conn->error;
    }
}

// Handle auto-refresh status
if (isset($_GET['refresh_status'])) {
    if (updateAutomaticTripStatus($conn)) {
        $success_message = "Trip statuses automatically updated based on current schedule!";
    } else {
        $error_message = "Error updating trip statuses automatically!";
    }
}

// Handle trip cancellation
if (isset($_GET['cancel_trip'])) {
    $trip_id = $conn->real_escape_string($_GET['cancel_trip']);
    
    $update_sql = "UPDATE trips SET status = 'cancelled' WHERE trip_id = '$trip_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        // Update bus status to available when trip is cancelled
        $trip_query = $conn->query("SELECT bus_id FROM trips WHERE trip_id = '$trip_id'");
        if ($trip_query->num_rows > 0) {
            $trip_data = $trip_query->fetch_assoc();
            $bus_id = $trip_data['bus_id'];
            $conn->query("UPDATE buses SET status = 'available' WHERE bus_id = '$bus_id'");
        }
        
        $success_message = "Trip #{$trip_id} has been cancelled!";
    } else {
        $error_message = "Error cancelling trip: " . $conn->error;
    }
}

// Handle trip reactivation
if (isset($_GET['reactivate_trip'])) {
    $trip_id = $conn->real_escape_string($_GET['reactivate_trip']);
    
    $update_sql = "UPDATE trips SET status = 'scheduled' WHERE trip_id = '$trip_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        // Update bus status to On Trip when trip is reactivated
        $trip_query = $conn->query("SELECT bus_id FROM trips WHERE trip_id = '$trip_id'");
        if ($trip_query->num_rows > 0) {
            $trip_data = $trip_query->fetch_assoc();
            $bus_id = $trip_data['bus_id'];
            $conn->query("UPDATE buses SET status = 'On Trip' WHERE bus_id = '$bus_id'");
        }
        
        $success_message = "Trip #{$trip_id} has been reactivated!";
    } else {
        $error_message = "Error reactivating trip: " . $conn->error;
    }
}

// Handle manual arrival from admin side (in case driver forgets)
if (isset($_GET['mark_arrived'])) {
    $trip_id = $conn->real_escape_string($_GET['mark_arrived']);
    
    $update_sql = "UPDATE trips SET status = 'arrived' WHERE trip_id = '$trip_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        // Update bus status to available
        $trip_query = $conn->query("SELECT bus_id FROM trips WHERE trip_id = '$trip_id'");
        if ($trip_query->num_rows > 0) {
            $trip_data = $trip_query->fetch_assoc();
            $bus_id = $trip_data['bus_id'];
            $conn->query("UPDATE buses SET status = 'available' WHERE bus_id = '$bus_id'");
        }
        
        $success_message = "Trip #{$trip_id} has been marked as arrived! Bus is now available.";
    } else {
        $error_message = "Error marking trip as arrived: " . $conn->error;
    }
}

// Fetch all trips with filtering
$status_filter = isset($_GET['status_filter']) ? $conn->real_escape_string($_GET['status_filter']) : '';
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT t.*, b.plates_number, b.model, b.status as bus_status, d.name as driver_name, r.departure, r.destination, r.price_per_seat 
        FROM trips t 
        LEFT JOIN buses b ON t.bus_id = b.bus_id 
        LEFT JOIN drivers d ON t.driver_id = d.driver_id 
        LEFT JOIN routes r ON t.route_id = r.route_id 
        WHERE 1=1";

if (!empty($status_filter)) {
    $sql .= " AND t.status = '$status_filter'";
}

if (!empty($search_query)) {
    $sql .= " AND (b.plates_number LIKE '%$search_query%' OR d.name LIKE '%$search_query%' OR r.departure LIKE '%$search_query%' OR r.destination LIKE '%$search_query%')";
}

$sql .= " ORDER BY 
    CASE 
        WHEN t.status = 'scheduled' THEN 1
        WHEN t.status = 'boarding' THEN 2
        WHEN t.status = 'departed' THEN 3
        WHEN t.status = 'arrived' THEN 4
        WHEN t.status = 'cancelled' THEN 5
        ELSE 6
    END, t.departure_datetime ASC";

$result = $conn->query($sql);

// Get stats for dashboard
$total_trips = $conn->query("SELECT COUNT(*) as total FROM trips")->fetch_assoc()['total'];
$scheduled_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE status = 'scheduled'")->fetch_assoc()['total'];
$boarding_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE status = 'boarding'")->fetch_assoc()['total'];
$departed_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE status = 'departed'")->fetch_assoc()['total'];
$arrived_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE status = 'arrived'")->fetch_assoc()['total'];
$cancelled_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE status = 'cancelled'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trips - SwiftPass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #c0392b);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-info {
            background: linear-gradient(135deg, var(--info), #16a085);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover, .btn-warning:hover, .btn-danger:hover, .btn-info:hover {
            transform: translateY(-2px);
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .back-btn {
            background: var(--warning);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: #e67e22;
            color: white;
            transform: translateY(-1px);
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        
        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-scheduled {
            background: linear-gradient(135deg, var(--info), #138496);
            color: white;
        }
        
        .badge-boarding {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            color: white;
        }
        
        .badge-departed {
            background: linear-gradient(135deg, var(--primary), #2c3e50);
            color: white;
        }
        
        .badge-arrived {
            background: linear-gradient(135deg, var(--success), #27ae60);
            color: white;
        }
        
        .badge-cancelled {
            background: linear-gradient(135deg, var(--danger), #c0392b);
            color: white;
        }
        
        .bus-status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-available {
            background: linear-gradient(135deg, var(--success), #27ae60);
            color: white;
        }
        
        .badge-ontrip {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            color: white;
        }
        
        .badge-maintenance {
            background: linear-gradient(135deg, var(--danger), #c0392b);
            color: white;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .no-trips {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .no-trips i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #bdc3c7;
        }
        
        .filter-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .seats-indicator {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        
        .seats-filled {
            height: 100%;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .seats-low { background: linear-gradient(135deg, var(--success), #27ae60); }
        .seats-medium { background: linear-gradient(135deg, var(--warning), #e67e22); }
        .seats-high { background: linear-gradient(135deg, var(--danger), #c0392b); }
        
        .trip-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .trip-card-scheduled { border-left-color: var(--info); }
        .trip-card-boarding { border-left-color: var(--warning); }
        .trip-card-departed { border-left-color: var(--primary); }
        .trip-card-arrived { border-left-color: var(--success); }
        .trip-card-cancelled { border-left-color: var(--danger); }
        
        .quick-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .auto-status-info {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .route-path {
            font-weight: 600;
            color: var(--primary);
        }
        
        .bus-info {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .driver-info {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .datetime-info {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .price-badge {
            background: linear-gradient(135deg, var(--info), #138496);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><i class="fas fa-route me-2"></i>Manage Trips</h3>
                        <div>
                            <a href="select_trip.php?refresh_status=true" class="btn btn-info me-2">
                                <i class="fas fa-sync-alt me-2"></i> Refresh Status
                            </a>
                            <a href="add_trip.php" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i> Add New Trip
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
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

                        <!-- Auto Status Info -->
                        <div class="auto-status-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Automatic Status Updates:</strong> Trip statuses are automatically updated based on current schedule. 
                            Scheduled → Boarding (30 min before departure) → Departed → Arrived
                            <br>
                            <small class="text-muted">Bus status automatically changes to "On Trip" when assigned and "Available" when trip is completed.</small>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-primary"><?php echo $total_trips; ?></div>
                                    <p class="mb-0 text-muted">Total Trips</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-info"><?php echo $scheduled_trips; ?></div>
                                    <p class="mb-0 text-muted">Scheduled</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-warning"><?php echo $boarding_trips; ?></div>
                                    <p class="mb-0 text-muted">Boarding</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-secondary"><?php echo $departed_trips; ?></div>
                                    <p class="mb-0 text-muted">Departed</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-success"><?php echo $arrived_trips; ?></div>
                                    <p class="mb-0 text-muted">Arrived</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-danger"><?php echo $cancelled_trips; ?></div>
                                    <p class="mb-0 text-muted">Cancelled</p>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="filter-card">
                            <form method="GET" action="">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Status Filter</label>
                                        <select name="status_filter" class="form-select" onchange="this.form.submit()">
                                            <option value="">All Statuses</option>
                                            <option value="scheduled" <?php echo $status_filter == 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                            <option value="boarding" <?php echo $status_filter == 'boarding' ? 'selected' : ''; ?>>Boarding</option>
                                            <option value="departed" <?php echo $status_filter == 'departed' ? 'selected' : ''; ?>>Departed</option>
                                            <option value="arrived" <?php echo $status_filter == 'arrived' ? 'selected' : ''; ?>>Arrived</option>
                                            <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Search Trips</label>
                                        <input type="text" name="search" class="form-control" placeholder="Search by bus plate, driver, departure, or destination..." value="<?php echo htmlspecialchars($search_query); ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-2"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Trips Table -->
                        <div class="table-responsive">
                            <?php if ($result->num_rows > 0): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Trip Details</th>
                                            <th>Bus & Driver</th>
                                            <th>Schedule</th>
                                            <th>Seats</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result->fetch_assoc()): 
                                            // Safe division for seats percentage
                                            $bus_capacity_sql = "SELECT number_of_seats FROM buses WHERE bus_id = '{$row['bus_id']}'";
                                            $capacity_result = $conn->query($bus_capacity_sql);
                                            $total_seats = 1;
                                            if ($capacity_result->num_rows > 0) {
                                                $capacity_data = $capacity_result->fetch_assoc();
                                                $total_seats = $capacity_data['number_of_seats'] > 0 ? $capacity_data['number_of_seats'] : 1;
                                            }
                                            $seats_percentage = ($row['available_seats'] / $total_seats) * 100;
                                            $seats_class = $seats_percentage > 50 ? 'seats-low' : ($seats_percentage > 20 ? 'seats-medium' : 'seats-high');
                                            
                                            // Determine trip card class
                                            $trip_card_class = 'trip-card-' . $row['status'];
                                            
                                            // Bus status badge
                                            $bus_status_class = '';
                                            $bus_status_text = '';
                                            switch($row['bus_status']) {
                                                case 'available': 
                                                    $bus_status_class = 'badge-available';
                                                    $bus_status_text = 'Available';
                                                    break;
                                                case 'On Trip': 
                                                    $bus_status_class = 'badge-ontrip';
                                                    $bus_status_text = 'On Trip';
                                                    break;
                                                case 'maintenance': 
                                                    $bus_status_class = 'badge-maintenance';
                                                    $bus_status_text = 'Maintenance';
                                                    break;
                                                default: 
                                                    $bus_status_class = 'badge-available';
                                                    $bus_status_text = 'Available';
                                            }
                                        ?>
                                        <tr class="trip-card <?php echo $trip_card_class; ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <strong class="text-primary">#<?php echo $row['trip_id']; ?></strong>
                                                    </div>
                                                    <div>
                                                        <div class="route-path">
                                                            <?php echo htmlspecialchars($row['departure']); ?> 
                                                            <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                            <?php echo htmlspecialchars($row['destination']); ?>
                                                        </div>
                                                        <div class="price-badge mt-1">
                                                            <?php echo number_format($row['price_per_seat']); ?> FRW
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="bus-info">
                                                    <strong>Bus:</strong> <?php echo htmlspecialchars($row['plates_number']); ?> - <?php echo htmlspecialchars($row['model']); ?>
                                                    <span class="bus-status-badge <?php echo $bus_status_class; ?> ms-1">
                                                        <?php echo $bus_status_text; ?>
                                                    </span>
                                                </div>
                                                <div class="driver-info">
                                                    <strong>Driver:</strong> <?php echo htmlspecialchars($row['driver_name']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="datetime-info">
                                                    <strong>Departure:</strong><br>
                                                    <?php echo date('M j, Y h:i A', strtotime($row['departure_datetime'])); ?>
                                                    <br>
                                                    <strong>Arrival:</strong><br>
                                                    <?php echo date('M j, Y h:i A', strtotime($row['estimated_arrival'])); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold"><?php echo $row['available_seats']; ?>/<?php echo $total_seats; ?></span>
                                                </div>
                                                <div class="seats-indicator">
                                                    <div class="seats-filled <?php echo $seats_class; ?>" style="width: <?php echo $seats_percentage; ?>%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                $status_class = '';
                                                $status_icon = '';
                                                switch($row['status']) {
                                                    case 'scheduled': 
                                                        $status_class = 'badge-scheduled';
                                                        $status_icon = 'fa-clock';
                                                        break;
                                                    case 'boarding': 
                                                        $status_class = 'badge-boarding';
                                                        $status_icon = 'fa-users';
                                                        break;
                                                    case 'departed': 
                                                        $status_class = 'badge-departed';
                                                        $status_icon = 'fa-road';
                                                        break;
                                                    case 'arrived': 
                                                        $status_class = 'badge-arrived';
                                                        $status_icon = 'fa-flag-checkered';
                                                        break;
                                                    case 'cancelled': 
                                                        $status_class = 'badge-cancelled';
                                                        $status_icon = 'fa-times-circle';
                                                        break;
                                                    default: 
                                                        $status_class = 'badge-scheduled';
                                                        $status_icon = 'fa-question-circle';
                                                }
                                                ?>
                                                <span class="status-badge <?php echo $status_class; ?>">
                                                    <i class="fas <?php echo $status_icon; ?> me-1"></i>
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <!-- Quick Status Actions -->
                                                    <?php if ($row['status'] == 'scheduled'): ?>
                                                        <a href="select_trip.php?update_status=<?php echo $row['trip_id']; ?>&status=boarding" 
                                                           class="btn btn-warning btn-sm"
                                                           title="Start Boarding">
                                                            <i class="fas fa-users"></i> Board
                                                        </a>
                                                    <?php elseif ($row['status'] == 'boarding'): ?>
                                                        <a href="select_trip.php?update_status=<?php echo $row['trip_id']; ?>&status=departed" 
                                                           class="btn btn-info btn-sm"
                                                           title="Mark as Departed">
                                                            <i class="fas fa-play"></i> Depart
                                                        </a>
                                                    <?php elseif ($row['status'] == 'departed'): ?>
                                                        <a href="select_trip.php?update_status=<?php echo $row['trip_id']; ?>&status=arrived" 
                                                           class="btn btn-success btn-sm"
                                                           title="Mark as Arrived">
                                                            <i class="fas fa-flag-checkered"></i> Arrive
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Manual Arrival Mark (for admin override) -->
                                                    <?php if (in_array($row['status'], ['scheduled', 'boarding', 'departed'])): ?>
                                                        <a href="select_trip.php?mark_arrived=<?php echo $row['trip_id']; ?>" 
                                                           class="btn btn-success btn-sm"
                                                           title="Mark as Arrived (Admin Override)"
                                                           onclick="return confirm('Mark this trip as arrived? Bus will become available for new trips.')">
                                                            <i class="fas fa-flag-checkered"></i> Arrived
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Cancel/Reactivate Actions -->
                                                    <?php if ($row['status'] != 'cancelled' && $row['status'] != 'arrived'): ?>
                                                        <a href="select_trip.php?cancel_trip=<?php echo $row['trip_id']; ?>" 
                                                           class="btn btn-danger btn-sm"
                                                           title="Cancel Trip">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </a>
                                                    <?php elseif ($row['status'] == 'cancelled'): ?>
                                                        <a href="select_trip.php?reactivate_trip=<?php echo $row['trip_id']; ?>" 
                                                           class="btn btn-success btn-sm"
                                                           title="Reactivate Trip">
                                                            <i class="fas fa-play"></i> Reactivate
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Quick Status Update Dropdown -->
                                                    <div class="dropdown">
                                                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="select_trip.php?update_status=<?php echo $row['trip_id']; ?>&status=scheduled">Set Scheduled</a></li>
                                                            <li><a class="dropdown-item" href="select_trip.php?update_status=<?php echo $row['trip_id']; ?>&status=boarding">Set Boarding</a></li>
                                                            <li><a class="dropdown-item" href="select_trip.php?update_status=<?php echo $row['trip_id']; ?>&status=departed">Set Departed</a></li>
                                                            <li><a class="dropdown-item" href="select_trip.php?update_status=<?php echo $row['trip_id']; ?>&status=arrived">Set Arrived</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item" href="select_trip.php?update_status=<?php echo $row['trip_id']; ?>&status=cancelled">Set Cancelled</a></li>
                                                        </ul>
                                                    </div>
                                                    
                                                    <!-- Edit Button -->
                                                    <a href="update_trips.php?edit_id=<?php echo $row['trip_id']; ?>" 
                                                       class="btn btn-warning btn-sm"
                                                       title="Edit Trip">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <!-- Delete Button -->
                                                    <a href="select_trip.php?delete_id=<?php echo $row['trip_id']; ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Are you sure you want to delete this trip?')"
                                                       title="Delete Trip">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="no-trips">
                                    <i class="fas fa-route"></i>
                                    <h4>No Trips Found</h4>
                                    <p>You haven't scheduled any trips yet or no trips match your search criteria.</p>
                                    <a href="add_trip.php" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-2"></i> Schedule Your First Trip
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

            // Add confirmation for actions
            const actionButtons = document.querySelectorAll('a[href*="update_status"], a[href*="cancel_trip"], a[href*="reactivate_trip"], a[href*="mark_arrived"]');
            actionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    let message = 'Are you sure you want to perform this action?';
                    
                    if (this.href.includes('cancel_trip')) {
                        message = 'Are you sure you want to cancel this trip?';
                    } else if (this.href.includes('reactivate_trip')) {
                        message = 'Are you sure you want to reactivate this trip?';
                    } else if (this.href.includes('mark_arrived')) {
                        message = 'Mark this trip as arrived? This will make the bus available for new trips.';
                    } else if (this.href.includes('update_status')) {
                        const status = new URL(this.href).searchParams.get('status');
                        message = `Are you sure you want to change trip status to ${status}?`;
                    }
                    
                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                });
            });

            // Auto-refresh page every 2 minutes to update statuses
            setTimeout(() => {
                window.location.reload();
            }, 120000); // 2 minutes
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>