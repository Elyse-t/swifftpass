<?php
session_start();
include('connection.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if user is admin
$userId = $_SESSION['user_id'];
$userQuery = $conn->query("SELECT role FROM users WHERE id = $userId");
$userData = $userQuery->fetch_assoc();

if ($userData['role'] != 'admin') {
    header("Location: unauthorized.php");
    exit;
}

// Get dashboard statistics
$total_buses = $conn->query("SELECT COUNT(*) as total FROM buses")->fetch_assoc()['total'];
$available_buses = $conn->query("SELECT COUNT(*) as total FROM buses WHERE status = 'active'")->fetch_assoc()['total'];
$maintenance_buses = $conn->query("SELECT COUNT(*) as total FROM buses WHERE status = 'maintenance'")->fetch_assoc()['total'];
$inactive_buses = $conn->query("SELECT COUNT(*) as total FROM buses WHERE status = 'inactive'")->fetch_assoc()['total'];

$total_routes = $conn->query("SELECT COUNT(*) as count FROM routes")->fetch_assoc()['count'];
$active_routes = $conn->query("SELECT COUNT(*) as count FROM routes WHERE status = 'active'")->fetch_assoc()['count'];

$total_drivers = $conn->query("SELECT COUNT(*) as count FROM drivers")->fetch_assoc()['count'];
$active_drivers = $conn->query("SELECT COUNT(*) as count FROM drivers WHERE status = 'active'")->fetch_assoc()['count'];

$total_customers = $conn->query("SELECT COUNT(*) as count FROM customers")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$total_trips = $conn->query("SELECT COUNT(*) as count FROM trips")->fetch_assoc()['count'];

// Today's bookings and revenue
$today_bookings_result = $conn->query("
    SELECT COUNT(*) as count, SUM(b.number_of_seats * r.price_per_seat) as revenue 
    FROM bookings b 
    JOIN trips t ON b.trip_id = t.trip_id 
    JOIN routes r ON t.route_id = r.route_id 
    WHERE DATE(b.booking_date) = CURDATE()
");
$today_data = $today_bookings_result->fetch_assoc();
$today_bookings = $today_data['count'];
$today_revenue = $today_data['revenue'] ? $today_data['revenue'] : 0;

// Fetch data for different sections
$all_buses = $conn->query("SELECT * FROM buses ORDER BY created_at DESC");
$all_routes = $conn->query("SELECT * FROM routes ORDER BY created_at DESC");
$all_drivers = $conn->query("SELECT * FROM drivers ORDER BY created_at DESC");
$all_trips = $conn->query("
    SELECT t.*, b.plates_number, d.name as driver_name, 
           r.departure, r.destination, r.price_per_seat
    FROM trips t
    JOIN buses b ON t.bus_id = b.bus_id
    JOIN drivers d ON t.driver_id = d.driver_id
    JOIN routes r ON t.route_id = r.route_id
    ORDER BY t.departure_datetime DESC
    LIMIT 50
");

$recent_bookings = $conn->query("
    SELECT b.*, c.firstname, c.lastname, c.contact, c.email,
           t.departure_datetime, bus.plates_number,
           r.departure, r.destination, r.price_per_seat
    FROM bookings b
    JOIN customers c ON b.customer_id = c.customer_id
    JOIN trips t ON b.trip_id = t.trip_id
    JOIN buses bus ON t.bus_id = bus.bus_id
    JOIN routes r ON t.route_id = r.route_id
    ORDER BY b.booking_date DESC
    LIMIT 8
");

$all_customers = $conn->query("SELECT * FROM customers ORDER BY created_at DESC LIMIT 50");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - SwiftPass</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Your existing CSS styles remain the same */
    :root {
      --primary: #2c3e50;
      --secondary: #3498db;
      --success: #2ecc71;
      --warning: #f39c12;
      --danger: #e74c3c;
      --info: #1abc9c;
      --dark: #191e32;
      --light: #ecf0f1;
      --sidebar-width: 280px;
    }
    
    body {
      background: linear-gradient(135deg, #191e32ff 0%, #1a151fff 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #fff;
      overflow-x: hidden;
    }
    
    /* ... (all your existing CSS styles remain exactly the same) ... */
    
    .sidebar {
      width: var(--sidebar-width);
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      height: 100vh;
      position: fixed;
      transition: all 0.3s;
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      border-right: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    /* ... (rest of your CSS remains unchanged) ... */
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-brand">
      <h4><i class="fas fa-bus"></i> <span>SwiftPass</span></h4>
    </div>
    <div class="nav-container">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="#" class="nav-link active" data-section="dashboard">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-buses">
            <i class="fas fa-bus"></i>
            <span>Manage Buses</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-routes">
            <i class="fas fa-route"></i>
            <span>Manage Routes</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-trips">
            <i class="fas fa-road"></i>
            <span>Manage Trips</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-drivers">
            <i class="fas fa-users"></i>
            <span>Manage Drivers</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-bookings">
            <i class="fas fa-ticket-alt"></i>
            <span>Manage Bookings</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-customers">
            <i class="fas fa-user-friends"></i>
            <span>Manage Customers</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-users">
            <i class="fas fa-user-cog"></i>
            <span>Manage Users</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="settings.php" class="nav-link">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Header -->
    <div class="header">
      <div>
        <h2 id="page-title">Welcome Admin 👨‍💼</h2>
        <p id="page-subtitle">Complete system management dashboard</p>
      </div>
      <div class="user-info">
        <div class="text-end">
          <div class="fw-bold text-dark">Admin Swift</div>
          <small class="text-muted">System Administrator</small>
        </div>
        <div class="user-avatar">AS</div>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">
          <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
      </div>
    </div>

    <!-- Dashboard Section -->
    <div id="dashboard" class="dashboard-section">
      <!-- Statistics Cards -->
      <div class="row g-4">
        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <h5><i class="fas fa-ticket-alt me-2"></i>TODAY'S BOOKINGS</h5>
            <p class="number"><?php echo $today_bookings; ?></p>
            <div class="trend up">
              <i class="fas fa-calendar-day"></i> Today's total
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <h5><i class="fas fa-chart-line me-2"></i>TODAY'S REVENUE</h5>
            <p class="number"><?php echo number_format($today_revenue); ?> FRW</p>
            <div class="trend up">
              <i class="fas fa-money-bill-wave"></i> Revenue generated
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <h5><i class="fas fa-bus me-2"></i>ACTIVE BUSES</h5>
            <p class="number"><?php echo $available_buses; ?></p>
            <div class="trend up">
              <i class="fas fa-check-circle"></i> Ready for service
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <h5><i class="fas fa-road me-2"></i>ACTIVE ROUTES</h5>
            <p class="number"><?php echo $active_routes; ?></p>
            <div class="trend up">
              <i class="fas fa-route"></i> Operational routes
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Stats Row -->
      <div class="row g-4 mt-2">
        <div class="col-xl-2 col-md-4">
          <div class="stat-card">
            <h5><i class="fas fa-users me-2"></i>DRIVERS</h5>
            <p class="number"><?php echo $total_drivers; ?></p>
            <div class="trend up">
              <i class="fas fa-id-card"></i> Total drivers
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-md-4">
          <div class="stat-card">
            <h5><i class="fas fa-user-friends me-2"></i>CUSTOMERS</h5>
            <p class="number"><?php echo $total_customers; ?></p>
            <div class="trend up">
              <i class="fas fa-users"></i> Registered customers
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-md-4">
          <div class="stat-card">
            <h5><i class="fas fa-clipboard-list me-2"></i>TRIPS</h5>
            <p class="number"><?php echo $total_trips; ?></p>
            <div class="trend up">
              <i class="fas fa-route"></i> Scheduled trips
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-md-4">
          <div class="stat-card">
            <h5><i class="fas fa-bookmark me-2"></i>BOOKINGS</h5>
            <p class="number"><?php echo $total_bookings; ?></p>
            <div class="trend up">
              <i class="fas fa-ticket-alt"></i> Total bookings
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-md-4">
          <div class="stat-card">
            <h5><i class="fas fa-tools me-2"></i>MAINTENANCE</h5>
            <p class="number"><?php echo $maintenance_buses; ?></p>
            <div class="trend down">
              <i class="fas fa-wrench"></i> Under maintenance
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-md-4">
          <div class="stat-card">
            <h5><i class="fas fa-ban me-2"></i>INACTIVE</h5>
            <p class="number"><?php echo $inactive_buses; ?></p>
            <div class="trend down">
              <i class="fas fa-times-circle"></i> Inactive buses
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Bookings Table -->
      <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Bookings</h4>
          <a href="#" class="btn btn-primary" onclick="showSection('manage-bookings')">
            <i class="fas fa-eye me-2"></i>View All
          </a>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>Route</th>
                <th>Travel Date</th>
                <th>Seats</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($recent_bookings->num_rows > 0): ?>
                <?php while ($booking = $recent_bookings->fetch_assoc()): ?>
                  <tr>
                    <td><strong>#BK<?php echo str_pad($booking['booking_id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                    <td><?php echo htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($booking['contact']); ?></td>
                    <td><?php echo htmlspecialchars($booking['departure'] . ' → ' . $booking['destination']); ?></td>
                    <td><?php echo date('M j, Y H:i', strtotime($booking['departure_datetime'])); ?></td>
                    <td><?php echo $booking['number_of_seats']; ?></td>
                    <td><strong><?php echo number_format($booking['number_of_seats'] * $booking['price_per_seat']); ?> FRW</strong></td>
                    <td>
                      <span class="badge badge-success">Confirmed</span>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No recent bookings found.</p>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Manage Buses Section -->
    <div id="manage-buses" class="dashboard-section hidden">
      <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0"><i class="fas fa-bus me-2"></i>Bus Management</h4>
          <div>
            <a href="add_buses.php" class="btn btn-success">
              <i class="fas fa-plus me-2"></i>Add New Bus
            </a>
          </div>
        </div>

        <!-- Buses Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Bus ID</th>
                <th>Plates Number</th>
                <th>Model</th>
                <th>Seats</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($all_buses->num_rows > 0): ?>
                <?php while ($bus = $all_buses->fetch_assoc()): ?>
                  <tr>
                    <td><strong>#<?php echo $bus['bus_id']; ?></strong></td>
                    <td>
                      <strong class="text-primary"><?php echo htmlspecialchars($bus['plates_number']); ?></strong>
                    </td>
                    <td><?php echo htmlspecialchars($bus['model']); ?></td>
                    <td><?php echo $bus['number_of_seats']; ?> seats</td>
                    <td>
                      <?php
                      $status_class = 'badge-secondary';
                      switch($bus['status']) {
                        case 'active': $status_class = 'badge-success'; break;
                        case 'maintenance': $status_class = 'badge-warning'; break;
                        case 'inactive': $status_class = 'badge-danger'; break;
                      }
                      ?>
                      <span class="badge <?php echo $status_class; ?>">
                        <?php echo ucfirst($bus['status']); ?>
                      </span>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($bus['created_at'])); ?></td>
                    <td>
                      <div class="action-buttons">
                        <a href="update_buses.php?id=<?php echo $bus['bus_id']; ?>" class="btn btn-sm btn-outline-warning">
                          <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_buses.php?id=<?php echo $bus['bus_id']; ?>" 
                          class="btn btn-sm btn-outline-danger" 
                          onclick="return confirm('Are you sure you want to delete this bus?')">
                          <i class="fas fa-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-bus fa-3x mb-3"></i>
                    <p>No buses found in the system.</p>
                    <a href="add_buses.php" class="btn btn-primary mt-2">
                      <i class="fas fa-plus me-2"></i>Add Your First Bus
                    </a>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Manage Routes Section -->
    <div id="manage-routes" class="dashboard-section hidden">
      <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0"><i class="fas fa-route me-2"></i>Route Management</h4>
          <div>
            <a href="add_routes.php" class="btn btn-success">
              <i class="fas fa-plus me-2"></i>Add New Route
            </a>
          </div>
        </div>

        <!-- Routes Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Route ID</th>
                <th>Departure</th>
                <th>Destination</th>
                <th>Departure Time</th>
                <th>Price/Seat</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($all_routes->num_rows > 0): ?>
                <?php while ($route = $all_routes->fetch_assoc()): ?>
                  <tr>
                    <td><strong>#<?php echo $route['route_id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($route['departure']); ?></td>
                    <td><?php echo htmlspecialchars($route['destination']); ?></td>
                    <td><?php echo date('H:i', strtotime($route['departure_time'])); ?></td>
                    <td>
                      <span class="fw-bold text-success"><?php echo number_format($route['price_per_seat']); ?> FRW</span>
                    </td>
                    <td>
                      <?php
                      $status_class = $route['status'] == 'active' ? 'badge-success' : 'badge-secondary';
                      ?>
                      <span class="badge <?php echo $status_class; ?>">
                        <?php echo ucfirst($route['status']); ?>
                      </span>
                    </td>
                    <td>
                      <div class="action-buttons">
                        <a href="update_routes.php?id=<?php echo $route['route_id']; ?>" class="btn btn-sm btn-outline-warning">
                          <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_routes.php?id=<?php echo $route['route_id']; ?>" 
                          class="btn btn-sm btn-outline-danger" 
                          onclick="return confirm('Are you sure you want to delete this route?')">
                          <i class="fas fa-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-route fa-3x mb-3"></i>
                    <p>No routes found in the system.</p>
                    <a href="add_routes.php" class="btn btn-primary mt-2">
                      <i class="fas fa-plus me-2"></i>Add Your First Route
                    </a>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Manage Trips Section -->
    <div id="manage-trips" class="dashboard-section hidden">
      <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0"><i class="fas fa-road me-2"></i>Trip Management</h4>
          <div>
            <a href="add_trips.php" class="btn btn-success">
              <i class="fas fa-plus me-2"></i>Add New Trip
            </a>
          </div>
        </div>

        <!-- Trips Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Trip ID</th>
                <th>Bus</th>
                <th>Driver</th>
                <th>Route</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Available Seats</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($all_trips->num_rows > 0): ?>
                <?php while ($trip = $all_trips->fetch_assoc()): ?>
                  <tr>
                    <td><strong>#<?php echo $trip['trip_id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($trip['plates_number']); ?></td>
                    <td><?php echo htmlspecialchars($trip['driver_name']); ?></td>
                    <td><?php echo htmlspecialchars($trip['departure'] . ' → ' . $trip['destination']); ?></td>
                    <td><?php echo date('M j, Y H:i', strtotime($trip['departure_datetime'])); ?></td>
                    <td><?php echo date('M j, Y H:i', strtotime($trip['estimated_arrival'])); ?></td>
                    <td><?php echo $trip['available_seats']; ?> seats</td>
                    <td>
                      <?php
                      $status_class = 'badge-secondary';
                      switch($trip['status']) {
                        case 'scheduled': $status_class = 'badge-info'; break;
                        case 'boarding': $status_class = 'badge-warning'; break;
                        case 'departed': $status_class = 'badge-primary'; break;
                        case 'arrived': $status_class = 'badge-success'; break;
                        case 'cancelled': $status_class = 'badge-danger'; break;
                      }
                      ?>
                      <span class="badge <?php echo $status_class; ?>">
                        <?php echo ucfirst($trip['status']); ?>
                      </span>
                    </td>
                    <td>
                      <div class="action-buttons">
                        <a href="update_trip.php?id=<?php echo $trip['trip_id']; ?>" class="btn btn-sm btn-outline-warning">
                          <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_trip.php?id=<?php echo $trip['trip_id']; ?>" 
                          class="btn btn-sm btn-outline-danger" 
                          onclick="return confirm('Are you sure you want to delete this trip?')">
                          <i class="fas fa-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="text-center py-5 text-muted">
                    <i class="fas fa-road fa-3x mb-3"></i>
                    <p>No trips found in the system.</p>
                    <a href="add_trips.php" class="btn btn-primary mt-2">
                      <i class="fas fa-plus me-2"></i>Add Your First Trip
                    </a>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Manage Drivers Section -->
    <div id="manage-drivers" class="dashboard-section hidden">
      <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0"><i class="fas fa-users me-2"></i>Driver Management</h4>
          <div>
            <a href="add_driver.php" class="btn btn-success">
              <i class="fas fa-plus me-2"></i>Add New Driver
            </a>
          </div>
        </div>

        <!-- Drivers Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Driver ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>License</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($all_drivers->num_rows > 0): ?>
                <?php while ($driver = $all_drivers->fetch_assoc()): ?>
                  <tr>
                    <td><strong>#<?php echo $driver['driver_id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($driver['name']); ?></td>
                    <td><?php echo htmlspecialchars($driver['contact']); ?></td>
                    <td><?php echo htmlspecialchars($driver['license']); ?></td>
                    <td>
                      <?php
                      $status_class = 'badge-secondary';
                      switch($driver['status']) {
                        case 'active': $status_class = 'badge-success'; break;
                        case 'on_leave': $status_class = 'badge-warning'; break;
                        case 'inactive': $status_class = 'badge-danger'; break;
                      }
                      ?>
                      <span class="badge <?php echo $status_class; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $driver['status'])); ?>
                      </span>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($driver['created_at'])); ?></td>
                    <td>
                      <div class="action-buttons">
                        <a href="update_drivers.php?id=<?php echo $driver['driver_id']; ?>" class="btn btn-sm btn-outline-warning">
                          <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_drivers.php?id=<?php echo $driver['driver_id']; ?>" 
                          class="btn btn-sm btn-outline-danger" 
                          onclick="return confirm('Are you sure you want to delete this driver?')">
                          <i class="fas fa-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <p>No drivers found in the system.</p>
                    <a href="add_drivers.php" class="btn btn-primary mt-2">
                      <i class="fas fa-plus me-2"></i>Add Your First Driver
                    </a>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Manage Bookings Section -->
    <div id="manage-bookings" class="dashboard-section hidden">
      <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Booking Management</h4>
          <div>
            <a href="add_booking.php" class="btn btn-success">
              <i class="fas fa-plus me-2"></i>Add New Booking
            </a>
          </div>
        </div>

        <!-- Bookings Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Customer</th>
                <th>Trip</th>
                <th>Seats</th>
                <th>Booking Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($recent_bookings->num_rows > 0): ?>
                <?php while ($booking = $recent_bookings->fetch_assoc()): ?>
                  <tr>
                    <td><strong>#BK<?php echo str_pad($booking['booking_id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                    <td><?php echo htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']); ?></td>
                    <td>
                      <?php echo htmlspecialchars($booking['departure'] . ' → ' . $booking['destination']); ?><br>
                      <small class="text-muted"><?php echo date('M j, Y H:i', strtotime($booking['departure_datetime'])); ?></small>
                    </td>
                    <td><?php echo $booking['number_of_seats']; ?></td>
                    <td><?php echo date('M j, Y H:i', strtotime($booking['booking_date'])); ?></td>
                    <td>
                      <div class="action-buttons">
                        <a href="update_booking.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-sm btn-outline-warning">
                          <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_booking.php?id=<?php echo $booking['booking_id']; ?>" 
                          class="btn btn-sm btn-outline-danger" 
                          onclick="return confirm('Are you sure you want to delete this booking?')">
                          <i class="fas fa-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                    <p>No bookings found in the system.</p>
                    <a href="add_booking.php" class="btn btn-primary mt-2">
                      <i class="fas fa-plus me-2"></i>Add Your First Booking
                    </a>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Manage Customers Section -->
    <div id="manage-customers" class="dashboard-section hidden">
      <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0"><i class="fas fa-user-friends me-2"></i>Customer Management</h4>
        </div>

        <!-- Customers Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Customer ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($all_customers->num_rows > 0): ?>
                <?php while ($customer = $all_customers->fetch_assoc()): ?>
                  <tr>
                    <td><strong>#<?php echo $customer['customer_id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($customer['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($customer['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($customer['contact']); ?></td>
                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                    <td><?php echo date('M j, Y', strtotime($customer['created_at'])); ?></td>
                    <td>
                      <div class="action-buttons">
                        <a href="update_customer.php?id=<?php echo $customer['customer_id']; ?>" class="btn btn-sm btn-outline-warning">
                          <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_customer.php?id=<?php echo $customer['customer_id']; ?>" 
                          class="btn btn-sm btn-outline-danger" 
                          onclick="return confirm('Are you sure you want to delete this customer?')">
                          <i class="fas fa-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-user-friends fa-3x mb-3"></i>
                    <p>No customers found in the system.</p>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Manage Users Section -->
    <div id="manage-users" class="dashboard-section hidden">
      <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0"><i class="fas fa-user-cog me-2"></i>User Management</h4>
          <div>
            <a href="register.php" class="btn btn-success">
              <i class="fas fa-plus me-2"></i>Add New User
            </a>
          </div>
        </div>
        
        <div class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          User management functionality - Display all system users with role-based access control.
        </div>
        
        <!-- Users table would go here -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Login</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                  <i class="fas fa-users fa-2x mb-3"></i>
                  <p>User management table - Implement user listing here</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Your existing JavaScript remains the same
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
      const sections = document.querySelectorAll('.dashboard-section');
      const pageTitle = document.getElementById('page-title');
      const pageSubtitle = document.getElementById('page-subtitle');

      const pageData = {
        'dashboard': {
          title: 'Dashboard Overview 📊',
          subtitle: 'Real-time system performance and analytics'
        },
        'manage-buses': {
          title: 'Bus Management 🚌',
          subtitle: 'Manage all buses, status, and maintenance'
        },
        'manage-routes': {
          title: 'Route Management 🗺️',
          subtitle: 'Manage bus routes, pricing, and schedules'
        },
        'manage-trips': {
          title: 'Trip Management 🚗',
          subtitle: 'Manage scheduled trips and assignments'
        },
        'manage-drivers': {
          title: 'Driver Management 👨‍💼',
          subtitle: 'Manage driver profiles and assignments'
        },
        'manage-bookings': {
          title: 'Booking Management 🎫',
          subtitle: 'View and manage all passenger bookings'
        },
        'manage-customers': {
          title: 'Customer Management 👥',
          subtitle: 'Manage customer information and history'
        },
        'manage-users': {
          title: 'User Management 🔐',
          subtitle: 'Manage system users and access control'
        }
      };

      function showSection(sectionId) {
        sections.forEach(section => {
          section.classList.add('hidden');
        });

        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
          targetSection.classList.remove('hidden');
        }

        if (pageData[sectionId]) {
          pageTitle.textContent = pageData[sectionId].title;
          pageSubtitle.textContent = pageData[sectionId].subtitle;
        }

        sidebarLinks.forEach(link => {
          link.classList.remove('active');
          if (link.getAttribute('data-section') === sectionId) {
            link.classList.add('active');
          }
        });

        const url = new URL(window.location);
        url.searchParams.set('section', sectionId);
        window.history.pushState({}, '', url);
      }

      window.showSection = showSection;

      const urlParams = new URLSearchParams(window.location.search);
      const sectionParam = urlParams.get('section');
      if (sectionParam && pageData[sectionParam]) {
        showSection(sectionParam);
      } else {
        showSection('dashboard');
      }

      sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const sectionId = this.getAttribute('data-section');
          if (sectionId) {
            showSection(sectionId);
          }
        });
      });

      window.addEventListener('popstate', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const sectionParam = urlParams.get('section');
        if (sectionParam && pageData[sectionParam]) {
          showSection(sectionParam);
        } else {
          showSection('dashboard');
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