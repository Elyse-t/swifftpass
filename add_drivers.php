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
    $name = $conn->real_escape_string(trim($_POST['name']));
    $contact = $conn->real_escape_string(trim($_POST['contact']));
    $license = $conn->real_escape_string(trim($_POST['license']));

    // Validate required fields
    if (empty($name) || empty($contact) || empty($license)) {
        $error_message = "❌ Error: Name, contact, and license are required fields!";
    } else {
        // Check if contact already exists
        $check_contact_sql = "SELECT * FROM drivers WHERE contact = '$contact'";
        $contact_result = $conn->query($check_contact_sql);
        
        if ($contact_result->num_rows > 0) {
            $error_message = "❌ Error: Contact number already exists!";
        } else {
            // Check if license already exists
            $check_license_sql = "SELECT * FROM drivers WHERE license = '$license'";
            $license_result = $conn->query($check_license_sql);
            
            if ($license_result->num_rows > 0) {
                $error_message = "❌ Error: License number already exists!";
            } else {
                // Insert into drivers table
                $stmt = $conn->prepare("INSERT INTO drivers (name, contact, license) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $contact, $license);

                if ($stmt->execute()) {
                    $success_message = "✅ New driver added successfully!";
                    // Clear form fields
                    $_POST = array();
                } else {
                    $error_message = "❌ Error: " . $stmt->error;
                }
                $stmt->close();
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
    <title>Add New Driver - SwiftPass</title>
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
        
        .driver-avatar {
            width: 100px;
            height: 100px;
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
        
        .required-field::after {
            content: " *";
            color: var(--danger);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-user-tie me-2"></i>Add New Driver</h3>
                        <p class="mb-0 opacity-75">Register a new driver for SwiftPass system</p>
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

                        <form action="" method="POST" id="driverForm">
                            <div class="row g-3">
                                <!-- Driver Avatar -->
                                <div class="col-12 text-center">
                                    <div class="driver-avatar">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                </div>

                                <!-- Driver Name -->
                                <div class="col-12">
                                    <label class="form-label fw-bold required-field">Driver Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="name" class="form-control" 
                                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                                               placeholder="Enter driver's full name" required maxlength="100">
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold required-field">Contact Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" name="contact" class="form-control" 
                                               value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>" 
                                               placeholder="+250 78 123 4567" required maxlength="20">
                                    </div>
                                    <div class="form-text">Unique contact number for the driver</div>
                                </div>

                                <!-- License Information -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold required-field">License Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        <input type="text" name="license" class="form-control" 
                                               value="<?php echo isset($_POST['license']) ? htmlspecialchars($_POST['license']) : ''; ?>" 
                                               placeholder="Enter license number" required maxlength="50">
                                    </div>
                                    <div class="form-text">Unique license number</div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="admin.php" class="back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="fas fa-undo me-2"></i> Clear Form
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-user-plus me-2"></i> Add Driver
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
            const driverForm = document.getElementById('driverForm');

            // Auto-capitalize driver name
            const nameInput = document.querySelector('input[name="name"]');
            nameInput.addEventListener('input', function() {
                this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
            });

            // Auto-format license number to uppercase
            const licenseInput = document.querySelector('input[name="license"]');
            licenseInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Phone number formatting for Rwanda
            const contactInput = document.querySelector('input[name="contact"]');
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

            // Form validation
            driverForm.addEventListener('submit', function(e) {
                const name = nameInput.value.trim();
                const contact = contactInput.value.trim();
                const license = licenseInput.value.trim();

                if (!name || !contact || !license) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }

                // Validate contact format
                const contactRegex = /^\+250\s\d{2}\s\d{3}\s\d{3}$/;
                if (!contactRegex.test(contact)) {
                    e.preventDefault();
                    alert('Please enter a valid Rwandan phone number (e.g., +250 78 123 4567)');
                    contactInput.focus();
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
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>