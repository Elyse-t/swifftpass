<?php
include 'connection.php';

// Debug: Show what parameters we're receiving
echo "<!-- DEBUG: GET parameters received: " . print_r($_GET, true) . " -->";
echo "<!-- DEBUG: Looking for trip with ID: " . $_GET['id'] . " -->";

// Check if trip_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin.php?section=manage-trips");
    exit();
}

$trip_id = $conn->real_escape_string($_GET['id']);

// Fetch trip data for editing
$sql = "SELECT t.*, b.plates_number, b.model, d.name as driver_name, 
               r.departure as route_departure, r.destination as route_destination, 
               r.delay_time, r.price_per_seat as route_price
        FROM trips t
        JOIN buses b ON t.bus_id = b.bus_id
        JOIN drivers d ON t.driver_id = d.driver_id
        JOIN routes r ON t.route_id = r.route_id
        WHERE t.trip_id = '$trip_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: admin.php?section=manage-trips");
    exit();
}

$trip = $result->fetch_assoc();

// Fetch data for dropdowns
$buses = $conn->query("SELECT bus_id, plates_number, model FROM buses WHERE status = 'active' ORDER BY plates_number ASC");
$drivers = $conn->query("SELECT driver_id, name, license FROM drivers WHERE status = 'active' ORDER BY name ASC");
$routes = $conn->query("SELECT route_id, departure, destination, delay_time, price_per_seat FROM routes WHERE status = 'active' ORDER BY departure ASC");

// Handle form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather and sanitize inputs
    $bus_id = $conn->real_escape_string($_POST['bus_id']);
    $driver_id = $conn->real_escape_string($_POST['driver_id']);
    $route_id = $conn->real_escape_string($_POST['route_id']);
    $departure_raw = isset($_POST['departure_datetime']) ? $_POST['departure_datetime'] : '';
    // Ignore any client-submitted estimated_arrival; compute server-side
    $available_seats = intval($conn->real_escape_string($_POST['available_seats']));
    $status = $conn->real_escape_string($_POST['status']);

    // Parse departure and convert to MySQL datetime
    $departure_ts = strtotime($departure_raw);
    if ($departure_ts === false) {
        $error_message = "❌ Error: Invalid departure datetime.";
    } else {
        $departure_datetime = date('Y-m-d H:i:s', $departure_ts);

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
            $update_sql = "UPDATE trips SET 
                          bus_id = '$bus_id',
                          driver_id = '$driver_id',
                          route_id = '$route_id',
                          departure_datetime = '$departure_datetime',
                          estimated_arrival = '$estimated_arrival',
                          available_seats = '$available_seats',
                          status = '$status'
                          WHERE trip_id = '$trip_id'";

            if ($conn->query($update_sql) === TRUE) {
                $success_message = "✅ Trip updated successfully!";
                // Refresh the trip data
                $result = $conn->query($sql);
                $trip = $result->fetch_assoc();
            } else {
                $error_message = "❌ Error updating trip: " . $conn->error;
            }
        }
    }
}

// end POST handling
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Trip - SwiftPass</title>
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
                        <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Update Trip</h3>
                        <p class="mb-0 opacity-75">Update trip information for SwiftPass</p>
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
                                                    <?php echo ($bus['bus_id'] == $trip['bus_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($bus['plates_number']); ?> - <?php echo htmlspecialchars($bus['model']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <!-- Driver Selection -->
                                <div class="col-md-6">
                                    <label for="driver_id" class="form-label fw-bold required-field">Select Driver *</label>
                                    <select class="form-select" id="driver_id" name="driver_id" required>
                                        <option value="">-- Select Driver --</option>
                                        <?php while($driver = $drivers->fetch_assoc()): ?>
                                            <option value="<?php echo $driver['driver_id']; ?>"
                                                    <?php echo ($driver['driver_id'] == $trip['driver_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($driver['name']); ?> - <?php echo htmlspecialchars($driver['license']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <!-- Route Selection -->
                                <div class="col-12">
                                    <label for="route_id" class="form-label fw-bold required-field">Select Route *</label>
                                    <select class="form-select" id="route_id" name="route_id" required>
                                        <option value="">-- Select Route --</option>
                                        <?php while($route = $routes->fetch_assoc()): ?>
                                            <option value="<?php echo $route['route_id']; ?>"
                                                    data-delay="<?php echo $route['delay_time']; ?>"
                                                    <?php echo ($route['route_id'] == $trip['route_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($route['departure']); ?> → <?php echo htmlspecialchars($route['destination']); ?> 
                                                (<?php echo number_format($route['price_per_seat']); ?> FRW)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <!-- Departure DateTime -->
                                <div class="col-md-6">
                                    <label for="departure_datetime" class="form-label fw-bold required-field">Departure Date & Time *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="datetime-local" class="form-control" id="departure_datetime" name="departure_datetime" required 
                                               value="<?php echo date('Y-m-d\TH:i', strtotime($trip['departure_datetime'])); ?>">
                                    </div>
                                </div>
                                
                                <!-- Estimated Arrival -->
                                <div class="col-md-6">
                                    <label for="estimated_arrival" class="form-label fw-bold required-field">Estimated Arrival *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-flag-checkered"></i></span>
                                        <input type="datetime-local" class="form-control" id="estimated_arrival" name="estimated_arrival" required 
                                               value="<?php echo date('Y-m-d\TH:i', strtotime($trip['estimated_arrival'])); ?>">
                                    </div>
                                </div>
                                
                                <!-- Available Seats -->
                                <div class="col-md-6">
                                    <label for="available_seats" class="form-label fw-bold required-field">Available Seats *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                                        <input type="number" class="form-control" id="available_seats" name="available_seats" required 
                                               value="<?php echo $trip['available_seats']; ?>"
                                               min="0" max="100">
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold required-field">Status *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="available" <?php echo ($trip['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                                        <option value="ontrip" <?php echo ($trip['status'] == 'ontrip') ? 'selected' : ''; ?>>On Trip</option>
                                        <option value="arrived" <?php echo ($trip['status'] == 'arrived') ? 'selected' : ''; ?>>Arrived</option>
                                        <option value="maintenance" <?php echo ($trip['status'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="admin.php?section=manage-trips" class="back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Trips
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="fas fa-undo me-2"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Update Trip
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
            const routeSelect = document.getElementById('route_id');
            const departureInput = document.getElementById('departure_datetime');
            const arrivalInput = document.getElementById('estimated_arrival');

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
            routeSelect.addEventListener('change', calculateEstimatedArrival);
            departureInput.addEventListener('change', calculateEstimatedArrival);

            // Form validation
            tripForm.addEventListener('submit', function(e) {
                const departure = departureInput.value;
                const arrival = arrivalInput.value;

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
                if (!confirm('Are you sure you want to reset all changes?')) {
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
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>