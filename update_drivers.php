<?php
include 'connection.php';

// Check if driver_id is provided
if (!isset($_GET['driver_id']) || empty($_GET['driver_id'])) {
    header("Location: select_drivers.php");
    exit();
}

$driver_id = $conn->real_escape_string($_GET['driver_id']);

// Fetch driver data for editing
$sql = "SELECT * FROM drivers WHERE driver_id = '$driver_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: select_drivers.php");
    exit();
}

$driver = $result->fetch_assoc();

// Handle form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $license = $conn->real_escape_string($_POST['license']);
    $status = $conn->real_escape_string($_POST['status']);

    $update_sql = "UPDATE drivers SET 
                  name = '$name', 
                  contact = '$contact', 
                  license = '$license', 
                  status = '$status',
                  updated_at = NOW()
                  WHERE driver_id = '$driver_id'";

    if ($conn->query($update_sql) === TRUE) {
        $success_message = "Driver updated successfully!";
        // Refresh the driver data
        $result = $conn->query($sql);
        $driver = $result->fetch_assoc();
    } else {
        $error_message = "Error updating driver: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Driver - SwiftPass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        .driver-info {
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
        
        .driver-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
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
                        <h3 class="mb-0"><i class="fas fa-user-edit me-2"></i>Update Driver</h3>
                        <p class="mb-0 opacity-75">Update driver information for SwiftPass</p>
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
                                <div class="col-12">
                                    <label for="name" class="form-label fw-bold">Driver Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="name" name="name" required 
                                               value="<?php echo htmlspecialchars($driver['name']); ?>"
                                               placeholder="Enter driver full name">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="contact" class="form-label fw-bold">Contact Number *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" id="contact" name="contact" required 
                                               value="<?php echo htmlspecialchars($driver['contact']); ?>"
                                               placeholder="+250 78 123 4567">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="license" class="form-label fw-bold">License Number *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        <input type="text" class="form-control" id="license" name="license" required 
                                               value="<?php echo htmlspecialchars($driver['license']); ?>"
                                               placeholder="Enter license number">
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <label for="status" class="form-label fw-bold">Status *</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="active" <?php echo ($driver['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($driver['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="select_drivers.php" class="back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Drivers
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="fas fa-undo me-2"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Update Driver
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
            // Auto-capitalize driver name
            const nameInput = document.getElementById('name');
            nameInput.addEventListener('input', function() {
                this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
            });

            // Auto-format license number to uppercase
            const licenseInput = document.getElementById('license');
            licenseInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Phone number formatting for Rwanda
            const contactInput = document.getElementById('contact');
            contactInput.addEventListener('input', function() {
                let numbers = this.value.replace(/\D/g, '');
                
                if (numbers.startsWith('250')) {
                    numbers = numbers.substring(3);
                }
                
                if (numbers.length <= 2) {
                    this.value = '+250 ' + numbers;
                } else if (numbers.length <= 5) {
                    this.value = '+250 ' + numbers.substring(0, 2) + ' ' + numbers.substring(2);
                } else if (numbers.length <= 8) {
                    this.value = '+250 ' + numbers.substring(0, 2) + ' ' + numbers.substring(2, 5) + ' ' + numbers.substring(5);
                } else {
                    this.value = '+250 ' + numbers.substring(0, 2) + ' ' + numbers.substring(2, 5) + ' ' + numbers.substring(5, 8);
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