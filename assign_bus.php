<?php
// Connect to database
$servername = "localhost";
$username = "root";
$password = ""; // your DB password
$dbname = "swifftpass";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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

// Fetch available drivers
$drivers = $conn->query("SELECT * FROM drivers WHERE status = 'active' ORDER BY name ASC");

// Fetch available routes
$routes = $conn->query("SELECT * FROM routes WHERE status = 'active' ORDER BY departure ASC");

// Fetch existing trips for this bus
$existing_trips = $conn->query("SELECT * FROM trips WHERE bus_id = '$bus_id' AND status = 'scheduled'");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bus_id = $conn->real_escape_string($_POST['bus_id']);
    $driver_id = $conn->real_escape_string($_POST['driver_id']);
    $route_id = $conn->real_escape_string($_POST['route_id']);
    $departure_datetime = $conn->real_escape_string($_POST['departure_datetime']);
    
    // Calculate estimated arrival based on route duration
    $route_query = $conn->query("SELECT duration_minutes FROM routes WHERE route_id = '$route_id'");
    $route_data = $route_query->fetch_assoc();
    $duration_minutes = $route_data['duration_minutes'];
    
    // Calculate estimated arrival
    $departure_timestamp = strtotime($departure_datetime);
    $estimated_arrival = date('Y-m-d H:i:s', $departure_timestamp + ($duration_minutes * 60));
    
    // Get bus capacity
    $bus_query = $conn->query("SELECT number_of_seats FROM buses WHERE bus_id = '$bus_id'");
    $bus_data = $bus_query->fetch_assoc();
    $available_seats = $bus_data['number_of_seats'];

    // Check if bus is already assigned to another trip at the same time
    $conflict_check = $conn->query("SELECT trip_id FROM trips 
                                   WHERE bus_id = '$bus_id' 
                                   AND departure_datetime = '$departure_datetime' 
                                   AND status = 'scheduled'");
    
    if ($conflict_check->num_rows > 0) {
        $error_message = "❌ Error: This bus is already assigned to another trip at the same time!";
    } else {
        // Insert new trip assignment
        $stmt = $conn->prepare("INSERT INTO trips 
            (bus_id, driver_id, route_id, departure_datetime, estimated_arrival, available_seats, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 'scheduled', NOW())");
        
        $stmt->bind_param("iiissi", $bus_id, $driver_id, $route_id, $departure_datetime, $estimated_arrival, $available_seats);

        if ($stmt->execute()) {
            $trip_id = $stmt->insert_id;
            $success_message = "✅ Bus successfully assigned to new trip! Trip ID: $trip_id";
        } else {
            $error_message = "❌ Error creating trip assignment: " . $stmt->error;
        }

        $stmt->close();
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
        
        .quick-action-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .assignment-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .info-item {
            display: flex;
            justify-content: between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--primary);
            min-width: 120px;
        }
        
        .info-value {
            color: #2c3e50;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
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
                                <div class="col-md-4">
                                    <strong>Plates:</strong> <?php echo htmlspecialchars($current_bus_data['plates_number']); ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Model:</strong> <?php echo htmlspecialchars($current_bus_data['model']); ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Seats:</strong> <?php echo $current_bus_data['number_of_seats']; ?>
                                </div>
                            </div>
                            <input type="hidden" name="bus_id" value="<?php echo $current_bus_data['bus_id']; ?>">
                        </div>
                        <?php endif; ?>

                        <!-- Existing Trips -->
                        <?php if ($existing_trips->num_rows > 0): ?>
                        <div class="current-assignment">
                            <h6><i class="fas fa-info-circle text-warning me-2"></i>Existing Scheduled Trips</h6>
                            <?php while($trip = $existing_trips->fetch_assoc()): 
                                $route_query = $conn->query("SELECT * FROM routes WHERE route_id = '{$trip['route_id']}'");
                                $route = $route_query->fetch_assoc();
                                $driver_query = $conn->query("SELECT * FROM drivers WHERE driver_id = '{$trip['driver_id']}'");
                                $driver = $driver_query->fetch_assoc();
                            ?>
                            <div class="row small mb-2">
                                <div class="col-md-4">
                                    <strong>Route:</strong> <?php echo htmlspecialchars($route['departure'] . ' to ' . $route['destination']); ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Departure:</strong> <?php echo date('M j, Y H:i', strtotime($trip['departure_datetime'])); ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Driver:</strong> <?php echo htmlspecialchars($driver['name']); ?>
                                </div>
                                <div class="col-md-2">
                                    <strong>Seats:</strong> <?php echo $trip['available_seats']; ?>/<?php echo $current_bus_data['number_of_seats']; ?>
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
                            
                            <div class="row g-3">
                                <!-- Driver Selection -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Driver *</label>
                                    <select name="driver_id" class="form-select" required id="driver_select">
                                        <option value="">-- Select Driver --</option>
                                        <?php while($driver = $drivers->fetch_assoc()): ?>
                                            <option value="<?= $driver['driver_id'] ?>">
                                                <?= htmlspecialchars($driver['name']) ?> - <?= $driver['license'] ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Route Selection -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Route *</label>
                                    <select name="route_id" class="form-select" required id="route_select">
                                        <option value="">-- Select Route --</option>
                                        <?php 
                                        $routes->data_seek(0);
                                        while($route = $routes->fetch_assoc()): 
                                        ?>
                                            <option value="<?= $route['route_id'] ?>" 
                                                    data-duration="<?= $route['duration_minutes'] ?>"
                                                    data-price="<?= $route['price_per_seat'] ?>">
                                                <?= htmlspecialchars($route['departure']) ?> to <?= htmlspecialchars($route['destination']) ?> 
                                                (<?= floor($route['duration_minutes'] / 60) ?>h <?= $route['duration_minutes'] % 60 ?>m)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Departure Date and Time -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Departure Date *</label>
                                    <input type="date" name="departure_date" class="form-control" 
                                           id="departure_date" required 
                                           min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Departure Time *</label>
                                    <input type="time" name="departure_time" class="form-control" 
                                           id="departure_time" required>
                                </div>

                                <!-- Auto-calculated Information -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Estimated Arrival</label>
                                    <input type="text" class="form-control auto-fill-field" 
                                           id="estimated_arrival" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Available Seats</label>
                                    <input type="number" class="form-control auto-fill-field" 
                                           value="<?php echo $current_bus_data['number_of_seats']; ?>" readonly>
                                </div>

                                <!-- Trip Preview -->
                                <div class="col-12">
                                    <div class="trip-preview" id="trip_preview">
                                        <h6><i class="fas fa-eye me-2"></i>Trip Preview</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Bus:</strong> <span id="preview_bus"><?php echo htmlspecialchars($current_bus_data['plates_number']); ?></span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Driver:</strong> <span id="preview_driver">-</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Route:</strong> <span id="preview_route">-</span>
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
            const driverSelect = document.getElementById('driver_select');
            const departureDate = document.getElementById('departure_date');
            const departureTime = document.getElementById('departure_time');
            const estimatedArrival = document.getElementById('estimated_arrival');
            const tripPreview = document.getElementById('trip_preview');
            const conflictWarning = document.getElementById('conflict_warning');
            const submitBtn = document.getElementById('submit_btn');

            let currentRouteDuration = 0;

            // Set default departure date to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            departureDate.valueAsDate = tomorrow;

            // Set default departure time to 08:00
            departureTime.value = '08:00';

            // Route selection handler
            routeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    currentRouteDuration = parseInt(selectedOption.getAttribute('data-duration'));
                    calculateArrivalTime();
                    updateTripPreview();
                }
            });

            // Driver selection handler
            driverSelect.addEventListener('change', updateTripPreview);

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
                    const arrivalDateTime = new Date(departureDateTime.getTime() + (currentRouteDuration * 60000));
                    
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
                }
            }

            // Function to update trip preview
            function updateTripPreview() {
                const hasRoute = routeSelect.value;
                const hasDriver = driverSelect.value;
                const hasDate = departureDate.value;
                const hasTime = departureTime.value;

                if (hasRoute && hasDriver && hasDate && hasTime) {
                    document.getElementById('preview_driver').textContent = driverSelect.options[driverSelect.selectedIndex].text.split(' - ')[0];
                    document.getElementById('preview_route').textContent = routeSelect.options[routeSelect.selectedIndex].text;
                    
                    const departureDateTime = new Date(departureDate.value + 'T' + departureTime.value);
                    document.getElementById('preview_departure').textContent = departureDateTime.toLocaleString();
                    
                    if (currentRouteDuration > 0) {
                        const arrivalDateTime = new Date(departureDateTime.getTime() + (currentRouteDuration * 60000));
                        document.getElementById('preview_arrival').textContent = arrivalDateTime.toLocaleString();
                    }
                    
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
            calculateArrivalTime();
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