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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $plates_number = $conn->real_escape_string($_POST['plates_number']);
    $model = $conn->real_escape_string($_POST['model']);
    $number_of_seats = intval($_POST['number_of_seats']);
    
    // Check if plates number already exists
    $check_sql = "SELECT bus_id FROM buses WHERE plates_number = '$plates_number'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $error_message = "❌ Error: A bus with plates number '$plates_number' already exists!";
    } else {
        // Insert into buses table with new schema
        $stmt = $conn->prepare("INSERT INTO buses 
            (plates_number, model, number_of_seats, status, created_at) 
            VALUES (?, ?, ?, 'active', NOW())");
        $stmt->bind_param("ssi", $plates_number, $model, $number_of_seats);

        if ($stmt->execute()) {
            $bus_id = $stmt->insert_id;
            $success_message = "✅ New bus added successfully! Bus ID: $bus_id";
        } else {
            $error_message = "❌ Error: " . $stmt->error;
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
    <title>Add New Bus - SwiftPass</title>
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
        
        .quick-action-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .btn-dashboard {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 1rem;
        }
        
        .btn-success-dash {
            background: linear-gradient(135deg, var(--success), #27ae60);
            color: white;
        }
        
        .btn-primary-dash {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
        }
        
        .btn-warning-dash {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            color: white;
        }
        
        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }
        
        .info-card {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .capacity-indicator {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-radius: 8px;
            padding: 0.75rem;
            text-align: center;
            margin-top: 0.5rem;
            font-weight: 600;
            color: #155724;
        }
        
        .plaque-preview {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-radius: 8px;
            padding: 0.75rem;
            text-align: center;
            margin-top: 0.5rem;
            font-weight: 600;
            color: #856404;
            font-family: monospace;
            font-size: 1.2rem;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-bus me-2"></i>Add New Bus</h3>
                        <p class="mb-0 opacity-75">Register a new bus for SwiftPass fleet</p>
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

                

                        <form action="" method="POST" id="busForm">
                            <div class="row g-3">
                                <!-- Plates Number -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Plates Number *</label>
                                    <input type="text" name="plates_number" class="form-control" 
                                           id="plates_number" placeholder="Enter plates number " 
                                           pattern="[A-Z0-9]{6,7}" title="Enter a valid plates number"
                                           required>
                                   
                                    <small class="text-muted">Format: 6-7 characters, uppercase letters and numbers only</small>
                                </div>

                                <!-- Bus Model -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Bus Model *</label>
                                    <select name="model" class="form-select" id="model" required>
                                        <option value="">Select Bus Model</option>
                                        <option value="Coaster">Toyota (30 seats)</option>
                                        <option value="Hiace">Hyundai (18 seats)</option>
                                        <option value="Bus">Benz Bus (45 seats)</option>
                            
                                        <option value="other">Other (specify below)</option>
                                    </select>
                                </div>

                                <!-- Custom Model Input -->
                                <div class="col-md-6" id="custom_model_container" style="display: none;">
                                    <label class="form-label fw-bold">Custom Model Name</label>
                                    <input type="text" name="custom_model" class="form-control" 
                                           id="custom_model" placeholder="Enter custom bus model">
                                </div>

                                <!-- Number of Seats -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Number of Seats *</label>
                                    <input type="number" name="number_of_seats" class="form-control" 
                                           id="number_of_seats" min="1" max="100" 
                                           placeholder="Enter total number of seats" required>
                                    <div class="capacity-indicator" id="capacity_indicator">
                                        Select bus model to see recommended capacity
                                    </div>
                                </div>

                                <!-- Auto-filled Status Information -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Automatic Status</label>
                                    <div class="capacity-indicator">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <span>New buses are automatically set to 'active' status</span>
                                   
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="admin.php?section=manage-buses" class="back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Buses
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i> Add Bus to Fleet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

               
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const platesNumber = document.getElementById('plates_number');
            const plaquePreview = document.getElementById('plaque_preview');
            const modelSelect = document.getElementById('model');
            const customModelContainer = document.getElementById('custom_model_container');
            const customModelInput = document.getElementById('custom_model');
            const numberSeats = document.getElementById('number_of_seats');
            const capacityIndicator = document.getElementById('capacity_indicator');

            // Model capacities mapping
            const modelCapacities = {
                'Coaster': 30,
                'Hiace': 18,
                'Bus': 45,
                'Mini Bus': 14,
                'Luxury Coach': 50,
                'other': 0
            };

            // Update plates number preview
            platesNumber.addEventListener('input', function() {
                const value = this.value.toUpperCase();
                this.value = value;
                plaquePreview.textContent = value || 'Plates Number Preview';
            });

            // Handle model selection
            modelSelect.addEventListener('change', function() {
                const selectedModel = this.value;
                
                // Show/hide custom model input
                if (selectedModel === 'other') {
                    customModelContainer.style.display = 'block';
                    customModelInput.setAttribute('required', 'required');
                } else {
                    customModelContainer.style.display = 'none';
                    customModelInput.removeAttribute('required');
                    
                    // Auto-fill number of seats based on model
                    if (modelCapacities[selectedModel] > 0) {
                        numberSeats.value = modelCapacities[selectedModel];
                        updateCapacityIndicator(selectedModel, modelCapacities[selectedModel]);
                    }
                }
            });

            // Update capacity indicator
            function updateCapacityIndicator(model, capacity) {
                capacityIndicator.innerHTML = `
                    <i class="fas fa-users me-2"></i>
                    <span>${model} - Recommended Capacity: ${capacity} seats</span>
                `;
            }

            // Handle custom model input
            customModelInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    capacityIndicator.innerHTML = `
                        <i class="fas fa-users me-2"></i>
                        <span>Custom Model: ${this.value}</span>
                    `;
                }
            });

            // Handle manual seat number input
            numberSeats.addEventListener('input', function() {
                const seats = parseInt(this.value) || 0;
                const selectedModel = modelSelect.value;
                
                if (selectedModel && selectedModel !== 'other' && modelCapacities[selectedModel]) {
                    const recommended = modelCapacities[selectedModel];
                    if (seats !== recommended) {
                        capacityIndicator.innerHTML = `
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span>Note: Recommended capacity for ${selectedModel} is ${recommended} seats</span>
                        `;
                        capacityIndicator.style.background = 'linear-gradient(135deg, #fff3cd, #ffeaa7)';
                        capacityIndicator.style.color = '#856404';
                    } else {
                        updateCapacityIndicator(selectedModel, recommended);
                        capacityIndicator.style.background = 'linear-gradient(135deg, #d4edda, #c3e6cb)';
                        capacityIndicator.style.color = '#155724';
                    }
                }
            });

            // Form validation
            document.getElementById('busForm').addEventListener('submit', function(e) {
                const platesValue = platesNumber.value.trim();
                const modelValue = modelSelect.value;
                const seatsValue = parseInt(numberSeats.value);
                
                // Validate plates number format
                if (!/^[A-Z0-9]{6,7}$/.test(platesValue)) {
                    e.preventDefault();
                    alert('Please enter a valid plates number (6-7 uppercase letters and numbers)');
                    platesNumber.focus();
                    return;
                }
                
                // Validate model selection
                if (!modelValue) {
                    e.preventDefault();
                    alert('Please select a bus model');
                    modelSelect.focus();
                    return;
                }
                
                // Validate custom model if "other" is selected
                if (modelValue === 'other' && !customModelInput.value.trim()) {
                    e.preventDefault();
                    alert('Please enter the custom bus model name');
                    customModelInput.focus();
                    return;
                }
                
                // Validate seat capacity
                if (!seatsValue || seatsValue < 1 || seatsValue > 100) {
                    e.preventDefault();
                    alert('Please enter a valid number of seats (1-100)');
                    numberSeats.focus();
                    return;
                }
            });

            // Auto-format plates number to uppercase
            platesNumber.addEventListener('blur', function() {
                this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>