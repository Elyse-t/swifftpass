<?php
include 'connection.php';

// Function to automatically update bus status based on trip status
function updateAutomaticBusStatus($conn) {
    // Update bus status based on active trips
    $update_sql = "UPDATE buses b 
                   LEFT JOIN trips t ON b.bus_id = t.bus_id 
                   SET b.status = 
                       CASE 
                           WHEN b.status = 'maintenance' THEN 'maintenance'
                           WHEN b.status = 'inactive' THEN 'inactive'
                           WHEN t.trip_id IS NOT NULL AND t.status IN ('scheduled', 'boarding', 'departed') THEN 'On Trip'
                           ELSE 'active'
                       END";
    
    return $conn->query($update_sql);
}

// Run automatic status update on page load
updateAutomaticBusStatus($conn);

// Handle bus assignment for new trip
if (isset($_GET['assign_bus'])) {
    $bus_id = $conn->real_escape_string($_GET['assign_bus']);
    
    // Fetch bus details
    $bus_sql = "SELECT * FROM buses WHERE bus_id = '$bus_id'";
    $bus_result = $conn->query($bus_sql);
    
    if ($bus_result->num_rows > 0) {
        $bus_data = $bus_result->fetch_assoc();
        
        // Update bus status to "On Trip" - This would be handled by trips table now
        $update_sql = "UPDATE buses SET status = 'On Trip' WHERE bus_id = '$bus_id'";
        
        if ($conn->query($update_sql) === TRUE) {
            $success_message = "Bus {$bus_data['plates_number']} successfully assigned to new trip!";
        } else {
            $error_message = "Error assigning bus: " . $conn->error;
        }
    } else {
        $error_message = "Bus not found!";
    }
}

// Handle bus release (make available again)
if (isset($_GET['release_bus'])) {
    $bus_id = $conn->real_escape_string($_GET['release_bus']);
    
    $update_sql = "UPDATE buses SET status = 'active' WHERE bus_id = '$bus_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $success_message = "Bus released and now available for new trips!";
    } else {
        $error_message = "Error releasing bus: " . $conn->error;
    }
}

// Handle quick status update
if (isset($_GET['update_status'])) {
    $bus_id = $conn->real_escape_string($_GET['update_status']);
    $new_status = $conn->real_escape_string($_GET['status']);
    
    $update_sql = "UPDATE buses SET status = '$new_status' WHERE bus_id = '$bus_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $success_message = "Bus status updated to {$new_status}!";
    } else {
        $error_message = "Error updating bus status: " . $conn->error;
    }
}

// Handle auto-refresh status
if (isset($_GET['refresh_status'])) {
    if (updateAutomaticBusStatus($conn)) {
        $success_message = "Bus statuses automatically updated based on current trips!";
    } else {
        $error_message = "Error updating bus statuses automatically!";
    }
}

// Fetch all buses with filtering and trip information
$status_filter = isset($_GET['status_filter']) ? $conn->real_escape_string($_GET['status_filter']) : '';
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT b.*, 
               COUNT(CASE WHEN t.status IN ('scheduled', 'boarding', 'departed') THEN t.trip_id END) as active_trips,
               COUNT(CASE WHEN t.status = 'arrived' THEN t.trip_id END) as completed_trips,
               COALESCE(SUM(CASE WHEN t.status IN ('scheduled', 'boarding', 'departed') THEN t.available_seats ELSE 0 END), 0) as total_available_seats,
               GROUP_CONCAT(DISTINCT 
                   CASE WHEN t.status IN ('scheduled', 'boarding', 'departed') THEN 
                       CONCAT(r.departure, ' → ', r.destination) 
                   END SEPARATOR ', ') as active_routes
        FROM buses b 
        LEFT JOIN trips t ON b.bus_id = t.bus_id 
        LEFT JOIN routes r ON t.route_id = r.route_id
        WHERE 1=1";

if (!empty($status_filter)) {
    $sql .= " AND b.status = '$status_filter'";
}

if (!empty($search_query)) {
    $sql .= " AND (b.plates_number LIKE '%$search_query%' OR b.model LIKE '%$search_query%')";
}

$sql .= " GROUP BY b.bus_id
          ORDER BY 
            CASE 
                WHEN b.status = 'active' THEN 1
                WHEN b.status = 'On Trip' THEN 2
                WHEN b.status = 'maintenance' THEN 3
                WHEN b.status = 'inactive' THEN 4
                ELSE 5
            END, b.created_at DESC";

$result = $conn->query($sql);

