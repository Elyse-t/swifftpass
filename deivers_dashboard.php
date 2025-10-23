<?php
session_start();
include('connection.php');

// Check if driver is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: login.php");
    exit;
}

$driver_id = $_SESSION['user_id'];

// Get driver details
$driver_sql = "SELECT * FROM drivers WHERE driver_id = '$driver_id'";
$driver_result = $conn->query($driver_sql);
$driver_data = $driver_result->fetch_assoc();

// Get driver's assigned bus
$bus_sql = "SELECT b.* FROM buses b 
            INNER JOIN drivers d ON b.bus_id = d.bus_id 
            WHERE d.driver_id = '$driver_id'";
$bus_result = $conn->query($bus_sql);
$bus_data = $bus_result->fetch_assoc();

// Get today's trips for this driver
$today = date('Y-m-d');
$trips_sql = "SELECT t.*, r.departure, r.destination, r.price_per_seat,
                     b.plates_number, b.model,
                     COUNT(DISTINCT bk.booking_id) as passenger_count
              FROM trips t
              LEFT JOIN routes r ON t.route_id = r.route_id
              LEFT JOIN buses b ON t.bus_id = b.bus_id
              LEFT JOIN bookings bk ON t.trip_id = bk.trip_id AND bk.status = 'confirmed'
              WHERE t.driver_id = '$driver_id' 
              AND DATE(t.departure_datetime) = '$today'
              GROUP BY t.trip_id
              ORDER BY t.departure_datetime ASC";
$trips_result = $conn->query($trips_sql);

// Handle trip status updates
if (isset($_GET['update_trip_status'])) {
    $trip_id = $conn->real_escape_string($_GET['update_trip_status']);
    $new_status = $conn->real_escape_string($_GET['status']);
    
    $update_sql = "UPDATE trips SET status = '$new_status' WHERE trip_id = '$trip_id' AND driver_id = '$driver_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $success_message = "Trip status updated to " . ucfirst($new_status) . "!";
    } else {
        $error_message = "Error updating trip status: " . $conn->error;
    }
    
    // Refresh the page to show updated status
    header("Location: driver_dashboard.php");
    exit;
}

// Get performance stats
$completed_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE driver_id = '$driver_id' AND status = 'arrived'")->fetch_assoc()['total'];
$today_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE driver_id = '$driver_id' AND DATE(departure_datetime) = '$today'")->fetch_assoc()['total'];
$on_time_rate = $conn->query("SELECT 
    ROUND((COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, departure_datetime, actual_departure) <= 5 THEN 1 END) / COUNT(*)) * 100, 1) as rate 
    FROM trips 
    WHERE driver_id = '$driver_id' AND status = 'arrived'")->fetch_assoc()['rate'];
$on_time_rate = $on_time_rate ?: 100;

// Get next trip
$next_trip_sql = "SELECT t.*, r.departure, r.destination 
                  FROM trips t 
                  LEFT JOIN routes r ON t.route_id = r.route_id 
                  WHERE t.driver_id = '$driver_id' 
                  AND t.status IN ('scheduled', 'boarding')
                  AND t.departure_datetime > NOW()
                  ORDER BY t.departure_datetime ASC 
                  LIMIT 1";
