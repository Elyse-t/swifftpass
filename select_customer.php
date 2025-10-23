<?php
include 'connection.php';

// Initialize variables
$search_term = "";
$customers = [];

// Handle search
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search_term = $conn->real_escape_string(trim($_GET['search']));
    
    if (!empty($search_term)) {
        $search_sql = "SELECT * FROM customers 
                      WHERE firstname LIKE '%$search_term%' 
                         OR lastname LIKE '%$search_term%' 
                         OR contact LIKE '%$search_term%' 
                         OR email LIKE '%$search_term%'
                      ORDER BY created_at DESC";
        $result = $conn->query($search_sql);
    } else {
        $result = $conn->query("SELECT * FROM customers ORDER BY created_at DESC");
    }
} else {
    $result = $conn->query("SELECT * FROM customers ORDER BY created_at DESC");
}

if ($result && $result->num_rows > 0) {
    $customers = $result->fetch_all(MYSQLI_ASSOC);
}

// Get total customer count
$total_customers = $conn->query("SELECT COUNT(*) as total FROM customers")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Customer - SwiftPass</title>
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
            padding: 0.5rem 1.5rem;
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
        
        .btn-info {
            background: linear-gradient(135deg, var(--info), #138496);
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
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
        
        .table {
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
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
            transform: translateY(-1px);
        }
        
        .stats-card {
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
        
        .search-box {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .action-buttons .btn {
            margin: 0 2px;
            border-radius: 8px;
        }
        
        .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .pagination .page-link {
            border-radius: 10px;
            margin: 0 2px;
            border: none;
            color: var(--primary);
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="mb-0"><i class="fas fa-users me-2"></i>Select Customer</h3>
                        <p class="mb-0 opacity-75">Manage and select customers from SwiftPass system</p>
                    </div>
                    <div class="card-body p-4">
                        <!-- Search and Stats Section -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="search-box">
                                    <form action="" method="GET" class="row g-3">
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent border-end-0">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                                <input type="text" name="search" class="form-control border-start-0" 
                                                       value="<?php echo htmlspecialchars($search_term); ?>" 
                                                       placeholder="Search by name, contact, or email...">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-search me-2"></i> Search
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card text-center">
                                    <div class="stat-number"><?php echo $total_customers; ?></div>
                                    <div class="stat-label">TOTAL CUSTOMERS</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="admin.php" class="back-btn">
                                        <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                    </a>
                                    <div>
                                        <a href="add_customer.php" class="btn btn-success">
                                            <i class="fas fa-user-plus me-2"></i> Add New Customer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customers Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Contact</th>
                                        <th>Email</th>
                                        <th>Registered</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($customers) > 0): ?>
                                        <?php foreach ($customers as $index => $customer): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="customer-avatar me-3">
                                                            <?php echo strtoupper(substr($customer['firstname'], 0, 1) . substr($customer['lastname'], 0, 1)); ?>
                                                        </div>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($customer['firstname'] . ' ' . $customer['lastname']); ?></strong>
                                                            <br>
                                                            <small class="text-muted">ID: <?php echo $customer['customer_id']; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <i class="fas fa-phone text-primary me-2"></i>
                                                    <?php echo htmlspecialchars($customer['contact']); ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($customer['email'])): ?>
                                                        <i class="fas fa-envelope text-primary me-2"></i>
                                                        <?php echo htmlspecialchars($customer['email']); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small>
                                                        <i class="fas fa-calendar text-primary me-2"></i>
                                                        <?php echo date('M j, Y', strtotime($customer['created_at'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small>
                                                        <i class="fas fa-clock text-primary me-2"></i>
                                                        <?php echo date('M j, Y', strtotime($customer['updated_at'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="action-buttons d-flex">
                                                        <a href="update_customer.php?id=<?php echo $customer['customer_id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="Edit Customer">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <a href="delete_customer.php?id=<?php echo $customer['customer_id']; ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           title="Delete Customer"
                                                           onclick="return confirm('Are you sure you want to delete this customer?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="py-5">
                                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No Customers Found</h5>
                                                    <p class="text-muted mb-0">
                                                        <?php if (!empty($search_term)): ?>
                                                            No customers match your search criteria "<?php echo htmlspecialchars($search_term); ?>"
                                                        <?php else: ?>
                                                            No customers registered in the system yet.
                                                        <?php endif; ?>
                                                    </p>
                                                    <?php if (empty($search_term)): ?>
                                                        <a href="add_customers.php" class="btn btn-success mt-3">
                                                            <i class="fas fa-user-plus me-2"></i> Add First Customer
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Results Info -->
                        <?php if (count($customers) > 0): ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-muted">
                                            Showing <?php echo count($customers); ?> 
                                            <?php echo count($customers) === 1 ? 'customer' : 'customers'; ?>
                                            <?php if (!empty($search_term)): ?>
                                                matching "<?php echo htmlspecialchars($search_term); ?>"
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <a href="select_customer.php" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-sync-alt me-2"></i> Clear Search
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus search input
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
            }

            // Add confirmation for delete actions
            const deleteButtons = document.querySelectorAll('a[href*="delete_customer"]');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                });
            });

            // Quick search enhancement
            searchInput.addEventListener('input', function() {
                const searchValue = this.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>