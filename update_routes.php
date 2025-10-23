<?php
include 'connection.php';

// Debug: Show what parameters we're receiving
echo "<!-- DEBUG: GET parameters received: " . print_r($_GET, true) . " -->";
echo "<!-- DEBUG: Looking for route with ID: " . $_GET['id'] . " -->";

// Rest of your code...

// Check if route_id is provided
if (!isset($_GET['route_id']) || empty($_GET['route_id'])) {
    header("Location: admin.php?section=manage-routes");
    exit();
}

$route_id = $conn->real_escape_string($_GET['route_id']);

// Fetch route data for editing
$sql = "SELECT * FROM routes WHERE route_id = '$route_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: admin.php?section=manage-routes");
    exit();
}

$route = $result->fetch_assoc();

// Handle form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $departure = $conn->real_escape_string($_POST['departure']);
    $destination = $conn->real_escape_string($_POST['destination']);
    $delay_time = $conn->real_escape_string($_POST['delay_time']);
    $price_per_seat = $conn->real_escape_string($_POST['price_per_seat']);
    $status = $conn->real_escape_string($_POST['status']);

    // Check if departure and destination are different
    if ($departure === $destination) {
        $error_message = "❌ Error: Departure and destination cannot be the same!";
    } else {
        $update_sql = "UPDATE routes SET 
                      departure = '$departure', 
                      destination = '$destination', 
                      delay_time = '$delay_time',
                      price_per_seat = '$price_per_seat', 
                      status = '$status'
                      WHERE route_id = '$route_id'";

        if ($conn->query($update_sql) === TRUE) {
            $success_message = "✅ Route updated successfully!";
            // Refresh the route data
            $result = $conn->query($sql);
            $route = $result->fetch_assoc();
        } else {
            $error_message = "❌ Error updating route: " . $conn->error;
        }
    }
}
// Fetch all buses for dropdown
$all_routes = $conn->query("SELECT route_id, departure, destination,delay_time, price_per_seat, status   FROM routes ORDER BY route_id ASC");
?>

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Route - SwiftPass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #17a2b8;
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
        
        .route-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .info-item {
            display: flex;
            justify-content: between;
            margin-bottom: 0.5rem;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--primary);
            min-width: 120px;
        }
        
        .info-value {
            color: #2c3e50;
        }
        
        .route-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--info), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }
        
        .input-group-text {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 10px 0 0 10px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-route me-2"></i>Update Route</h3>
                        <p class="mb-0 opacity-75">Update route information for SwiftPass</p>
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

                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="departure" class="form-label fw-bold">Departure City *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control" id="departure" name="departure" required 
                                               value="<?php echo htmlspecialchars($route['departure']); ?>"
                                               placeholder="Enter departure city">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="destination" class="form-label fw-bold">Destination City *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-location-arrow"></i></span>
                                        <input type="text" class="form-control" id="destination" name="destination" required 
                                               value="<?php echo htmlspecialchars($route['destination']); ?>"
                                               placeholder="Enter destination city">
                                    </div>
                                </div>
                                
                              
                                
                                <div class="col-md-6">
                                    <label for="delay_time" class="form-label fw-bold">Delay Time (hours)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-hourglass-half"></i></span>
                                        <input type="number" class="form-control" id="delay_time" name="delay_time" 
                                               value="<?php echo htmlspecialchars($route['delay_time']); ?>"
                                               placeholder="Enter delay in hours" hour="1" max="6">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="price_per_seat" class="form-label fw-bold">Price per Seat (FRW) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                                        <input type="number" class="form-control" id="price_per_seat" name="price_per_seat" required 
                                               value="<?php echo htmlspecialchars($route['price_per_seat']); ?>"
                                               placeholder="Enter price" min="0" step="100">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status *</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="active" <?php echo ($route['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($route['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="admin.php?section=manage-routes" class="back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Routes
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="fas fa-undo me-2"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Update Route
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
            // Auto-capitalize city names
            const departureInput = document.getElementById('departure');
            const destinationInput = document.getElementById('destination');
            
            departureInput.addEventListener('input', function() {
                this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
            });
            
            destinationInput.addEventListener('input', function() {
                this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
            });

            // Price validation
            const priceInput = document.getElementById('price_per_seat');
            priceInput.addEventListener('input', function() {
                if (this.value < 0) {
                    this.value = 0;
                }
            });

            // Delay time validation
            const delayInput = document.getElementById('delay_time');
            delayInput.addEventListener('input', function() {
                if (this.value < 0) {
                    this.value = 0;
                }
                if (this.value > 240) {
                    this.value = 240;
                }
            });

            // Form validation for same departure/destination
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const departure = departureInput.value.trim();
                const destination = destinationInput.value.trim();

                if (departure.toLowerCase() === destination.toLowerCase()) {
                    e.preventDefault();
                    alert('Departure and destination cities cannot be the same.');
                    departureInput.focus();
                    return false;
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
            
            // Add confirmation for reset
            document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to reset all changes?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>