$next_trip_result = $conn->query($next_trip_sql);
$next_trip = $next_trip_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - SwiftPass</title>
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
            color: #fff;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.25rem;
            font-weight: 600;
        }
        
        .driver-profile {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 20px;
        }
        
        .stats-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success), #27ae60);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            transition: all 0.3s ease;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            transition: all 0.3s ease;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #c0392b);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            transition: all 0.3s ease;
        }
        
        .btn-info {
            background: linear-gradient(135deg, var(--info), #16a085);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover, .btn-warning:hover, .btn-danger:hover, .btn-info:hover {
            transform: translateY(-2px);
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
        
        .trip-card {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }
        
        .trip-card-scheduled { border-left-color: var(--info); }
        .trip-card-boarding { border-left-color: var(--warning); }
        .trip-card-departed { border-left-color: var(--primary); }
        .trip-card-arrived { border-left-color: var(--success); }
        
        .progress {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
        }
        
        .progress-bar {
            border-radius: 4px;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        
        .action-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .action-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .action-btn i {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .next-trip-card {
            background: linear-gradient(135deg, var(--info), #16a085);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
        }
        
        .countdown {
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin: 1rem 0;
        }
        
        .passenger-count {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .time-display {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .route-path {
            font-weight: 600;
            color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 10px;
            }
            
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="driver-profile">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Welcome, <?php echo htmlspecialchars($driver_data['name']); ?>! 👋</h2>
                    <p class="mb-0">Ready for today's journeys</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="row">
                        <div class="col-6">
                            <strong>Bus:</strong> <?php echo htmlspecialchars($bus_data['plates_number']); ?><br>
                            <small><?php echo htmlspecialchars($bus_data['model']); ?></small>
                        </div>
                        <div class="col-6">
                            <strong>License:</strong> <?php echo htmlspecialchars($driver_data['license_number']); ?><br>
                            <small><?php echo htmlspecialchars($driver_data['phone']); ?></small>
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

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt me-2"></i>Quick Actions
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <div class="action-btn" onclick="location.href='driver_trips.php'">
                        <i class="fas fa-route"></i>
                        <div>My Trips</div>
                    </div>
                    <div class="action-btn" onclick="location.href='driver_schedule.php'">
                        <i class="fas fa-calendar"></i>
                        <div>Schedule</div>
                    </div>
                    <div class="action-btn" onclick="location.href='report_issue.php'">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>Report Issue</div>
                    </div>
                    <div class="action-btn" onclick="location.href='driver_profile.php'">
                        <i class="fas fa-user"></i>
                        <div>Profile</div>
                    </div>
                    <div class="action-btn" onclick="location.href='logout.php'">
                        <i class="fas fa-sign-out-alt"></i>
                        <div>Logout</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Performance Stats -->
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number text-primary"><?php echo $completed_trips; ?></div>
                    <p class="mb-0 text-muted">Total Trips</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number text-success"><?php echo $today_trips; ?></div>
                    <p class="mb-0 text-muted">Today's Trips</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number text-warning"><?php echo $on_time_rate; ?>%</div>
                    <p class="mb-0 text-muted">On Time Rate</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stat-number text-info"><?php echo $bus_data['number_of_seats']; ?></div>
                    <p class="mb-0 text-muted">Bus Capacity</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Next Trip -->
            <?php if ($next_trip): ?>
            <div class="col-md-4">
                <div class="next-trip-card">
                    <h5><i class="fas fa-clock me-2"></i>Next Trip</h5>
                    <div class="route-path mb-2">
                        <?php echo htmlspecialchars($next_trip['departure']); ?> 
                        <i class="fas fa-arrow-right mx-2"></i>
                        <?php echo htmlspecialchars($next_trip['destination']); ?>
                    </div>
                    <div class="countdown" id="countdown">
                        <!-- Countdown will be populated by JavaScript -->
                    </div>
                    <div class="time-display">
                        <i class="fas fa-calendar me-1"></i>
                        <?php echo date('M j, Y', strtotime($next_trip['departure_datetime'])); ?>
                        <br>
                        <i class="fas fa-clock me-1"></i>
                        <?php echo date('h:i A', strtotime($next_trip['departure_datetime'])); ?>
                    </div>
                    <?php if ($next_trip['status'] == 'scheduled'): ?>
                        <a href="driver_dashboard.php?update_trip_status=<?php echo $next_trip['trip_id']; ?>&status=boarding" 
                           class="btn btn-warning btn-sm mt-2 w-100">
                            <i class="fas fa-users me-2"></i>Start Boarding
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Today's Schedule -->
            <div class="<?php echo $next_trip ? 'col-md-8' : 'col-12'; ?>">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-calendar-day me-2"></i>Today's Schedule</span>
                        <span class="badge bg-primary"><?php echo date('F j, Y'); ?></span>
                    </div>
                    <div class="card-body">
                        <?php if ($trips_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Route</th>
                                            <th>Time</th>
                                            <th>Passengers</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($trip = $trips_result->fetch_assoc()): 
                                            $status_class = 'badge-' . $trip['status'];
                                            $trip_card_class = 'trip-card-' . $trip['status'];
                                        ?>
                                        <tr class="<?php echo $trip_card_class; ?>">
                                            <td>
                                                <div class="route-path">
                                                    <?php echo htmlspecialchars($trip['departure']); ?> 
                                                    <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                                    <?php echo htmlspecialchars($trip['destination']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="time-display">
                                                    <?php echo date('h:i A', strtotime($trip['departure_datetime'])); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="passenger-count">
                                                    <i class="fas fa-users me-1"></i>
                                                    <?php echo $trip['passenger_count']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo $status_class; ?>">
                                                    <?php echo ucfirst($trip['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <?php if ($trip['status'] == 'scheduled'): ?>
                                                        <a href="driver_dashboard.php?update_trip_status=<?php echo $trip['trip_id']; ?>&status=boarding" 
                                                           class="btn btn-warning btn-sm">
                                                            <i class="fas fa-users"></i> Board
                                                        </a>
                                                    <?php elseif ($trip['status'] == 'boarding'): ?>
                                                        <a href="driver_dashboard.php?update_trip_status=<?php echo $trip['trip_id']; ?>&status=departed" 
                                                           class="btn btn-info btn-sm">
                                                            <i class="fas fa-play"></i> Depart
                                                        </a>
                                                    <?php elseif ($trip['status'] == 'departed'): ?>
                                                        <a href="driver_dashboard.php?update_trip_status=<?php echo $trip['trip_id']; ?>&status=arrived" 
                                                           class="btn btn-success btn-sm">
                                                            <i class="fas fa-flag-checkered"></i> Arrive
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Completed</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Trips Scheduled Today</h5>
                                <p class="text-muted">You're all caught up! Enjoy your day off.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bus Status -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-bus me-2"></i>Bus Information
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <strong>Plates:</strong><br>
                                <h4 class="text-primary"><?php echo htmlspecialchars($bus_data['plates_number']); ?></h4>
                            </div>
                            <div class="col-6">
                                <strong>Model:</strong><br>
                                <h5><?php echo htmlspecialchars($bus_data['model']); ?></h5>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <strong>Capacity:</strong><br>
                                <span class="fw-bold"><?php echo $bus_data['number_of_seats']; ?> seats</span>
                            </div>
                            <div class="col-6">
                                <strong>Status:</strong><br>
                                <span class="status-badge badge-success">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-2"></i>Performance
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>On-time Performance</span>
                                <span><?php echo $on_time_rate; ?>%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: <?php echo $on_time_rate; ?>%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Trip Completion</span>
                                <span>100%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: 100%"></div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">Last updated: <?php echo date('h:i A'); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Countdown for next trip
        <?php if ($next_trip): ?>
        function updateCountdown() {
            const tripTime = new Date('<?php echo $next_trip['departure_datetime']; ?>').getTime();
            const now = new Date().getTime();
            const distance = tripTime - now;
            
            if (distance < 0) {
                document.getElementById('countdown').innerHTML = "DEPARTURE TIME";
                return;
            }
            
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('countdown').innerHTML = 
                `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
        <?php endif; ?>

        // Auto-refresh dashboard every 30 seconds
        setTimeout(() => {
            window.location.reload();
        }, 30000);

        // Add confirmation for status changes
        document.addEventListener('DOMContentLoaded', function() {
            const actionButtons = document.querySelectorAll('a[href*="update_trip_status"]');
            actionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const status = new URL(this.href).searchParams.get('status');
                    const message = `Are you sure you want to change trip status to ${status}?`;
                    
                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                });
            });
            
            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>