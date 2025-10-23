<?php
include 'connection.php';

// Fetch data for dropdowns

$buses = $conn->query("SELECT bus_id, plates_number, model, number_of_seats FROM buses WHERE status = 'active' ORDER BY plates_number ASC");
$drivers = $conn->query("SELECT driver_id, name, license FROM drivers WHERE status = 'active' ORDER BY name ASC");
$routes = $conn->query("SELECT route_id, departure, destination, delay_time, price_per_seat FROM routes WHERE status = 'active' ORDER BY departure ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather and sanitize inputs
    $bus_id = $conn->real_escape_string($_POST['bus_id']);
    $driver_id = $conn->real_escape_string($_POST['driver_id']);
    $route_id = $conn->real_escape_string($_POST['route_id']);
    $departure_raw = isset($_POST['departure_datetime']) ? $_POST['departure_datetime'] : '';
    $status = 'available';

    // Convert departure to timestamp and MySQL datetime
    $departure_ts = strtotime($departure_raw);
    if ($departure_ts === false) {
        $error_message = "❌ Error: Invalid departure datetime.";
    } else {
        $departure_datetime = date('Y-m-d H:i:s', $departure_ts);

        // Get bus capacity for available seats calculation
        $bus_capacity_sql = "SELECT number_of_seats FROM buses WHERE bus_id = '$bus_id'";
        $bus_capacity_result = $conn->query($bus_capacity_sql);
        $bus_capacity = $bus_capacity_result->fetch_assoc()['number_of_seats'];
        $available_seats = intval($bus_capacity);

        // Get route delay_time (hours)
        $route_row = $conn->query("SELECT delay_time FROM routes WHERE route_id = '$route_id' LIMIT 1")->fetch_assoc();
        $delay_hours = 0;
        if ($route_row && isset($route_row['delay_time'])) {
            $delay_hours = intval($route_row['delay_time']);
        }

        // Compute estimated arrival timestamp and formatted datetime
        $arrival_timestamp = $departure_ts + ($delay_hours * 3600);
        $estimated_arrival = date('Y-m-d H:i:s', $arrival_timestamp);

        if ($arrival_timestamp <= $departure_ts) {
            $error_message = "❌ Error: Computed estimated arrival must be after departure time!";
        } else {
            // Check if bus is already scheduled for the same departure time
            $check_sql = "SELECT trip_id FROM trips WHERE bus_id = '$bus_id' AND departure_datetime = '$departure_datetime'";
            $check_result = $conn->query($check_sql);

            if ($check_result->num_rows > 0) {
                $error_message = "❌ Error: This bus is already scheduled for the selected departure time!";
            } else {
                // Insert the new trip using computed estimated_arrival
                $trip_sql = "INSERT INTO trips (bus_id, driver_id, route_id, departure_datetime, estimated_arrival, available_seats, status) 
                             VALUES ('$bus_id', '$driver_id', '$route_id', '$departure_datetime', '$estimated_arrival', '$available_seats', '$status')";

                if ($conn->query($trip_sql) === TRUE) {
                    $success_message = "✅ New trip scheduled successfully!";
                    // Clear form fields
                    $_POST = array();
                } else {
                    $error_message = "❌ Error: " . $conn->error;
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule New Trip - SwiftPass</title>
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
            --dark: #191e32;
            --light: #ecf0f1;
        }
        
        body {
            background: linear-gradient(135deg, #191e32ff 0%, #1a151fff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
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
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
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
        
        .input-group-text {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 10px 0 0 10px;
        }
        
        .trip-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--info), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }
        
        .required-field::after {
            content: " *";
            color: var(--danger);
        }
        
        .route-preview {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 0.5rem;
            display: none;
        }
        
        .bus-preview {
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 0.5rem;
            display: none;
        }
        
        .driver-preview {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 0.5rem;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Schedule New Trip</h3>
                        <p class="mb-0 opacity-75">Create a new bus trip for SwiftPass system</p>
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

                        <form method="POST" action="" id="tripForm">
                            <div class="row g-3">
                                <!-- Trip Icon -->
                                <div class="col-12 text-center">
                                    <div class="trip-icon">
                                        <i class="fas fa-bus"></i>
                                    </div>
                                </div>

                                <!-- Bus Selection -->
                                <div class="col-md-6">
                                    <label for="bus_id" class="form-label fw-bold required-field">Select Bus *</label>
                                    <select class="form-select" id="bus_id" name="bus_id" required>
                                        <option value="">-- Select Bus --</option>
                                        <?php while($bus = $buses->fetch_assoc()): ?>
                                            <option value="<?php echo $bus['bus_id']; ?>" 
                                                    data-seats="<?php echo $bus['number_of_seats']; ?>"
                                                    data-model="<?php echo htmlspecialchars($bus['model']); ?>">
                                                <?php echo htmlspecialchars($bus['plates_number']); ?> - <?php echo htmlspecialchars($bus['model']); ?> (<?php echo $bus['number_of_seats']; ?> seats)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="bus-preview" id="busPreview">
                                        <small class="text-muted" id="busInfo"></small>
                                    </div>
                                </div>
                                
                                <!-- Driver Selection -->
                                <div class="col-md-6">
                                    <label for="driver_id" class="form-label fw-bold required-field">Select Driver *</label>
                                    <select class="form-select" id="driver_id" name="driver_id" required>
                                        <option value="">-- Select Driver --</option>
                                        <?php while($driver = $drivers->fetch_assoc()): ?>
                                            <option value="<?php echo $driver['driver_id']; ?>"
                                                    data-license="<?php echo htmlspecialchars($driver['license']); ?>">
                                                <?php echo htmlspecialchars($driver['name']); ?> - <?php echo htmlspecialchars($driver['license']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="driver-preview" id="driverPreview">
                                        <small class="text-muted" id="driverInfo"></small>
                                    </div>
                                </div>
                                
                                <!-- Route Selection -->
                                <div class="col-12">
                                    <label for="route_id" class="form-label fw-bold required-field">Select Route *</label>
                                    <select class="form-select" id="route_id" name="route_id" required>
                                        <option value="">-- Select Route --</option>
                                        <?php while($route = $routes->fetch_assoc()): ?>
                                            <option value="<?php echo $route['route_id']; ?>"
                                                    data-departure="<?php echo htmlspecialchars($route['departure']); ?>"
                                                    data-destination="<?php echo htmlspecialchars($route['destination']); ?>"
                                                    data-price="<?php echo $route['price_per_seat']; ?>"
                                                    data-delay="<?php echo $route['delay_time']; ?>">
                                                <?php echo htmlspecialchars($route['departure']); ?> → <?php echo htmlspecialchars($route['destination']); ?> 
                                                (<?php echo number_format($route['price_per_seat']); ?> FRW)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="route-preview" id="routePreview">
                                        <small class="text-muted" id="routeInfo"></small>
                                    </div>
                                </div>
                                
                                <!-- Departure DateTime -->
                                <div class="col-md-6">
                                    <label for="departure_datetime" class="form-label fw-bold required-field">Departure Date & Time *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="datetime-local" class="form-control" id="departure_datetime" name="departure_datetime" required 
                                               value="<?php echo isset($_POST['departure_datetime']) ? htmlspecialchars($_POST['departure_datetime']) : ''; ?>">
                                    </div>
                                    <div class="form-text">Scheduled departure date and time</div>
                                </div>
                                
                                <!-- Estimated Arrival (Auto-calculated) -->
                                <div class="col-md-6">
                                    <label for="estimated_arrival" class="form-label fw-bold required-field">Estimated Arrival *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-flag-checkered"></i></span>
                                        <input type="datetime-local" class="form-control" id="estimated_arrival" name="estimated_arrival" required 
                                               value="<?php echo isset($_POST['estimated_arrival']) ? htmlspecialchars($_POST['estimated_arrival']) : ''; ?>"
                                               readonly>
                                    </div>
                                    <div class="form-text">Auto-calculated based on route travel time</div>
                                </div>
                                
                                <!-- Status (Hidden - Auto set to available) -->
                                <input type="hidden" id="status" name="status" value="available">
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="admin.php?section=manage-trips" class="back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back 
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="fas fa-undo me-2"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-calendar-plus me-2"></i> Schedule Trip
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tripForm = document.getElementById('tripForm');
            const busSelect = document.getElementById('bus_id');
            const driverSelect = document.getElementById('driver_id');
            const routeSelect = document.getElementById('route_id');
            const departureInput = document.getElementById('departure_datetime');
            const arrivalInput = document.getElementById('estimated_arrival');

            // Set default datetime to current time + 1 hour
            const now = new Date();
            now.setHours(now.getHours() + 1);
            now.setMinutes(0);
            now.setSeconds(0);
            const defaultDatetime = now.toISOString().slice(0, 16);
            departureInput.value = defaultDatetime;

            // Calculate estimated arrival when route or departure time changes
            function calculateEstimatedArrival() {
                const selectedRoute = routeSelect.options[routeSelect.selectedIndex];
                const departureValue = departureInput.value;
                
                if (selectedRoute.value && departureValue) {
                    const delayHours = parseInt(selectedRoute.getAttribute('data-delay')) || 0;
                    
                    // Calculate arrival time by adding delay hours to departure time
                    const departure = new Date(departureValue);
                    departure.setHours(departure.getHours() + delayHours);

                    // Format as local 'YYYY-MM-DDTHH:MM' for datetime-local input without timezone conversion
                    const pad = (n) => n.toString().padStart(2, '0');
                    const localDate = departure.getFullYear() + '-' + pad(departure.getMonth() + 1) + '-' + pad(departure.getDate());
                    const localTime = pad(departure.getHours()) + ':' + pad(departure.getMinutes());
                    arrivalInput.value = localDate + 'T' + localTime;
                }
            }

            // Event listeners for auto-calculation
            routeSelect.addEventListener('change', function() {
                calculateEstimatedArrival();
                showRoutePreview();
            });

            departureInput.addEventListener('change', function() {
                calculateEstimatedArrival();
            });

            // Show route preview
            function showRoutePreview() {
                const selectedOption = routeSelect.options[routeSelect.selectedIndex];
                if (selectedOption.value) {
                    const routePreview = document.getElementById('routePreview');
                    const routeInfo = document.getElementById('routeInfo');
                    const delayHours = selectedOption.getAttribute('data-delay') || '0';
                    
                    routeInfo.innerHTML = `<strong>Route:</strong> ${selectedOption.getAttribute('data-departure')} → ${selectedOption.getAttribute('data-destination')} | <strong>Price:</strong> ${selectedOption.getAttribute('data-price')} FRW | <strong>Travel Time:</strong> ${delayHours} hours`;
                    routePreview.style.display = 'block';
                } else {
                    document.getElementById('routePreview').style.display = 'none';
                }
            }

            // Show bus preview
            busSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const busPreview = document.getElementById('busPreview');
                    const busInfo = document.getElementById('busInfo');
                    busInfo.innerHTML = `<strong>Model:</strong> ${selectedOption.getAttribute('data-model')} | <strong>Capacity:</strong> ${selectedOption.getAttribute('data-seats')} seats`;
                    busPreview.style.display = 'block';
                } else {
                    document.getElementById('busPreview').style.display = 'none';
                }
            });

            // Show driver preview
            driverSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const driverPreview = document.getElementById('driverPreview');
                    const driverInfo = document.getElementById('driverInfo');
                    driverInfo.innerHTML = `<strong>License:</strong> ${selectedOption.getAttribute('data-license')}`;
                    driverPreview.style.display = 'block';
                } else {
                    document.getElementById('driverPreview').style.display = 'none';
                }
            });

            // Form validation
            tripForm.addEventListener('submit', function(e) {
                const busId = busSelect.value;
                const driverId = driverSelect.value;
                const routeId = routeSelect.value;
                const departure = departureInput.value;
                const arrival = arrivalInput.value;

                if (!busId || !driverId || !routeId || !departure || !arrival) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }

                // Validate arrival is after departure
                const departureTime = new Date(departure);
                const arrivalTime = new Date(arrival);
                
                if (arrivalTime <= departureTime) {
                    e.preventDefault();
                    alert('Estimated arrival must be after departure time.');
                    arrivalInput.focus();
                    return false;
                }

                // Validate departure is not in the past
                const currentTime = new Date();
                if (departureTime < currentTime) {
                    e.preventDefault();
                    alert('Departure time cannot be in the past.');
                    departureInput.focus();
                    return false;
                }
            });

            // Add confirmation for reset
            document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to clear all form fields?')) {
                    e.preventDefault();
                }
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Initial calculation
            calculateEstimatedArrival();
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>