// Get stats for dashboard
$total_buses = $conn->query("SELECT COUNT(*) as total FROM buses")->fetch_assoc()['total'];
$active_buses = $conn->query("SELECT COUNT(*) as total FROM buses WHERE status = 'active'")->fetch_assoc()['total'];
$on_trip_buses = $conn->query("SELECT COUNT(*) as total FROM buses WHERE status = 'On Trip'")->fetch_assoc()['total'];
$maintenance_buses = $conn->query("SELECT COUNT(*) as total FROM buses WHERE status = 'maintenance'")->fetch_assoc()['total'];
$inactive_buses = $conn->query("SELECT COUNT(*) as total FROM buses WHERE status = 'inactive'")->fetch_assoc()['total'];

// Get buses with active trips (for accurate counting)
$buses_with_active_trips = $conn->query("SELECT COUNT(DISTINCT b.bus_id) as total 
                                  FROM buses b 
                                  JOIN trips t ON b.bus_id = t.bus_id 
                                  WHERE t.status IN ('scheduled', 'boarding', 'departed')")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Buses - SwiftPass</title>
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
        
        .badge-active {
            background: linear-gradient(135deg, var(--success), #27ae60);
            color: white;
        }
        
        .badge-maintenance {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            color: white;
        }
        
        .badge-inactive {
            background: linear-gradient(135deg, var(--danger), #c0392b);
            color: white;
        }
        
        .badge-on-trip {
            background: linear-gradient(135deg, var(--info), #16a085);
            color: white;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .no-buses {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .no-buses i {
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
        
        .bus-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .bus-card-active { border-left-color: var(--success); }
        .bus-card-maintenance { border-left-color: var(--warning); }
        .bus-card-inactive { border-left-color: var(--danger); }
        .bus-card-on-trip { border-left-color: var(--info); }
        
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
        
        .trip-indicator {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .route-info {
            font-size: 0.75rem;
            color: #666;
            margin-top: 0.25rem;
        }
        
        .driver-status-info {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><i class="fas fa-bus me-2"></i>Manage Buses</h3>
                        <div>
                            <a href="select_buses.php?refresh_status=true" class="btn btn-info me-2">
                                <i class="fas fa-sync-alt me-2"></i> Refresh Status
                            </a>
                            <a href="add_buses.php" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i> Add New Bus
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

                        <!-- Driver Status Integration Info -->
                        <div class="driver-status-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Driver Integration:</strong> Bus status automatically updates when drivers mark trips as arrived in their dashboard. 
                            "On Trip" → "Active" (when driver arrives at destination)
                        </div>

                        <!-- Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-primary"><?php echo $total_buses; ?></div>
                                    <p class="mb-0 text-muted">Total Buses</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-success"><?php echo $active_buses; ?></div>
                                    <p class="mb-0 text-muted">Available</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-info"><?php echo $on_trip_buses; ?></div>
                                    <p class="mb-0 text-muted">On Trip</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-warning"><?php echo $maintenance_buses; ?></div>
                                    <p class="mb-0 text-muted">Maintenance</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-danger"><?php echo $inactive_buses; ?></div>
                                    <p class="mb-0 text-muted">Inactive</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stats-card text-center">
                                    <div class="stat-number text-secondary"><?php echo $buses_with_active_trips; ?></div>
                                    <p class="mb-0 text-muted">Active Trips</p>
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
                                            <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Available</option>
                                            <option value="On Trip" <?php echo $status_filter == 'On Trip' ? 'selected' : ''; ?>>On Trip</option>
                                            <option value="maintenance" <?php echo $status_filter == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                            <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Search Buses</label>
                                        <input type="text" name="search" class="form-control" placeholder="Search by plates number or model..." value="<?php echo htmlspecialchars($search_query); ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-2"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Buses Table -->
                        <div class="table-responsive">
                            <?php if ($result->num_rows > 0): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Bus ID</th>
                                            <th>Plates Number</th>
                                            <th>Model</th>
                                            <th>Seats</th>
                                            <th>Active Trips</th>
                                            <th>Available Seats</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($bus = $result->fetch_assoc()): 
                                            // Determine status badge and card class based on actual bus status
                                            $status_class = '';
                                            $status_icon = '';
                                            $bus_card_class = '';
                                            $display_status = $bus['status'];
                                            
                                            switch($bus['status']) {
                                                case 'active': 
                                                    $status_class = 'badge-active';
                                                    $status_icon = 'fa-check-circle';
                                                    $bus_card_class = 'bus-card-active';
                                                    $display_status = 'Available';
                                                    break;
                                                case 'On Trip': 
                                                    $status_class = 'badge-on-trip';
                                                    $status_icon = 'fa-road';
                                                    $bus_card_class = 'bus-card-on-trip';
                                                    $display_status = 'On Trip';
                                                    break;
                                                case 'maintenance': 
                                                    $status_class = 'badge-maintenance';
                                                    $status_icon = 'fa-tools';
                                                    $bus_card_class = 'bus-card-maintenance';
                                                    break;
                                                case 'inactive': 
                                                    $status_class = 'badge-inactive';
                                                    $status_icon = 'fa-times-circle';
                                                    $bus_card_class = 'bus-card-inactive';
                                                    break;
                                                default: 
                                                    $status_class = 'badge-active';
                                                    $status_icon = 'fa-question-circle';
                                                    $bus_card_class = 'bus-card-active';
                                                    $display_status = 'Available';
                                            }
                                        ?>
                                        <tr class="bus-card <?php echo $bus_card_class; ?>">
                                            <td>
                                                <strong class="text-primary">#<?php echo $bus['bus_id']; ?></strong>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($bus['plates_number']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($bus['model']); ?></td>
                                            <td>
                                                <span class="fw-bold"><?php echo $bus['number_of_seats']; ?> seats</span>
                                            </td>
                                            <td>
                                                <?php if ($bus['active_trips'] > 0): ?>
                                                    <span class="trip-indicator">
                                                        <i class="fas fa-route me-1"></i>
                                                        <?php echo $bus['active_trips']; ?> active trip<?php echo $bus['active_trips'] > 1 ? 's' : ''; ?>
                                                    </span>
                                                    <?php if (!empty($bus['active_routes'])): ?>
                                                        <div class="route-info">
                                                            <small><?php echo htmlspecialchars($bus['active_routes']); ?></small>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No active trips</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold"><?php echo $bus['total_available_seats']; ?> available</span>
                                                </div>
                                                <?php if ($bus['number_of_seats'] > 0): ?>
                                                <div class="seats-indicator">
                                                    <?php 
                                                    $seats_percentage = ($bus['total_available_seats'] / $bus['number_of_seats']) * 100;
                                                    $seats_class = $seats_percentage > 50 ? 'seats-low' : ($seats_percentage > 20 ? 'seats-medium' : 'seats-high');
                                                    ?>
                                                    <div class="seats-filled <?php echo $seats_class; ?>" style="width: <?php echo min($seats_percentage, 100); ?>%"></div>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo $status_class; ?>">
                                                    <i class="fas <?php echo $status_icon; ?> me-1"></i>
                                                    <?php echo $display_status; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <?php if ($bus['status'] == 'active' && $bus['active_trips'] == 0): ?>
                                                        <a href="assign_trip.php?bus_id=<?php echo $bus['bus_id']; ?>" 
                                                           class="btn btn-success btn-sm" 
                                                           title="Assign to Trip">
                                                            <i class="fas fa-route me-1"></i> Assign
                                                        </a>
                                                    <?php elseif ($bus['active_trips'] > 0): ?>
                                                        <a href="select_trip.php?bus_id=<?php echo $bus['bus_id']; ?>" 
                                                           class="btn btn-info btn-sm"
                                                           title="View Active Trips">
                                                            <i class="fas fa-eye me-1"></i> Trips
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Quick Status Update -->
                                                    <div class="dropdown">
                                                        <button class="btn btn-warning btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="select_buses.php?update_status=<?php echo $bus['bus_id']; ?>&status=active">Set Available</a></li>
                                                            <li><a class="dropdown-item" href="select_buses.php?update_status=<?php echo $bus['bus_id']; ?>&status=maintenance">Set Maintenance</a></li>
                                                            <li><a class="dropdown-item" href="select_buses.php?update_status=<?php echo $bus['bus_id']; ?>&status=inactive">Set Inactive</a></li>
                                                        </ul>
                                                    </div>
                                                    
                                                    <a href="update_buses.php?bus_id=<?php echo $bus['bus_id']; ?>" 
                                                       class="btn btn-warning btn-sm"
                                                       title="Edit Bus">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <a href="delete_buses.php?bus_id=<?php echo $bus['bus_id']; ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Are you sure you want to delete this bus?')"
                                                       title="Delete Bus">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="no-buses">
                                    <i class="fas fa-bus"></i>
                                    <h4>No Buses Found</h4>
                                    <p>You haven't added any buses yet or no buses match your search criteria.</p>
                                    <a href="add_buses.php" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-2"></i> Add Your First Bus
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
            const actionButtons = document.querySelectorAll('a[href*="delete_bus"]');
            actionButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this bus? This action cannot be undone.')) {
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