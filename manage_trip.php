<?php
include 'connection.php';

// Get bus_id from URL
$bus_id = isset($_GET['bus_id']) ? $conn->real_escape_string($_GET['bus_id']) : '';

// Fetch bus details
$bus_data = [];
if ($bus_id) {
    $bus_query = $conn->query("SELECT * FROM buses WHERE bus_id = '$bus_id'");
    if ($bus_query->num_rows > 0) {
        $bus_data = $bus_query->fetch_assoc();
    } else {
        die("Bus not found!");
    }
} else {
    die("No bus specified!");
}

// Handle trip cancellation
if (isset($_GET['cancel_trip'])) {
    $trip_id = $conn->real_escape_string($_GET['cancel_trip']);
    
    $update_sql = "UPDATE trips SET status = 'cancelled' WHERE trip_id = '$trip_id' AND bus_id = '$bus_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $success_message = "Trip cancelled successfully!";
    } else {
        $error_message = "Error cancelling trip: " . $conn->error;
    }
}

// Handle trip completion
if (isset($_GET['complete_trip'])) {
    $trip_id = $conn->real_escape_string($_GET['complete_trip']);
    
    $update_sql = "UPDATE trips SET status = 'completed' WHERE trip_id = '$trip_id' AND bus_id = '$bus_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $success_message = "Trip marked as completed!";
    } else {
        $error_message = "Error completing trip: " . $conn->error;
    }
}

// Handle trip reactivation
if (isset($_GET['reactivate_trip'])) {
    $trip_id = $conn->real_escape_string($_GET['reactivate_trip']);
    
    $update_sql = "UPDATE trips SET status = 'scheduled' WHERE trip_id = '$trip_id' AND bus_id = '$bus_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $success_message = "Trip reactivated successfully!";
    } else {
        $error_message = "Error reactivating trip: " . $conn->error;
    }
}

// Fetch all trips for this bus
$trips_query = "SELECT t.*, 
                       r.departure, 
                       r.destination, 
                       r.price_per_seat,
                       d.name as driver_name,
                       d.contact as driver_contact,
                       COUNT(b.booking_id) as total_bookings,
                       COALESCE(SUM(b.number_of_seats), 0) as total_seats_booked
                FROM trips t
                JOIN routes r ON t.route_id = r.route_id
                JOIN drivers d ON t.driver_id = d.driver_id
                LEFT JOIN bookings b ON t.trip_id = b.trip_id AND b.booking_status = 'confirmed'
                WHERE t.bus_id = '$bus_id'
                GROUP BY t.trip_id
                ORDER BY t.departure_datetime DESC";

$trips_result = $conn->query($trips_query);

// Get trip statistics
$total_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE bus_id = '$bus_id'")->fetch_assoc()['total'];
$scheduled_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE bus_id = '$bus_id' AND status = 'scheduled'")->fetch_assoc()['total'];
$completed_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE bus_id = '$bus_id' AND status = 'completed'")->fetch_assoc()['total'];
$cancelled_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE bus_id = '$bus_id' AND status = 'cancelled'")->fetch_assoc()['total'];

