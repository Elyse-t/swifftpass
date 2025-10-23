<?php
include 'connection.php';

// Initialize variables
$success_message = "";
$error_message = "";
$customer_data = null;

// Check if customer ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: select_customer.php");
    exit();
}

$customer_id = $conn->real_escape_string($_GET['id']);

// Fetch current customer data
$sql = "SELECT * FROM customers WHERE customer_id = '$customer_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: select_customer.php");
    exit();
}

$customer_data = $result->fetch_assoc();

// Handle form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $conn->real_escape_string(trim($_POST['firstname']));
    $lastname = $conn->real_escape_string(trim($_POST['lastname']));
    $contact = $conn->real_escape_string(trim($_POST['contact']));
    $email = $conn->real_escape_string(trim($_POST['email']));

    // Validate required fields
    if (empty($firstname) || empty($lastname) || empty($contact)) {
        $error_message = "❌ First name, last name, and contact are required fields.";
    } else {
        // Check if email already exists (if provided and changed)
        if (!empty($email) && $email != $customer_data['email']) {
            $check_email_sql = "SELECT customer_id FROM customers WHERE email = '$email' AND customer_id != '$customer_id'";
            $email_result = $conn->query($check_email_sql);
            if ($email_result->num_rows > 0) {
                $error_message = "❌ Email address already exists in the system.";
            }
        }

        // Check if contact already exists (if changed)
        if ($contact != $customer_data['contact']) {
            $check_contact_sql = "SELECT customer_id FROM customers WHERE contact = '$contact' AND customer_id != '$customer_id'";
            $contact_result = $conn->query($check_contact_sql);
            if ($contact_result->num_rows > 0) {
                $error_message = "❌ Contact number already exists in the system.";
            }
        }

        // If no errors, update the customer
        if (empty($error_message)) {
            $update_sql = "UPDATE customers SET 
                          firstname = '$firstname',
                          lastname = '$lastname',
                          contact = '$contact',
                          email = '$email',
                          updated_at = CURRENT_TIMESTAMP
                          WHERE customer_id = '$customer_id'";

            if ($conn->query($update_sql) === TRUE) {
                $success_message = "✅ Customer updated successfully!";
                // Refresh the customer data
                $result = $conn->query($sql);
                $customer_data = $result->fetch_assoc();
            } else {
                $error_message = "❌ Error updating customer: " . $conn->error;
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
    <title>Update Customer - SwiftPass</title>
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
        
        .customer-avatar {
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-user-edit me-2"></i>Update Customer</h3>
                        <p class="mb-0 opacity-75">Update customer information in SwiftPass system</p>
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

                        <!-- Customer Avatar and Basic Info -->
                        <div class="text-center mb-4">
                            <div class="customer-avatar">
                                <?php echo strtoupper(substr($customer_data['firstname'], 0, 1) . substr($customer_data['lastname'], 0, 1)); ?>
                            </div>
                            <h5><?php echo htmlspecialchars($customer_data['firstname'] . ' ' . $customer_data['lastname']); ?></h5>
                            <p class="text-muted">Customer ID: <?php echo $customer_data['customer_id']; ?></p>
                        </div>

                        <!-- Current Customer Information -->
                        <div class="current-info">
                            <h6 class="mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Current Information</h6>
                            <div class="info-item">
                                <span class="info-label">Registered:</span>
                                <span class="info-value"><?php echo date('M j, Y g:i A', strtotime($customer_data['created_at'])); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Last Updated:</span>
                                <span class="info-value"><?php echo date('M j, Y g:i A', strtotime($customer_data['updated_at'])); ?></span>
                            </div>
                        </div>

                        <form action="" method="POST" id="customerForm">
                            <div class="row g-3">
                                <!-- First Name -->
                                <div class="col-md-6">
                                    <label class="form-label required-field">First Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="firstname" class="form-control" 
                                               value="<?php echo htmlspecialchars($customer_data['firstname']); ?>" 
                                               required maxlength="50" placeholder="Enter first name">
                                    </div>
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6">
                                    <label class="form-label required-field">Last Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="lastname" class="form-control" 
                                               value="<?php echo htmlspecialchars($customer_data['lastname']); ?>" 
                                               required maxlength="50" placeholder="Enter last name">
                                    </div>
                                </div>

                                <!-- Contact -->
                                <div class="col-12">
                                    <label class="form-label required-field">Contact Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" name="contact" class="form-control" 
                                               value="<?php echo htmlspecialchars($customer_data['contact']); ?>" 
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
                                               value="<?php echo htmlspecialchars($customer_data['email']); ?>" 
                                               maxlength="100" placeholder="Enter email address (optional)">
                                    </div>
                                    <div class="form-text">Optional - must be unique if provided</div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="select_customer.php" class="back-btn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Customers
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="fas fa-undo me-2"></i> Reset Changes
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i> Update Customer
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

            // Auto-capitalize first and last names
            const nameInputs = document.querySelectorAll('input[name="firstname"], input[name="lastname"]');
            nameInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                });
            });

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
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>