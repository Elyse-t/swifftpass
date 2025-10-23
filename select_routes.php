<?php
include 'connection.php';

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $delete_sql = "DELETE FROM routes WHERE route_id = '$delete_id'";
    
    if ($conn->query($delete_sql) === TRUE) {
        $success_message = "Route deleted successfully!";
    } else {
        $error_message = "Error deleting route: " . $conn->error;
    }
}

// Handle edit action
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $conn->real_escape_string($_GET['edit_id']);
    $edit_sql = "SELECT * FROM routes WHERE route_id = '$edit_id'";
    $edit_result = $conn->query($edit_sql);
    
    if ($edit_result->num_rows > 0) {
        $edit_data = $edit_result->fetch_assoc();
    }
}

// Handle update action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $update_id = $conn->real_escape_string($_POST['update_id']);
    $departure = $conn->real_escape_string($_POST['departure']);
    $destination = $conn->real_escape_string($_POST['destination']);
    $departure_time = $conn->real_escape_string($_POST['departure_time']);
    $price_per_seat = $conn->real_escape_string($_POST['price_per_seat']);
    $status = $conn->real_escape_string($_POST['status']);

    // Check if departure and destination are different
    if ($departure === $destination) {
        $error_message = "Error: Departure and destination cannot be the same!";
    } else {
        $update_sql = "UPDATE routes SET 
                      departure = '$departure', 
                      destination = '$destination', 
                      departure_time = '$departure_time', 
                      price_per_seat = '$price_per_seat', 
                      status = '$status'
                      WHERE route_id = '$update_id'";

        if ($conn->query($update_sql) === TRUE) {
            $success_message = "Route updated successfully!";
            $edit_data = null; // Clear edit data after successful update
        } else {
            $error_message = "Error updating route: " . $conn->error;
        }
    }
}

// Fetch all routes
$sql = "SELECT * FROM routes ORDER BY route_id DESC";
$result = $conn->query($sql);

// Get statistics
$total_routes = $result->num_rows;
$active_routes = $conn->query("SELECT COUNT(*) as active_count FROM routes WHERE status = 'active'")->fetch_assoc()['active_count'];
$inactive_routes = $conn->query("SELECT COUNT(*) as inactive_count FROM routes WHERE status = 'inactive'")->fetch_assoc()['inactive_count'];
$total_revenue = $conn->query("SELECT SUM(price_per_seat) as total_revenue FROM routes WHERE status = 'active'")->fetch_assoc()['total_revenue'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Routes - SwiftPass</title>
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
        
        .no-routes {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .no-routes i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #bdc3c7;
        }
        
        .route-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--info), var(--secondary));
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
        
        .price-badge {
            background: linear-gradient(135deg, var(--info), #138496);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .route-path {
            font-weight: 600;
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><i class="fas fa-route me-2"></i>Manage Routes</h3>
                        <a href="add_routes.php" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i> Add New Route
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
                                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Route</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="update_id" value="<?php echo $edit_data['route_id']; ?>">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="departure" class="form-label fw-bold">Departure City *</label>
                                            <input type="text" class="form-control" id="departure" name="departure" required 
                                                   value="<?php echo htmlspecialchars($edit_data['departure']); ?>">
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="destination" class="form-label fw-bold">Destination City *</label>
                                            <input type="text" class="form-control" id="destination" name="destination" required 
                                                   value="<?php echo htmlspecialchars($edit_data['destination']); ?>">
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="departure_time" class="form-label fw-bold">Departure Time *</label>
                                            <input type="time" class="form-control" id="departure_time" name="departure_time" required 
                                                   value="<?php echo htmlspecialchars($edit_data['departure_time']); ?>">
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="price_per_seat" class="form-label fw-bold">Price per Seat (FRW) *</label>
                                            <input type="number" class="form-control" id="price_per_seat" name="price_per_seat" required 
                                                   value="<?php echo htmlspecialchars($edit_data['price_per_seat']); ?>"
                                                   min="0" step="100">
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
                                        <a href="select_routes.php" class="back-btn">
                                            <i class="fas fa-times me-2"></i> Cancel Edit
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i> Update Route
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Routes Table -->
                        <div class="table-responsive">
                            <?php if ($result->num_rows > 0): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Route</th>
                                            <th>Departure Time</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result->fetch_assoc()): 
                                            $initial = substr($row['departure'], 0, 1);
                                            $status_class = 'badge-' . $row['status'];
                                            $status_text = ucfirst($row['status']);
                                        ?>
                                        <tr>
                                            <td><strong>#<?php echo $row['route_id']; ?></strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="route-avatar me-3">
                                                        <?php echo strtoupper($initial); ?>
                                                    </div>
                                                    <div>
                                                        <div class="route-path">
                                                            <?php echo htmlspecialchars($row['departure']); ?> 
                                                            <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                            <?php echo htmlspecialchars($row['destination']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary"><?php echo date('h:i A', strtotime($row['departure_time'])); ?></span>
                                            </td>
                                            <td>
                                                <span class="price-badge"><?php echo number_format($row['price_per_seat']); ?> FRW</span>
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
                                                    <a href="update_routes.php?route_id=<?php echo $row['route_id']; ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="select_routes.php?delete_id=<?php echo $row['route_id']; ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Are you sure you want to delete this route?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="no-routes">
                                    <i class="fas fa-route"></i>
                                    <h4>No Routes Found</h4>
                                    <p>You haven't added any routes yet. Start by adding your first route.</p>
                                    <a href="add_routes.php" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-2"></i> Add Your First Route
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
                                <h4 class="text-primary"><?php echo $total_routes; ?></h4>
                                <p class="mb-0 text-muted">Total Routes</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-success"><?php echo $active_routes; ?></h4>
                                <p class="mb-0 text-muted">Active Routes</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-warning"><?php echo $inactive_routes; ?></h4>
                                <p class="mb-0 text-muted">Inactive Routes</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="text-info"><?php echo number_format($total_revenue ?: '0'); ?> FRW</h4>
                                <p class="mb-0 text-muted">Total Revenue Potential</p>
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
                    if (!confirm('Are you sure you want to delete this route? This action cannot be undone.')) {
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