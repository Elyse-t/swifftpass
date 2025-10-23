<?php
include 'connection.php';

$success_message = "";
$error_message = "";

// Get bus details from URL parameters
$bus_id = isset($_GET['bus_id']) ? $conn->real_escape_string($_GET['bus_id']) : '';

// Fetch bus data from database
$current_bus_data = [];
if ($bus_id) {
    $bus_query = $conn->query("SELECT * FROM buses WHERE bus_id = '$bus_id'");
    if ($bus_query->num_rows > 0) {
        $current_bus_data = $bus_query->fetch_assoc();
    } else {
        $error_message = "Bus not found!";
    }
}

// Fetch available routes
$routes = $conn->query("SELECT * FROM routes WHERE status = 'active' ORDER BY departure ASC");

// AUTO-ASSIGN DRIVER: Fetch the driver assigned to this bus from recent trips
$assigned_driver = null;
if ($bus_id) {
    $driver_assignment_query = $conn->query("
        SELECT t.driver_id, d.name, d.contact, d.license 
        FROM trips t 
        JOIN drivers d ON t.driver_id = d.driver_id 
        WHERE t.bus_id = '$bus_id' 
        AND t.status IN ('scheduled', 'boarding', 'departed')
        ORDER BY t.departure_datetime DESC 
        LIMIT 1
    ");
    
    if ($driver_assignment_query->num_rows > 0) {
        $assigned_driver = $driver_assignment_query->fetch_assoc();
    } else {
        // If no driver is assigned, get any available active driver
        $available_driver_query = $conn->query("
            SELECT * FROM drivers 
            WHERE status = 'active' 
            ORDER BY driver_id ASC 
            LIMIT 1
        ");
        if ($available_driver_query->num_rows > 0) {
            $assigned_driver = $available_driver_query->fetch_assoc();
        }
    }
}

// Fetch existing trips for this bus
$existing_trips = $conn->query("SELECT * FROM trips WHERE bus_id = '$bus_id' AND status IN ('scheduled', 'boarding', 'departed')");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bus_id = $conn->real_escape_string($_POST['bus_id']);
    $driver_id = $conn->real_escape_string($_POST['driver_id']);
    $route_id = $conn->real_escape_string($_POST['route_id']);
    $departure_date = $conn->real_escape_string($_POST['departure_date']);
    $departure_time = $conn->real_escape_string($_POST['departure_time']);
    
    // Combine date and time
    $departure_datetime = $departure_date . ' ' . $departure_time;
    
    // Validate departure datetime is in the future
    if (strtotime($departure_datetime) <= time()) {
        $error_message = "❌ Error: Departure must be in the future!";
    } else {
        // Get route details including time duration
        $route_query = $conn->query("SELECT departure, destination, time FROM routes WHERE route_id = '$route_id'");
        if ($route_query->num_rows > 0) {
            $route_data = $route_query->fetch_assoc();
            $duration_hours = floatval($route_data['time']);
            
            // Calculate estimated arrival
            $departure_timestamp = strtotime($departure_datetime);
            $estimated_arrival = date('Y-m-d H:i:s', $departure_timestamp + ($duration_hours * 3600));
            
            // Get bus capacity
            $bus_query = $conn->query("SELECT number_of_seats FROM buses WHERE bus_id = '$bus_id'");
            $bus_data = $bus_query->fetch_assoc();
            $available_seats = $bus_data['number_of_seats'];

            // Check if bus is already assigned to another trip at the same time
            $conflict_check = $conn->query("SELECT trip_id FROM trips 
                                           WHERE bus_id = '$bus_id' 
                                           AND departure_datetime = '$departure_datetime' 
                                           AND status IN ('scheduled', 'boarding', 'departed')");
            
            if ($conflict_check->num_rows > 0) {
                $error_message = "❌ Error: This bus is already assigned to another trip at the same time!";
            } else {
                // Check if driver is available
                $driver_check = $conn->query("SELECT trip_id FROM trips 
                                             WHERE driver_id = '$driver_id' 
                                             AND departure_datetime = '$departure_datetime' 
                                             AND status IN ('scheduled', 'boarding', 'departed')");
                
                if ($driver_check->num_rows > 0) {
                    $error_message = "❌ Error: This driver is already assigned to another trip at the same time!";
                } else {
                    // Insert new trip assignment
                    $stmt = $conn->prepare("INSERT INTO trips 
                        (bus_id, driver_id, route_id, departure_datetime, estimated_arrival, available_seats, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, 'scheduled', NOW())");
                    
                    $stmt->bind_param("iiissi", $bus_id, $driver_id, $route_id, $departure_datetime, $estimated_arrival, $available_seats);

                    if ($stmt->execute()) {
                        $trip_id = $stmt->insert_id;
                        $success_message = "✅ Bus successfully assigned to new trip! Trip ID: $trip_id";
                        
                        // Reset form fields
                        $_POST = array();
                    } else {
                        $error_message = "❌ Error creating trip assignment: " . $stmt->error;
                    }

                    $stmt->close();
                }
            }
        } else {
            $error_message = "❌ Error: Route not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Bus to Trip - SwiftPass</title>
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
            padding: 20px 0;
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
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.4);
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
        
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-select:focus {
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
        
        .auto-fill-field {
            background-color: #f8f9fa;
        }
        
        .duration-display {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            margin-top: 1rem;
        }
        
        .assignment-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .bus-selection-card {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 2px solid #bbdefb;
        }
        
        .current-assignment {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 2px solid #ffeaa7;
        }
        
        .trip-preview {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
            display: none;
        }
        
        .conflict-warning {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 2px solid #f5c6cb;
            display: none;
        }
        
        .route-details {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            display: none;
        }
        
        .driver-assignment-info {
            background: linear-gradient(135deg, #e8f4fd, #d1ebff);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 2px solid #d1ebff;
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
        
        .route-path {
            font-weight: 600;
            color: var(--primary);
        }
        
        .price-badge {
            background: linear-gradient(135deg, var(--info), #138496);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .readonly-field {
            background-color: #f8f9fa !important;
            color: #6c757d !important;
            border: 2px solid #e9ecef !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-route me-2"></i>Assign Bus to Trip</h3>
                        <p class="mb-0 opacity-75">Schedule a new trip for this bus</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo $success_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php echo $error_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Bus Information -->
                        <?php if (!empty($current_bus_data)): ?>
                        <div class="bus-selection-card">
                            <h6><i class="fas fa-bus text-primary me-2"></i>Selected Bus</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Plates:</strong> <?php echo htmlspecialchars($current_bus_data['plates_number']); ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Model:</strong> <?php echo htmlspecialchars($current_bus_data['model']); ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Seats:</strong> <?php echo $current_bus_data['number_of_seats']; ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-<?php echo $current_bus_data['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($current_bus_data['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Driver Assignment Information -->
                        <?php if ($assigned_driver): ?>
                        <div class="driver-assignment-info">
                            <h6><i class="fas fa-user-check text-success me-2"></i>Automatically Assigned Driver</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Name:</strong> <?php echo htmlspecialchars($assigned_driver['name']); ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>License:</strong> <?php echo htmlspecialchars($assigned_driver['license']); ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Contact:</strong> <?php echo htmlspecialchars($assigned_driver['contact']); ?>
                                </div>
                            </div>
                            <div class="mt-2 small text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Driver is automatically assigned based on bus assignment history
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Existing Trips -->
                        <?php if ($existing_trips->num_rows > 0): ?>
                        <div class="current-assignment">
                            <h6><i class="fas fa-info-circle text-warning me-2"></i>Existing Active Trips</h6>
                            <?php while($trip = $existing_trips->fetch_assoc()): 
                                $route_query = $conn->query("SELECT * FROM routes WHERE route_id = '{$trip['route_id']}'");
                                $route = $route_query->fetch_assoc();
                                $driver_query = $conn->query("SELECT * FROM drivers WHERE driver_id = '{$trip['driver_id']}'");
                                $driver = $driver_query->fetch_assoc();
                            ?>
                            <div class="row small mb-2">
                                <div class="col-md-3">
                                    <div class="route-path">
                                        <?php echo htmlspecialchars($route['departure']); ?> 
                                        <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                        <?php echo htmlspecialchars($route['destination']); ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <strong>Departure:</strong> <?php echo date('M j, H:i', strtotime($trip['departure_datetime'])); ?>
                                </div>
                                <div class="col-md-2">
                                    <strong>Arrival:</strong> <?php echo date('M j, H:i', strtotime($trip['estimated_arrival'])); ?>
                                </div>
                                <div class="col-md-2">
                                    <strong>Driver:</strong> <?php echo htmlspecialchars($driver['name']); ?>
                                </div>
                                <div class="col-md-2">
                                    <strong>Seats:</strong> <?php echo $trip['available_seats']; ?>/<?php echo $current_bus_data['number_of_seats']; ?>
                                </div>
                                <div class="col-md-1">
                                    <?php 
                                    $status_class = '';
                                    switch($trip['status']) {
                                        case 'scheduled': $status_class = 'badge-scheduled'; break;
                                        case 'boarding': $status_class = 'badge-boarding'; break;
                                        case 'departed': $status_class = 'badge-departed'; break;
                                        default: $status_class = 'badge-scheduled';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo ucfirst($trip['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Conflict Warning -->
                        <div class="conflict-warning" id="conflict_warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="conflict_message">This bus is already scheduled for another trip at the same time.</span>
                        </div>

                        <form action="" method="POST" id="assignmentForm">
                            <input type="hidden" name="bus_id" value="<?php echo $current_bus_data['bus_id']; ?>">
                            <input type="hidden" name="driver_id" value="<?php echo $assigned_driver ? $assigned_driver['driver_id'] : ''; ?>">
                            
                            <div class="row g-3">
                                <!-- Bus Information (Read Only) -->
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Bus Plate Number</label>
                                    <input type="text" class="form-control readonly-field" 
                                           value="<?php echo htmlspecialchars($current_bus_data['plates_number']); ?>" readonly>
                                </div>

                                <!-- Driver Information (Read Only) -->
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Assigned Driver</label>
                                    <input type="text" class="form-control readonly-field" 
                                           value="<?php echo $assigned_driver ? htmlspecialchars($assigned_driver['name']) : 'No driver available'; ?>" readonly>
                                </div>

                                <!-- Available Seats (Read Only) -->
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Available Seats</label>
                                    <input type="text" class="form-control readonly-field" 
                                           value="<?php echo $current_bus_data['number_of_seats']; ?>" readonly>
                                </div>

                                <!-- Route Selection (Admin can select) -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Route *</label>
                                    <select name="route_id" class="form-select" required id="route_select">
                                        <option value="">-- Select Route --</option>
                                        <?php 
                                        $routes->data_seek(0);
                                        while($route = $routes->fetch_assoc()): 
                                            $selected = (isset($_POST['route_id']) && $_POST['route_id'] == $route['route_id']) ? 'selected' : '';
                                            $time_value = floatval($route['time']);
                                        ?>
                                            <option value="<?= $route['route_id'] ?>" 
                                                    data-time="<?= $time_value ?>"
                                                    data-price="<?= $route['price_per_seat'] ?>"
                                                    data-departure="<?= htmlspecialchars($route['departure']) ?>"
                                                    data-destination="<?= htmlspecialchars($route['destination']) ?>"
                                                    <?= $selected ?>>
                                                <?= $route['departure'] ?> to <?= $route['destination'] ?> 
                                                (<?= $route['time'] ?> hours) - ₵<?= $route['price_per_seat'] ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Route Details -->
                                <div class="col-12">
                                    <div class="route-details" id="route_details">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Departure:</strong> <span id="route_departure">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Destination:</strong> <span id="route_destination">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Duration:</strong> <span id="route_duration">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Price:</strong> <span class="price-badge" id="route_price">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Departure Date and Time (Admin can select) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Departure Date *</label>
                                    <input type="date" name="departure_date" class="form-control" 
                                           id="departure_date" required 
                                           min="<?php echo date('Y-m-d'); ?>"
                                           value="<?php echo isset($_POST['departure_date']) ? $_POST['departure_date'] : date('Y-m-d', strtotime('+1 day')); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Departure Time *</label>
                                    <input type="time" name="departure_time" class="form-control" 
                                           id="departure_time" required
                                           value="<?php echo isset($_POST['departure_time']) ? $_POST['departure_time'] : '08:00'; ?>">
                                </div>

                                <!-- Auto-calculated Arrival Time (Read Only)
                                <div class="col-12">
                                    <label class="form-label fw-bold">Estimated Arrival Time</label>
                                    <input type="text" class="form-control readonly-field" 
                                           id="estimated_arrival" readonly
                                           placeholder="Arrival time will be calculated automatically">
                                </div> -->

                                <!-- Trip Preview -->
                                <div class="col-12">
                                    <div class="trip-preview" id="trip_preview">
                                        <h6><i class="fas fa-eye me-2"></i>Trip Preview</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Bus:</strong> <span id="preview_bus"><?php echo htmlspecialchars($current_bus_data['plates_number']); ?></span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Driver:</strong> <span id="preview_driver"><?php echo $assigned_driver ? htmlspecialchars($assigned_driver['name']) : 'No driver'; ?></span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Route:</strong> <span id="preview_route">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Seats:</strong> <span id="preview_seats"><?php echo $current_bus_data['number_of_seats']; ?></span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Departure:</strong> <span id="preview_departure">-</span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Arrival:</strong> <span id="preview_arrival">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="select_buses.php" class="back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Buses
                                </a>
                                <button type="submit" class="btn btn-primary" id="submit_btn">
                                    <i class="fas fa-check-circle me-2"></i> Create Trip Assignment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const routeSelect = document.getElementById('route_select');
            const departureDate = document.getElementById('departure_date');
            const departureTime = document.getElementById('departure_time');
            const estimatedArrival = document.getElementById('estimated_arrival');
            const tripPreview = document.getElementById('trip_preview');
            const routeDetails = document.getElementById('route_details');
            const conflictWarning = document.getElementById('conflict_warning');
            const submitBtn = document.getElementById('submit_btn');

            let currentRouteDuration = 0;
            let currentRoutePrice = 0;

            // Route selection handler
            routeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    currentRouteDuration = parseFloat(selectedOption.getAttribute('data-time'));
                    currentRoutePrice = parseFloat(selectedOption.getAttribute('data-price'));
                    
                    // Update route details
                    document.getElementById('route_departure').textContent = selectedOption.getAttribute('data-departure');
                    document.getElementById('route_destination').textContent = selectedOption.getAttribute('data-destination');
                    
                    const hours = Math.floor(currentRouteDuration);
                    const minutes = Math.round((currentRouteDuration - hours) * 60);
                    const durationText = hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`;
                    document.getElementById('route_duration').textContent = durationText;
                    
                    document.getElementById('route_price').textContent = '₵' + currentRoutePrice.toFixed(2);
                    
                    routeDetails.style.display = 'block';
                    calculateArrivalTime();
                    updateTripPreview();
                } else {
                    routeDetails.style.display = 'none';
                }
            });

            // Date and time change handlers
            departureDate.addEventListener('change', function() {
                calculateArrivalTime();
                updateTripPreview();
            });

            departureTime.addEventListener('change', function() {
                calculateArrivalTime();
                updateTripPreview();
            });

            // Function to calculate arrival time
            function calculateArrivalTime() {
                if (departureDate.value && departureTime.value && currentRouteDuration > 0) {
                    const departureDateTime = new Date(departureDate.value + 'T' + departureTime.value);
                    const arrivalDateTime = new Date(departureDateTime.getTime() + (currentRouteDuration * 3600000));
                    
                    // Format for display
                    const options = { 
                        weekday: 'short', 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric',
                        hour: '2-digit', 
                        minute: '2-digit'
                    };
                    
                    estimatedArrival.value = arrivalDateTime.toLocaleDateString('en-US', options);
                    
                    // Update preview
                    document.getElementById('preview_arrival').textContent = arrivalDateTime.toLocaleString();
                }
            }

            // Function to update trip preview
            function updateTripPreview() {
                const hasRoute = routeSelect.value;
                const hasDate = departureDate.value;
                const hasTime = departureTime.value;

                if (hasRoute && hasDate && hasTime) {
                    document.getElementById('preview_route').textContent = routeSelect.options[routeSelect.selectedIndex].text;
                    
                    const departureDateTime = new Date(departureDate.value + 'T' + departureTime.value);
                    document.getElementById('preview_departure').textContent = departureDateTime.toLocaleString();
                    
                    tripPreview.style.display = 'block';
                    checkForConflicts();
                } else {
                    tripPreview.style.display = 'none';
                    conflictWarning.style.display = 'none';
                    submitBtn.disabled = false;
                }
            }

            // Function to check for scheduling conflicts
            function checkForConflicts() {
                // This would typically involve an AJAX call to check the database
                // For now, we'll just enable the submit button
                submitBtn.disabled = false;
                conflictWarning.style.display = 'none';
            }

            // Initialize on page load
            if (routeSelect.value) {
                routeSelect.dispatchEvent(new Event('change'));
            }
            updateTripPreview();

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