// Calculate total revenue
$revenue_query = $conn->query("SELECT COALESCE(SUM(p.amount), 0) as total_revenue
                              FROM payments p
                              JOIN bookings b ON p.booking_id = b.booking_id
                              JOIN trips t ON b.trip_id = t.trip_id
                              WHERE t.bus_id = '$bus_id' AND p.payment_status = 'completed'");
$total_revenue = $revenue_query->fetch_assoc()['total_revenue'];
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
            background: linear-gradient(135deg, var(--info), #16a085);
            color: white;
        }
        
        .badge-boarding {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            color: white;
        }
        
        .badge-departed {
            background: linear-gradient(135deg, var(--secondary), #3498db);
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
        
        .badge-completed {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
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
        
        .bus-info-card {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .trip-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .trip-scheduled { border-left-color: var(--info); }
        .trip-boarding { border-left-color: var(--warning); }
        .trip-departed { border-left-color: var(--secondary); }
        .trip-arrived { border-left-color: var(--success); }
        .trip-cancelled { border-left-color: var(--danger); }
        .trip-completed { border-left-color: #95a5a6; }
        
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
        
        .time-badge {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .revenue-badge {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-radius: 6px;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
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
                            <a href="assign_bus.php?bus_id=<?php echo $bus_id; ?>" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i> Schedule New Trip
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

                        <!-- Bus Information -->
                        <div class="bus-info-card">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($bus_data['plates_number']); ?></h5>
                                    <p class="mb-0 text-muted"><?php echo htmlspecialchars($bus_data['model']); ?></p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Capacity:</strong> <?php echo $bus_data['number_of_seats']; ?> seats
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong> 
                                    <span class="status-badge <?php echo $bus_data['status'] == 'active' ? 'badge-scheduled' : 'badge-cancelled'; ?>">
                                        <?php echo ucfirst($bus_data['status']); ?>
                                    </span>
                                </div>
                                <div class="col-md-3 text-end">
                                    <a href="select_buses.php" class="back-btn">
                                        <i class="fas fa-arrow-left me-2"></i> Back to Buses
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
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
                                    <div class="stat-number text-success"><?php echo $completed_trips; ?></div>
                                    <p class="mb-0 text-muted">Completed</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-danger"><?php echo $cancelled_trips; ?></div>
                                    <p class="mb-0 text-muted">Cancelled</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-warning"><?php echo number_format($total_revenue); ?> FRW</div>
                                    <p class="mb-0 text-muted">Total Revenue</p>
                                </div>
                            </div>
                        </div>

                        <!-- Trips Table -->
                        <div class="table-responsive">
                            <?php if ($trips_result->num_rows > 0): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Trip ID</th>
                                            <th>Route</th>
                                            <th>Driver</th>
                                            <th>Schedule</th>
                                            <th>Seats</th>
                                            <th>Bookings</th>
                                            <th>Revenue</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($trip = $trips_result->fetch_assoc()): 
                                            // Calculate seat percentage
                                            $seat_percentage = ($trip['total_seats_booked'] / $bus_data['number_of_seats']) * 100;
                                            $seats_class = $seat_percentage > 80 ? 'seats-high' : ($seat_percentage > 50 ? 'seats-medium' : 'seats-low');
                                            
                                            // Determine trip card class
                                            $trip_card_class = 'trip-' . $trip['status'];
                                            
                                            // Calculate trip revenue
                                            $trip_revenue_query = $conn->query("SELECT COALESCE(SUM(p.amount), 0) as revenue
                                                                              FROM payments p
                                                                              JOIN bookings b ON p.booking_id = b.booking_id
                                                                              WHERE b.trip_id = '{$trip['trip_id']}' AND p.payment_status = 'completed'");
                                            $trip_revenue = $trip_revenue_query->fetch_assoc()['revenue'];
                                        ?>
                                        <tr class="trip-card <?php echo $trip_card_class; ?>">
                                            <td>
                                                <strong class="text-primary">#<?php echo $trip['trip_id']; ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($trip['departure']); ?> to <?php echo htmlspecialchars($trip['destination']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo number_format($trip['price_per_seat']); ?> FRW/seat</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($trip['driver_name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($trip['driver_contact']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="time-badge">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo date('M j, Y H:i', strtotime($trip['departure_datetime'])); ?>
                                                </div>
                                                <small class="text-muted">
                                                    Arrival: <?php echo date('M j, Y H:i', strtotime($trip['estimated_arrival'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold"><?php echo $trip['total_seats_booked']; ?>/<?php echo $bus_data['number_of_seats']; ?></span>
                                                </div>
                                                <div class="seats-indicator">
                                                    <div class="seats-filled <?php echo $seats_class; ?>" style="width: <?php echo min($seat_percentage, 100); ?>%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold"><?php echo $trip['total_bookings']; ?> booking(s)</span>
                                            </td>
                                            <td>
                                                <span class="revenue-badge">
                                                    <?php echo number_format($trip_revenue); ?> FRW
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                $status_class = '';
                                                $status_icon = '';
                                                switch($trip['status']) {
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
                                                    case 'completed': 
                                                        $status_class = 'badge-completed';
                                                        $status_icon = 'fa-check-circle';
                                                        break;
                                                    default: 
                                                        $status_class = 'badge-scheduled';
                                                        $status_icon = 'fa-question-circle';
                                                }
                                                ?>
                                                <span class="status-badge <?php echo $status_class; ?>">
                                                    <i class="fas <?php echo $status_icon; ?> me-1"></i>
                                                    <?php echo ucfirst($trip['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <!-- View Trip Details -->
                                                    <a href="trip_details.php?trip_id=<?php echo $trip['trip_id']; ?>" 
                                                       class="btn btn-info btn-sm"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <!-- Status Management -->
                                                    <?php if ($trip['status'] == 'scheduled'): ?>
                                                        <a href="manage_trips.php?bus_id=<?php echo $bus_id; ?>&cancel_trip=<?php echo $trip['trip_id']; ?>" 
                                                           class="btn btn-danger btn-sm"
                                                           title="Cancel Trip"
                                                           onclick="return confirm('Are you sure you want to cancel this trip?')">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    <?php elseif ($trip['status'] == 'cancelled'): ?>
                                                        <a href="manage_trips.php?bus_id=<?php echo $bus_id; ?>&reactivate_trip=<?php echo $trip['trip_id']; ?>" 
                                                           class="btn btn-success btn-sm"
                                                           title="Reactivate Trip"
                                                           onclick="return confirm('Are you sure you want to reactivate this trip?')">
                                                            <i class="fas fa-redo"></i>
                                                        </a>
                                                    <?php elseif ($trip['status'] == 'departed'): ?>
                                                        <a href="manage_trips.php?bus_id=<?php echo $bus_id; ?>&complete_trip=<?php echo $trip['trip_id']; ?>" 
                                                           class="btn btn-success btn-sm"
                                                           title="Mark as Completed"
                                                           onclick="return confirm('Mark this trip as completed?')">
                                                            <i class="fas fa-flag-checkered"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Edit Trip -->
                                                    <a href="edit_trip.php?trip_id=<?php echo $trip['trip_id']; ?>" 
                                                       class="btn btn-warning btn-sm"
                                                       title="Edit Trip">
                                                        <i class="fas fa-edit"></i>
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
                                    <p>This bus doesn't have any scheduled trips yet.</p>
                                    <a href="assign_bus.php?bus_id=<?php echo $bus_id; ?>" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-2"></i> Schedule First Trip
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
            const actionButtons = document.querySelectorAll('a[href*="cancel_trip"], a[href*="complete_trip"], a[href*="reactivate_trip"]');
            actionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const action = this.href.includes('cancel_trip') ? 'cancel' : 
                                  this.href.includes('complete_trip') ? 'complete' : 'reactivate';
                    const message = action === 'cancel' 
                        ? 'Are you sure you want to cancel this trip? All bookings will be cancelled.' 
                        : action === 'complete' 
                        ? 'Mark this trip as completed?'
                        : 'Are you sure you want to reactivate this trip?';
                    
                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                });
            });

            // Auto-refresh page every 2 minutes for real-time updates
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