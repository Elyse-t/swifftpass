<?php
include 'connection.php';

// Initialize variables
$success_message = "";
$error_message = "";
$bus_data = null;

// Check if bus_id is provided for editing
if (!isset($_GET['bus_id']) || empty($_GET['bus_id'])) {
    header("Location: select_buses.php");
    exit();
}

$bus_id = $conn->real_escape_string($_GET['bus_id']);

// Fetch current bus data
$sql = "SELECT * FROM buses WHERE bus_id = '$bus_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: select_buses.php");
    exit();
}

$bus_data = $result->fetch_assoc();

// Handle form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plates_number = $conn->real_escape_string($_POST['plates_number']);
    $model = $conn->real_escape_string($_POST['model']);
    $number_of_seats = $conn->real_escape_string($_POST['number_of_seats']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // DEBUG: Check what values we're receiving
    error_log("DEBUG - Received status: " . $status);
    error_log("DEBUG - All POST data: " . print_r($_POST, true));

    // Update bus record
    $update_sql = "UPDATE buses SET 
                  plates_number = '$plates_number',
                  model = '$model',
                  number_of_seats = '$number_of_seats',
                  status = '$status'
                  WHERE bus_id = '$bus_id'";
    
    error_log("DEBUG - SQL Query: " . $update_sql);

    if ($conn->query($update_sql) === TRUE) {
        $success_message = "✅ Bus updated successfully!";
        // Refresh the bus data
        $result = $conn->query($sql);
        $bus_data = $result->fetch_assoc();
        
        // DEBUG: Check what was actually updated
        error_log("DEBUG - Update successful. New status should be: " . $status);
    } else {
        $error_message = "❌ Error updating bus: " . $conn->error;
        error_log("DEBUG - SQL Error: " . $conn->error);
    }
}
// Fetch all buses for dropdown
$all_buses = $conn->query("SELECT bus_id, plates_number, model FROM buses ORDER BY plates_number ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Bus - SwiftPass</title>
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
        
        .current-info {
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
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-maintenance {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Update Bus</h3>
                        <p class="mb-0 opacity-75">Update bus information for SwiftPass</p>
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

                        <!-- ADDED THE MISSING FORM TAG -->
                        <form method="POST" action="">
                            <div class="row g-3">
                                <!-- Plates Number -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Plates Number *</label>
                                    <input type="text" name="plates_number" class="form-control" 
                                           value="<?php echo htmlspecialchars($bus_data['plates_number']); ?>" 
                                           required maxlength="20" placeholder="Enter license plate number">
                                    <div class="form-text">Unique license plate number</div>
                                </div>

                                <!-- Model -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Model *</label>
                                    <input type="text" name="model" class="form-control" 
                                           value="<?php echo htmlspecialchars($bus_data['model']); ?>" 
                                           required maxlength="50" placeholder="Enter bus model">
                                </div>

                                <!-- Number of Seats -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Number of Seats *</label>
                                    <input type="number" name="number_of_seats" class="form-control" 
                                           value="<?php echo htmlspecialchars($bus_data['number_of_seats']); ?>" 
                                           required min="1" max="100" placeholder="Enter seat count">
                                    <div class="form-text">Must be greater than 0</div>
                                </div>

                                <!-- Status -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Status *</label>
                                    <select name="status" class="form-select" required>
                                        <option value="active" <?php echo $bus_data['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="maintenance" <?php echo $bus_data['status'] == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                        <option value="inactive" <?php echo $bus_data['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="admin.php" class="back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to manage buses
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="fas fa-undo me-2"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Update Bus
                                    </button>
                                </div>
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

            // Real-time validation for plates number
            const platesInput = document.querySelector('input[name="plates_number"]');
            platesInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>