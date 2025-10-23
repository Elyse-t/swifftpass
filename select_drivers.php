<?php
include 'connection.php';

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $delete_sql = "DELETE FROM drivers WHERE driver_id = '$delete_id'";
    
    if ($conn->query($delete_sql) === TRUE) {
        $success_message = "Driver deleted successfully!";
    } else {
        $error_message = "Error deleting driver: " . $conn->error;
    }
}

// Handle edit action
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $conn->real_escape_string($_GET['edit_id']);
    $edit_sql = "SELECT * FROM drivers WHERE driver_id = '$edit_id'";
    $edit_result = $conn->query($edit_sql);
    
    if ($edit_result->num_rows > 0) {
        $edit_data = $edit_result->fetch_assoc();
    }
}

// Handle update action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $update_id = $conn->real_escape_string($_POST['update_id']);
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
                  WHERE driver_id = '$update_id'";

    if ($conn->query($update_sql) === TRUE) {
        $success_message = "Driver updated successfully!";
        $edit_data = null; // Clear edit data after successful update
    } else {
        $error_message = "Error updating driver: " . $conn->error;
    }
}

// Fetch all drivers
$sql = "SELECT * FROM drivers ORDER BY driver_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Drivers - SwiftPass</title>
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
        
        .btn-success {
            background: linear-gradient(135deg, var(--success), #27ae60);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #c0392b);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover, .btn-warning:hover, .btn-danger:hover {
            transform: translateY(-2px);
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
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        
        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-active {
            background: linear-gradient(135deg, var(--success), #27ae60);
            color: white;
        }
        
        .badge-inactive {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .no-drivers {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .no-drivers i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #bdc3c7;
        }
        
        .driver-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .contact-info {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><i class="fas fa-users me-2"></i>Manage Drivers</h3>
                        <a href="add_drivers.php" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i> Add New Driver
                        </a>
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

                        <!-- Edit Form (shown when editing) -->
                        <?php if ($edit_data): ?>
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Driver</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="update_id" value="<?php echo $edit_data['driver_id']; ?>">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label fw-bold">Driver Full Name *</label>
                                            <input type="text" class="form-control" id="name" name="name" required 
                                                   value="<?php echo htmlspecialchars($edit_data['name']); ?>">
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="contact" class="form-label fw-bold">Contact Number *</label>
                                            <input type="tel" class="form-control" id="contact" name="contact" required 
                                                   value="<?php echo htmlspecialchars($edit_data['contact']); ?>">
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="license" class="form-label fw-bold">Driver License *</label>
                                            <input type="text" class="form-control" id="license" name="license" required 
                                                   value="<?php echo htmlspecialchars($edit_data['license']); ?>">
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="status" class="form-label fw-bold">Status *</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="active" <?php echo ($edit_data['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                                <option value="inactive" <?php echo ($edit_data['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 d-flex justify-content-between">
                                        <a href="select_drivers.php" class="back-btn">
                                            <i class="fas fa-times me-2"></i> Cancel Edit
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i> Update Driver
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Drivers Table -->
                        <div class="table-responsive">
                            <?php if ($result->num_rows > 0): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Driver</th>
                                            <th>Contact Info</th>
                                            <th>License</th>
                                            <th>Status</th>
                                            <th>Registered</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result->fetch_assoc()): 
                                            $initial = substr($row['name'], 0, 1);
                                            $status_class = ($row['status'] == 'active') ? 'badge-active' : 'badge-inactive';
                                            $status_text = ucfirst($row['status']);
                                        ?>
                                        <tr>
                                            <td><strong>#<?php echo $row['driver_id']; ?></strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="driver-avatar me-3">
                                                        <?php echo strtoupper($initial); ?>
                                                    </div>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="contact-info">
                                                    <div><i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($row['contact']); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary"><?php echo htmlspecialchars($row['license']); ?></span>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                            </td>
                                            <td>
                                                <div class="contact-info">
                                                    <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="update_drivers.php?driver_id=<?php echo $row['driver_id']; ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="delete_drivers.php?delete_id=<?php echo $row['driver_id']; ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Are you sure you want to delete this driver?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="no-drivers">
                                    <i class="fas fa-users"></i>
                                    <h4>No Drivers Found</h4>
                                    <p>You haven't added any drivers yet. Start by adding your first driver.</p>
                                    <a href="add_drivers.php" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-2"></i> Add Your First Driver
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Card -->
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h4 class="text-primary"><?php echo $result->num_rows; ?></h4>
                                <p class="mb-0 text-muted">Total Drivers</p>
                            </div>
                            <div class="col-md-3">
                                <?php 
                                $active_drivers = $conn->query("SELECT COUNT(*) as active_count FROM drivers WHERE status = 'active'");
                                $active_count = $active_drivers->fetch_assoc();
                                ?>
                                <h4 class="text-success"><?php echo $active_count['active_count']; ?></h4>
                                <p class="mb-0 text-muted">Active Drivers</p>
                            </div>
                            <div class="col-md-3">
                                <?php 
                                $inactive_drivers = $conn->query("SELECT COUNT(*) as inactive_count FROM drivers WHERE status = 'inactive'");
                                $inactive_count = $inactive_drivers->fetch_assoc();
                                ?>
                                <h4 class="text-warning"><?php echo $inactive_count['inactive_count']; ?></h4>
                                <p class="mb-0 text-muted">Inactive Drivers</p>
                            </div>
                            <div class="col-md-3">
                                <?php 
                                $unique_licenses = $conn->query("SELECT COUNT(DISTINCT license) as unique_licenses FROM drivers");
                                $license_count = $unique_licenses->fetch_assoc();
                                ?>
                                <h4 class="text-info"><?php echo $license_count['unique_licenses']; ?></h4>
                                <p class="mb-0 text-muted">Unique Licenses</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add confirmation for delete actions
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-danger');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this driver? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
            
            // Scroll to edit form when editing
            <?php if ($edit_data): ?>
                document.querySelector('.card.mb-4').scrollIntoView({ behavior: 'smooth' });
            <?php endif; ?>
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>