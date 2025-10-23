<?php
include 'connection.php';

// Initialize variables
$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $conn->real_escape_string(trim($_POST['firstname']));
    $lastname = $conn->real_escape_string(trim($_POST['lastname']));
    $contact = $conn->real_escape_string(trim($_POST['contact']));
    $email = $conn->real_escape_string(trim($_POST['email']));

    // Validate required fields
    if (empty($firstname) || empty($lastname) || empty($contact)) {
        $error_message = "❌ First name, last name, and contact are required fields.";
    } else {
        // Check if email already exists (if provided)
        if (!empty($email)) {
            $check_email_sql = "SELECT customer_id FROM customers WHERE email = '$email'";
            $email_result = $conn->query($check_email_sql);
            if ($email_result->num_rows > 0) {
                $error_message = "❌ Email address already exists in the system.";
            }
        }

        // Check if contact already exists
        $check_contact_sql = "SELECT customer_id FROM customers WHERE contact = '$contact'";
        $contact_result = $conn->query($check_contact_sql);
        if ($contact_result->num_rows > 0) {
            $error_message = "❌ Contact number already exists in the system.";
        }

        // If no errors, insert the customer
        if (empty($error_message)) {
            $insert_sql = "INSERT INTO customers (firstname, lastname, contact, email) 
                          VALUES ('$firstname', '$lastname', '$contact', '$email')";

            if ($conn->query($insert_sql) === TRUE) {
                $success_message = "✅ Customer added successfully!";
                
                // Clear form fields
                $_POST = array();
            } else {
                $error_message = "❌ Error adding customer: " . $conn->error;
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
    <title>Add Customer - SwiftPass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        .form-label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .required-field::after {
            content: " *";
            color: var(--danger);
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
        
        .feature-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--secondary);
        }
        
        .feature-icon {
            font-size: 2rem;
            color: var(--secondary);
            margin-bottom: 1rem;
        }
        
        .quick-stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New Customer</h3>
                        <p class="mb-0 opacity-75">Register a new customer in SwiftPass system</p>
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

                        <div class="row">
                            <div class="col-lg-8">
                                <form action="" method="POST" id="customerForm">
                                    <div class="row g-3">
                                        <!-- First Name -->
                                        <div class="col-md-6">
                                            <label class="form-label required-field">First Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" name="firstname" class="form-control" 
                                                       value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>" 
                                                       required maxlength="50" placeholder="Enter first name">
                                            </div>
                                        </div>

                                        <!-- Last Name -->
                                        <div class="col-md-6">
                                            <label class="form-label required-field">Last Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" name="lastname" class="form-control" 
                                                       value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>" 
                                                       required maxlength="50" placeholder="Enter last name">
                                            </div>
                                        </div>

                                        <!-- Contact -->
                                        <div class="col-12">
                                            <label class="form-label required-field">Contact Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input type="tel" name="contact" class="form-control" 
                                                       value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>" 
                                                       required maxlength="20" placeholder="Enter contact number">
                                            </div>
                                            <div class="form-text">Unique contact number for the customer</div>
                                        </div>

                                        <!-- Email -->
                                        <div class="col-12">
                                            <label class="form-label">Email Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" name="email" class="form-control" 
                                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                                       maxlength="100" placeholder="Enter email address (optional)">
                                            </div>
                                            <div class="form-text">Optional - must be unique if provided</div>
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
                                                <i class="fas fa-user-plus me-2"></i> Add Customer
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-lg-4">
                                <!-- Quick Stats -->
                                <div class="quick-stats text-center">
                                    <div class="stat-number">
                                        <?php
                                        $total_customers = $conn->query("SELECT COUNT(*) as total FROM customers")->fetch_assoc()['total'];
                                        echo $total_customers;
                                        ?>
                                    </div>
                                    <div class="stat-label">TOTAL CUSTOMERS</div>
                                </div>
                        </div>

                                
                            </div>
                        </div>
                    </div>
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

            // Form validation
            const form = document.getElementById('customerForm');
            form.addEventListener('submit', function(e) {
                const firstname = document.querySelector('input[name="firstname"]').value.trim();
                const lastname = document.querySelector('input[name="lastname"]').value.trim();
                const contact = document.querySelector('input[name="contact"]').value.trim();

                if (!firstname || !lastname || !contact) {
                    e.preventDefault();
                    alert('Please fill in all required fields (First Name, Last Name, and Contact).');
                    return false;
                }
            });

            // Auto-capitalize first and last names
            const nameInputs = document.querySelectorAll('input[name="firstname"], input[name="lastname"]');
            nameInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                });
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>