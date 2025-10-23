<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $departure = $conn->real_escape_string($_POST['departure']);
    $destination = $conn->real_escape_string($_POST['destination']);
    $delay_time = $conn->real_escape_string($_POST['delay_time']);
    $price_per_seat = $conn->real_escape_string($_POST['price_per_seat']);
    $status = 'active'; // Automatically set to active

    // Check if route already exists (same departure and destination)
    $check_sql = "SELECT route_id FROM routes WHERE departure = '$departure' AND destination = '$destination'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $error_message = "❌ Error: Route from $departure to $destination already exists!";
    } else {
        $sql = "INSERT INTO routes (departure, destination, delay_time, price_per_seat, status) 
                VALUES ('$departure', '$destination', '$delay_time', '$price_per_seat', '$status')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "✅ New route added successfully!";
            // Clear form fields
            $_POST = array();
        } else {
            $error_message = "❌ Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Route - SwiftPass</title>
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
        
        .route-icon {
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
        
        .auto-status-badge {
            background: linear-gradient(135deg, var(--success), #27ae60);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-box {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--success);
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn-success, .btn-primary {
                padding: 0.6rem 1.5rem;
                font-size: 0.9rem;
            }
            
            .route-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .form-control {
                padding: 0.6rem 0.8rem;
            }
        }
        
        @media (max-width: 576px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }
            
            .d-flex.justify-content-between > * {
                width: 100%;
                text-align: center;
            }
            
            .back-btn, .btn-warning, .btn-success {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-route me-2"></i>Add New Route</h3>
                        <p class="mb-0 opacity-75">Create a new bus route for SwiftPass system</p>
                    </div>
                    <div class="card-body p-3 p-md-4">
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

                        <form method="POST" action="" id="routeForm">
                            <div class="row g-3">
                                <!-- Route Icon -->
                                <div class="col-12 text-center">
                                    <div class="route-icon">
                                        <i class="fas fa-route"></i>
                                    </div>
                                </div>

                                <!-- Departure -->
                                <div class="col-md-6">
                                    <label for="departure" class="form-label fw-bold required-field">Departure City *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control" id="departure" name="departure" required 
                                               value="<?php echo isset($_POST['departure']) ? htmlspecialchars($_POST['departure']) : ''; ?>"
                                               placeholder="Enter departure city">
                                    </div>
                                </div>
                                
                                <!-- Destination -->
                                <div class="col-md-6">
                                    <label for="destination" class="form-label fw-bold required-field">Destination City *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-location-arrow"></i></span>
                                        <input type="text" class="form-control" id="destination" name="destination" required 
                                               value="<?php echo isset($_POST['destination']) ? htmlspecialchars($_POST['destination']) : ''; ?>"
                                               placeholder="Enter destination city">
                                    </div>
                                </div>
                                
                                <!-- Delay Time -->
                                <div class="col-md-6">
                                    <label for="delay_time" class="form-label fw-bold required-field">Journey Duration *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                        <input type="text" class="form-control" id="delay_time" name="delay_time" required 
                                               value="<?php echo isset($_POST['delay_time']) ? htmlspecialchars($_POST['delay_time']) : ''; ?>"
                                               placeholder="e.g., 2 hours, 3h 30m">
                                    </div>
                                    <div class="form-text">Estimated journey duration (e.g., "2 hours", "3h 30m", "4 hrs")</div>
                                </div>
                            <!-- Price per Seat -->
<div class="col-md-6">
    <label for="price_per_seat" class="form-label fw-bold required-field">
        Price per Seat (FRW) *
    </label>
    <div class="input-group">
        <span class="input-group-text">
            <i class="fas fa-money-bill-wave"></i>
        </span>
        <input type="text" 
               class="form-control" 
               id="price_per_seat" 
               name="price_per_seat" 
               required
               pattern="^\d+(\.\d{1,2})?$" 
               title="Enter a valid price, e.g., 1000 or 2500.50" 
               placeholder="Enter price"
               value="<?php echo isset($_POST['price_per_seat']) ? htmlspecialchars($_POST['price_per_seat']) : ''; ?>">
        <div class="invalid-feedback">
            Please enter a valid price (numbers only, up to 2 decimal places).
        </div>
    </div>
</div>

                                    <div class="form-text">Price per passenger seat</div>
                                </div>
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between flex-wrap gap-2">
                                <a href="admin.php" class="back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </a>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="reset" class="btn btn-warning">
                                        <i class="fas fa-undo me-2"></i> Clear Form
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i> Add Route
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
            const routeForm = document.getElementById('routeForm');

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

            // Delay time validation and formatting
            const delayTimeInput = document.getElementById('delay_time');
            delayTimeInput.addEventListener('blur', function() {
                let value = this.value.trim();
                
                // Format common patterns
                if (value.match(/^\d+$/)) {
                    // If just numbers, assume hours
                    this.value = value + ' hours';
                } else if (value.match(/^\d+\s*h$/i)) {
                    // Format "2h" to "2 hours"
                    this.value = value.replace(/(\d+)\s*h/i, '$1 hours');
                } else if (value.match(/^\d+\s*hrs?$/i)) {
                    // Format "2hrs" to "2 hours"
                    this.value = value.replace(/(\d+)\s*hrs?/i, '$1 hours');
                }
            });

            // Form validation
            routeForm.addEventListener('submit', function(e) {
                const departure = departureInput.value.trim();
                const destination = destinationInput.value.trim();
                const delayTime = delayTimeInput.value.trim();
                const price = priceInput.value.trim();

                if (!departure || !destination || !delayTime || !price) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }

                // Check if departure and destination are the same
                if (departure.toLowerCase() === destination.toLowerCase()) {
                    e.preventDefault();
                    alert('Departure and destination cities cannot be the same.');
                    departureInput.focus();
                    return false;
                }

                // Validate delay time format
                if (!delayTime.match(/^(\d+\s*(hours?|hrs?|h)(\s*\d+\s*(minutes?|mins?|m))?|\d+\s*(minutes?|mins?|m))$/i)) {
                    e.preventDefault();
                    alert('Please enter a valid journey duration (e.g., "2 hours", "3h 30m", "45 minutes")');
                    delayTimeInput.focus();
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

            // Set default delay time to "2 hours"
            document.getElementById('delay_time').value = '2 hours';
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>