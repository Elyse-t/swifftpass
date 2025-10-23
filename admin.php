<?php
session_start();
include('connection.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Auto-update trip status based on time and conditions
function updateTripStatusAutomatically($conn) {
    $current_time = date('Y-m-d H:i:s');
    
    // Update trips from 'available' to 'ontrip' when departure time is reached
    $update_to_ontrip = "UPDATE trips 
                          SET status = 'ontrip' 
                          WHERE departure_datetime <= '$current_time' 
                          AND status = 'available'";
    $conn->query($update_to_ontrip);
    
    // Update trips from 'ontrip' to 'arrived' when estimated arrival time is reached
    $update_to_arrived = "UPDATE trips 
                         SET status = 'arrived' 
                         WHERE estimated_arrival <= '$current_time' 
                         AND status = 'ontrip'";
    $conn->query($update_to_arrived);
    
    return true;
}

// Run the auto-update
updateTripStatusAutomatically($conn);

// ===== UPDATED BUS STATUS MANAGEMENT =====
// Function to automatically update bus status based on trips
function updateAutomaticBusStatus($conn) {
    // Update buses that are on active trips to 'inactive'
    $update_on_trip = "UPDATE buses b 
                      JOIN trips t ON b.bus_id = t.bus_id 
                      SET b.status = 'inactive' 
                      WHERE t.status IN ('scheduled', 'boarding', 'departed') 
                      AND b.status != 'maintenance'";
    $conn->query($update_on_trip);
    
    // Update buses with no active trips to 'active'
    $update_available = "UPDATE buses b 
                        LEFT JOIN trips t ON b.bus_id = t.bus_id AND t.status IN ('scheduled', 'boarding', 'departed')
                        SET b.status = 'active' 
                        WHERE t.trip_id IS NULL 
                        AND b.status NOT IN ('maintenance', 'inactive')";
    $conn->query($update_available);
    
    return true;
}

// Run automatic status updates on page load
updateAutomaticBusStatus($conn);

// Handle bus assignment for new trip
if (isset($_GET['assign_bus'])) {
    $bus_id = $conn->real_escape_string($_GET['assign_bus']);
    
    // Fetch bus details
    $bus_sql = "SELECT * FROM buses WHERE bus_id = '$bus_id'";
    $bus_result = $conn->query($bus_sql);
    
    if ($bus_result->num_rows > 0) {
        $bus_data = $bus_result->fetch_assoc();
        
        // Check if bus is already assigned to an active trip
        $check_trip_sql = "SELECT * FROM trips WHERE bus_id = '$bus_id' AND status IN ('scheduled', 'boarding', 'departed')";
        $trip_result = $conn->query($check_trip_sql);
        
        if ($trip_result->num_rows > 0) {
            $error_message = "Bus {$bus_data['plates_number']} is already assigned to an active trip!";
        } else {
            // Update bus status to 'inactive' when assigned
            $update_sql = "UPDATE buses SET status = 'inactive' WHERE bus_id = '$bus_id'";
            
            if ($conn->query($update_sql) === TRUE) {
                $success_message = "Bus {$bus_data['plates_number']} assigned successfully! Status changed to inactive to prevent duplicate assignments.";
            } else {
                $error_message = "Error assigning bus: " . $conn->error;
            }
        }
    } else {
        $error_message = "Bus not found!";
    }
}

// Handle bus release (make available again)
if (isset($_GET['release_bus'])) {
    $bus_id = $conn->real_escape_string($_GET['release_bus']);
    
    // Check if bus has any active trips
    $check_trip_sql = "SELECT * FROM trips WHERE bus_id = '$bus_id' AND status IN ('scheduled', 'boarding', 'departed')";
    $trip_result = $conn->query($check_trip_sql);
    
    if ($trip_result->num_rows > 0) {
        $error_message = "Cannot release bus! It is currently assigned to active trips.";
    } else {
        $update_sql = "UPDATE buses SET status = 'active' WHERE bus_id = '$bus_id'";
        
        if ($conn->query($update_sql) === TRUE) {
            $success_message = "Bus released and now available for new trips!";
        } else {
            $error_message = "Error releasing bus: " . $conn->error;
        }
    }
}

// Handle quick status update with validation
if (isset($_GET['update_status'])) {
    $bus_id = $conn->real_escape_string($_GET['update_status']);
    $new_status = $conn->real_escape_string($_GET['status']);
    
    // Check if bus has active trips when trying to set to active
    if ($new_status == 'active') {
        $check_trip_sql = "SELECT * FROM trips WHERE bus_id = '$bus_id' AND status IN ('scheduled', 'boarding', 'departed')";
        $trip_result = $conn->query($check_trip_sql);
        
        if ($trip_result->num_rows > 0) {
            $error_message = "Cannot set bus to active! It is currently assigned to active trips.";
        } else {
            $update_sql = "UPDATE buses SET status = '$new_status' WHERE bus_id = '$bus_id'";
            if ($conn->query($update_sql) === TRUE) {
                $success_message = "Bus status updated to {$new_status}!";
            } else {
                $error_message = "Error updating bus status: " . $conn->error;
            }
        }
    } else {
        // For other statuses (maintenance, inactive), allow directly
        $update_sql = "UPDATE buses SET status = '$new_status' WHERE bus_id = '$bus_id'";
        if ($conn->query($update_sql) === TRUE) {
            $success_message = "Bus status updated to {$new_status}!";
        } else {
            $error_message = "Error updating bus status: " . $conn->error;
        }
    }
}

// Handle auto-refresh status
if (isset($_GET['refresh_status'])) {
    if (updateAutomaticBusStatus($conn)) {
        $success_message = "Bus statuses automatically updated based on current trips!";
    } else {
        $error_message = "Error updating bus statuses automatically!";
    }
}

// Handle bus filtering and search
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Build buses query with filters using new schema - UPDATED TO REMOVE UNNECESSARY COLUMNS
$buses_query = "SELECT b.*, 
                       COUNT(t.trip_id) as active_trips
                FROM buses b 
                LEFT JOIN trips t ON b.bus_id = t.bus_id AND t.status IN ('scheduled', 'boarding', 'departed')
                WHERE 1=1";

// Apply status filter
if (!empty($status_filter)) {
    $status_filter = $conn->real_escape_string($status_filter);
    $buses_query .= " AND b.status = '$status_filter'";
}

// Apply search filter
if (!empty($search_term)) {
    $buses_query .= " AND (b.plates_number LIKE '%$search_term%' 
                          OR b.model LIKE '%$search_term%')";
}

$buses_query .= " GROUP BY b.bus_id
                  ORDER BY 
                    CASE 
                        WHEN b.status = 'active' THEN 1
                        WHEN b.status = 'inactive' THEN 2
                        WHEN b.status = 'maintenance' THEN 3
                        ELSE 4
                    END, b.created_at DESC";

$all_buses = $conn->query($buses_query);

// Get stats for dashboard using new schema
$total_buses = $conn->query("SELECT COUNT(*) as total FROM buses")->fetch_assoc()['total'];
$available_buses = $conn->query("SELECT COUNT(*) as total FROM buses WHERE status = 'active'")->fetch_assoc()['total'];
$inactive_buses = $conn->query("SELECT COUNT(*) as total FROM buses WHERE status = 'inactive'")->fetch_assoc()['total'];
$maintenance_buses = $conn->query("SELECT COUNT(*) as total FROM buses WHERE status = 'maintenance'")->fetch_assoc()['total'];

// Count buses on active trips
$on_trip_buses = $conn->query("SELECT COUNT(DISTINCT b.bus_id) as total 
                              FROM buses b 
                              JOIN trips t ON b.bus_id = t.bus_id 
                              WHERE t.status IN ('scheduled', 'boarding', 'departed')")->fetch_assoc()['total'];
// ===== END UPDATED BUS STATUS MANAGEMENT =====

// ... rest of your existing code for other statistics ...

// Fetch other statistics using new database tables
$total_routes = $conn->query("SELECT COUNT(*) as count FROM routes")->fetch_assoc()['count'];
$total_drivers = $conn->query("SELECT COUNT(*) as count FROM drivers")->fetch_assoc()['count'];
$total_customers = $conn->query("SELECT COUNT(*) as count FROM customers")->fetch_assoc()['count'];
$total_trips = $conn->query("SELECT COUNT(*) as count FROM trips")->fetch_assoc()['count'];

// Today's bookings and revenue using new schema
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

// Fetch data for other sections using new schema
$all_routes = $conn->query("SELECT * FROM routes ORDER BY created_at DESC");
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
$all_bookings = $conn->query("
    SELECT b.*, c.firstname, c.lastname, c.contact, c.email,
           t.departure_datetime, bus.plates_number,
           r.departure, r.destination, r.price_per_seat
    FROM bookings b
    JOIN customers c ON b.customer_id = c.customer_id
    JOIN trips t ON b.trip_id = t.trip_id
    JOIN buses bus ON t.bus_id = bus.bus_id
    JOIN routes r ON t.route_id = r.route_id
    ORDER BY b.booking_date DESC
    LIMIT 50
");
$all_drivers = $conn->query("SELECT * FROM drivers ORDER BY created_at DESC");
$all_customers = $conn->query("SELECT * FROM customers ORDER BY created_at DESC LIMIT 50");
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
// Handle route filtering and search
$route_status_filter = isset($_GET['route_status_filter']) ? $_GET['route_status_filter'] : '';
$route_search_term = isset($_GET['route_search']) ? $_GET['route_search'] : '';

// Build routes query with filters
$routes_query = "SELECT * FROM routes WHERE 1=1";

// Apply status filter
if (!empty($route_status_filter)) {
    $route_status_filter = $conn->real_escape_string($route_status_filter);
    $routes_query .= " AND status = '$route_status_filter'";
}

// Apply search filter
if (!empty($route_search_term)) {
    $route_search_term = $conn->real_escape_string($route_search_term);
    $routes_query .= " AND (departure LIKE '%$route_search_term%' 
                          OR destination LIKE '%$route_search_term%'
                          OR price_per_seat LIKE '%$route_search_term%')";
}

$routes_query .= " ORDER BY created_at DESC";
$all_routes = $conn->query($routes_query);

// Get route stats for the dashboard
$total_routes = $conn->query("SELECT COUNT(*) as total FROM routes")->fetch_assoc()['total'];
$active_routes = $conn->query("SELECT COUNT(*) as total FROM routes WHERE status = 'active'")->fetch_assoc()['total'];
$inactive_routes = $conn->query("SELECT COUNT(*) as total FROM routes WHERE status = 'inactive'")->fetch_assoc()['total'];


// Handle trip filtering and search
$trip_status_filter = isset($_GET['trip_status_filter']) ? $_GET['trip_status_filter'] : '';
$trip_search_term = isset($_GET['trip_search']) ? $_GET['trip_search'] : '';

// Build trips query with filters
$trips_query = "SELECT t.*, b.plates_number, d.name as driver_name, 
                       r.departure, r.destination, r.price_per_seat
                FROM trips t
                JOIN buses b ON t.bus_id = b.bus_id
                JOIN drivers d ON t.driver_id = d.driver_id
                JOIN routes r ON t.route_id = r.route_id
                WHERE 1=1";

// Apply status filter
if (!empty($trip_status_filter)) {
    $trip_status_filter = $conn->real_escape_string($trip_status_filter);
    $trips_query .= " AND t.status = '$trip_status_filter'";
}

// Apply search filter
if (!empty($trip_search_term)) {
    $trip_search_term = $conn->real_escape_string($trip_search_term);
    $trips_query .= " AND (b.plates_number LIKE '%$trip_search_term%' 
                          OR d.name LIKE '%$trip_search_term%'
                          OR r.departure LIKE '%$trip_search_term%'
                          OR r.destination LIKE '%$trip_search_term%')";
}

$trips_query .= " ORDER BY t.departure_datetime DESC LIMIT 50";
$all_trips = $conn->query($trips_query);

// Get trip stats with new status values
$total_trips = $conn->query("SELECT COUNT(*) as total FROM trips")->fetch_assoc()['total'];
$available_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE status = 'available'")->fetch_assoc()['total'];
$ontrip_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE status = 'ontrip'")->fetch_assoc()['total'];
$arrived_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE status = 'arrived'")->fetch_assoc()['total'];
$maintenance_trips = $conn->query("SELECT COUNT(*) as total FROM trips WHERE status = 'maintenance'")->fetch_assoc()['total'];

// REPORTS SECTION PHP CODE
// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$route_filter = isset($_GET['route_filter']) ? $_GET['route_filter'] : '';

// Build query for trip reports
$report_query = "SELECT 
    t.trip_id,
    t.departure_datetime,
    t.estimated_arrival,
    t.status,
    t.available_seats,
    b.plates_number,
    d.name as driver_name,
    r.departure,
    r.destination,
    r.price_per_seat,
    COUNT(bk.booking_id) as total_bookings,
    SUM(bk.number_of_seats) as total_seats_sold,
    SUM(bk.number_of_seats * r.price_per_seat) as total_revenue
FROM trips t
LEFT JOIN buses b ON t.bus_id = b.bus_id
LEFT JOIN drivers d ON t.driver_id = d.driver_id
LEFT JOIN routes r ON t.route_id = r.route_id
LEFT JOIN bookings bk ON t.trip_id = bk.trip_id
WHERE DATE(t.departure_datetime) BETWEEN '$start_date' AND '$end_date'";

if (!empty($route_filter)) {
    $report_query .= " AND t.route_id = '$route_filter'";
}

$report_query .= " GROUP BY t.trip_id
                  ORDER BY t.departure_datetime DESC";

$trip_reports = $conn->query($report_query);

// Calculate summary statistics
$summary_query = "SELECT 
    COUNT(DISTINCT t.trip_id) as total_trips,
    SUM(bk.number_of_seats) as total_passengers,
    SUM(bk.number_of_seats * r.price_per_seat) as total_revenue,
    AVG(bk.number_of_seats) as avg_passengers_per_trip
FROM trips t
LEFT JOIN bookings bk ON t.trip_id = bk.trip_id
LEFT JOIN routes r ON t.route_id = r.route_id
WHERE DATE(t.departure_datetime) BETWEEN '$start_date' AND '$end_date'";

if (!empty($route_filter)) {
    $summary_query .= " AND t.route_id = '$route_filter'";
}

$summary_result = $conn->query($summary_query);
$summary = $summary_result->fetch_assoc();

// Revenue by route
$revenue_by_route = $conn->query("
    SELECT r.departure, r.destination, 
           SUM(bk.number_of_seats * r.price_per_seat) as route_revenue,
           COUNT(DISTINCT t.trip_id) as trips_count
    FROM routes r
    LEFT JOIN trips t ON r.route_id = t.route_id
    LEFT JOIN bookings bk ON t.trip_id = bk.trip_id
    WHERE DATE(t.departure_datetime) BETWEEN '$start_date' AND '$end_date'
    GROUP BY r.route_id
    ORDER BY route_revenue DESC
");

// Performance stats
$performance_stats = $conn->query("
    SELECT 
        COUNT(*) as total_trips,
        SUM(CASE WHEN status = 'arrived' THEN 1 ELSE 0 END) as completed_trips,
        SUM(CASE WHEN status = 'ontrip' THEN 1 ELSE 0 END) as ongoing_trips,
        SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as upcoming_trips
    FROM trips 
    WHERE DATE(departure_datetime) BETWEEN '$start_date' AND '$end_date'
")->fetch_assoc();

$completion_rate = $performance_stats['total_trips'] > 0 ? 
    round(($performance_stats['completed_trips'] / $performance_stats['total_trips']) * 100, 1) : 0;
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
    /* YOUR EXACT CSS STYLES - NO CHANGES */
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
    
    /* Sidebar Styling */
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
    
    .sidebar-brand {
      padding: 2rem 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      margin: -1px -1px 0 -1px;
    }
    
    .sidebar-brand h4 {
      margin: 0;
      font-weight: 700;
      display: flex;
      align-items: center;
      color: white;
    }
    
    .sidebar-brand i {
      margin-right: 12px;
      font-size: 1.8rem;
      color: var(--success);
    }
    
    .nav-container {
      padding: 1rem 0;
    }
    
    .sidebar .nav-link {
      color: var(--primary);
      padding: 1rem 1.5rem;
      margin: 0.3rem 1rem;
      border-radius: 12px;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      cursor: pointer;
      font-weight: 500;
      border: none;
    }
    
    .sidebar .nav-link:hover {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: white;
      transform: translateX(5px);
      box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }
    
    .sidebar .nav-link.active {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: white;
      box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
    }
    
    .sidebar .nav-link i {
      margin-right: 12px;
      width: 20px;
      text-align: center;
      font-size: 1.1rem;
    }
    
    /* Main Content */
    .main-content {
      margin-left: var(--sidebar-width);
      padding: 2rem;
      transition: all 0.3s;
      min-height: 100vh;
    }
    
    /* Header */
    .header {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 1.5rem 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      backdrop-filter: blur(10px);
    }
    
    .header h2 {
      margin: 0;
      color: var(--primary);
      font-weight: 700;
      font-size: 1.8rem;
    }
    
    .header p {
      margin: 0.5rem 0 0 0;
      color: #6c757d;
      font-size: 1rem;
    }
    
    .user-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .user-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 1.2rem;
      border: 3px solid var(--success);
    }
    
    /* Cards */
    .stat-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      border: none;
      height: 100%;
      backdrop-filter: blur(10px);
      border-left: 5px solid var(--secondary);
    }
    
    .stat-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .stat-card h5 {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 600;
    }
    
    .stat-card .number {
      font-size: 2.5rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      color: var(--primary);
      line-height: 1;
    }
    
    .stat-card .trend {
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .trend.up {
      color: var(--success);
      font-weight: 600;
    }
    
    .trend.down {
      color: var(--danger);
      font-weight: 600;
    }
    
    /* Table Containers */
    .table-container {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      margin-top: 2rem;
      backdrop-filter: blur(10px);
    }
    
    .table-container h4, .table-container h5 {
      color: var(--primary);
      font-weight: 700;
      margin-bottom: 1.5rem;
    }
    
    .table thead th {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      border: none;
      padding: 1.2rem 1rem;
      font-weight: 600;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    
    .table tbody td {
      padding: 1.2rem 1rem;
      vertical-align: middle;
      border-color: #e9ecef;
      color: var(--primary);
      font-weight: 500;
    }
    
    .table tbody tr:hover {
      background-color: rgba(52, 152, 219, 0.05);
      transform: scale(1.01);
      transition: all 0.2s ease;
    }
    
    /* Badges */
    .badge {
      padding: 0.6rem 1rem;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.8rem;
    }
    
    .badge-success { background: linear-gradient(135deg, var(--success), #27ae60) !important; }
    .badge-warning { background: linear-gradient(135deg, var(--warning), #e67e22) !important; }
    .badge-danger { background: linear-gradient(135deg, var(--danger), #c0392b) !important; }
    .badge-info { background: linear-gradient(135deg, var(--info), #16a085) !important; }
    .badge-secondary { background: linear-gradient(135deg, #95a5a6, #7f8c8d) !important; }
    
    /* Buttons */
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
      box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
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
      box-shadow: 0 8px 20px rgba(46, 204, 113, 0.4);
    }
    
    /* ===== SELECT_BUSES.PHP STYLES ===== */
    .status-badge {
      padding: 0.5rem 1rem;
      border-radius: 50px;
      font-size: 0.8rem;
      font-weight: 600;
      display: inline-block;
    }
    
    .badge-available {
      background: linear-gradient(135deg, var(--success), #27ae60);
      color: white;
    }
    
    .badge-ontrip {
      background: linear-gradient(135deg, var(--warning), #e67e22);
      color: white;
    }
    
    .badge-maintenance {
      background: linear-gradient(135deg, var(--danger), #c0392b);
      color: white;
    }
    
    .badge-full {
      background: linear-gradient(135deg, #95a5a6, #7f8c8d);
      color: white;
    }
    
    .badge-inactive {
      background: linear-gradient(135deg, #7f8c8d, #95a5a6);
      color: white;
    }
    
    .seats-indicator {
      height: 8px;
      border-radius: 4px;
      background: #e9ecef;
      overflow: hidden;
      margin-top: 0.5rem;
    }
    
    .seats-filled {
      height: 100%;
      border-radius: 4px;
      transition: all 0.3s ease;
    }
    
    .seats-low { background: linear-gradient(135deg, var(--success), #27ae60); }
    .seats-medium { background: linear-gradient(135deg, var(--warning), #e67e22); }
    .seats-high { background: linear-gradient(135deg, var(--danger), #c0392b); }
    
    .bus-card {
      transition: all 0.3s ease;
      border-left: 4px solid transparent;
    }
    
    .bus-card-available { border-left-color: var(--success); }
    .bus-card-ontrip { border-left-color: var(--warning); }
    .bus-card-maintenance { border-left-color: var(--danger); }
    .bus-card-full { border-left-color: #95a5a6; }
    .bus-card-inactive { border-left-color: #7f8c8d; }
    
    .quick-actions {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }
    
    .stats-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      transition: all 0.3s ease;
    }
    
    .stats-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    
    .stat-number {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    /* ===== END SELECT_BUSES.PHP STYLES ===== */

    /* Filter and Search Section */
    .filter-section {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }
    
    .search-box {
      position: relative;
    }
    
    .search-box i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }
    
    .search-box input {
      padding-left: 45px;
      border-radius: 10px;
      border: 2px solid #e9ecef;
      transition: all 0.3s ease;
    }
    
    .search-box input:focus {
      border-color: var(--secondary);
      box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    
    .filter-buttons .btn {
      border-radius: 10px;
      padding: 0.5rem 1.5rem;
      font-weight: 500;
      margin-right: 0.5rem;
      margin-bottom: 0.5rem;
      transition: all 0.3s ease;
    }
    
    .filter-buttons .btn.active {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: white;
      border: none;
      box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    }
    
    /* Section Management */
    .dashboard-section {
      display: block;
      animation: fadeIn 0.5s ease;
    }
    
    .dashboard-section.hidden {
      display: none;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    /* Action Buttons */
    .action-buttons {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }
    
    /* No Data States */
    .no-data {
      text-align: center;
      padding: 3rem;
      color: #6c757d;
    }
    
    .no-data i {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: #bdc3c7;
    }
    
    /* Auto Status Update Indicator */
    .auto-update-indicator {
      background: linear-gradient(135deg, var(--info), #16a085);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      font-size: 0.8rem;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        width: 80px;
      }
      
      .sidebar-brand h4 span, .sidebar .nav-link span {
        display: none;
      }
      
      .sidebar .nav-link i {
        margin-right: 0;
        font-size: 1.3rem;
      }
      
      .sidebar .nav-link {
        padding: 1rem;
        justify-content: center;
      }
      
      .main-content {
        margin-left: 80px;
        padding: 1rem;
      }
      
      .header {
        padding: 1rem;
      }
      
      .stat-card {
        padding: 1.5rem;
      }
      
      .stat-card .number {
        font-size: 2rem;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .filter-section .row {
        flex-direction: column;
      }
      
      .filter-section .col-md-6 {
        margin-bottom: 1rem;
      }
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 6px;
    }
    
    ::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.1);
    }
    
    ::-webkit-scrollbar-thumb {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      border-radius: 3px;
    }
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
            <span>Fleet</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-routes">
            <i class="fas fa-route"></i>
            <span>Routes</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-trips">
            <i class="fas fa-road"></i>
            <span>Trips</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-bookings">
            <i class="fas fa-ticket-alt"></i>
            <span>Bookings</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-drivers">
            <i class="fas fa-users"></i>
            <span>Drivers</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-customers">
            <i class="fas fa-user-friends"></i>
            <span>Customers</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-section="manage-users">
            <i class="fas fa-user-cog"></i>
            <span>Users</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="payments.php" class="nav-link">
            <i class="fas fa-credit-card"></i>
            <span>Payments</span>
          </a>
        </li>

        <li class="nav-item">
  <a href="#" class="nav-link" data-section="reports">
    <i class="fas fa-chart-bar"></i>
    <span>Reports</span>
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
        <h2 id="page-title">Welcome Admin 👨‍💻</h2>
        <p id="page-subtitle">Overview of system performance and key actions</p>
      </div>
      <div class="user-info">
        <div class="text-end">
          <div class="fw-bold text-dark">Admin Swift</div>
          <small class="text-muted">Administrator</small>
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
              <i class="fas fa-arrow-up"></i> Today's total
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <h5><i class="fas fa-chart-line me-2"></i>TODAY'S REVENUE</h5>
            <p class="number"><?php echo number_format($today_revenue); ?> FRW</p>
            <div class="trend up">
              <i class="fas fa-arrow-up"></i> Revenue generated
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <h5><i class="fas fa-check-circle me-2"></i>AVAILABLE BUSES</h5>
            <p class="number"><?php echo $available_buses; ?></p>
            <div class="trend up">
              <i class="fas fa-arrow-up"></i> Ready for trips
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <h5><i class="fas fa-road me-2"></i>ON TRIP BUSES</h5>
            <p class="number"><?php echo $on_trip_buses; ?></p>
            <div class="trend up">
              <i class="fas fa-arrow-up"></i> Currently active
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Stats Row -->
      <div class="row g-4 mt-2">
        <div class="col-xl-2 col-md-4">
          <div class="stat-card">
            <h5><i class="fas fa-route me-2"></i>TOTAL ROUTES</h5>
            <p class="number"><?php echo $total_routes; ?></p>
            <div class="trend up">
              <i class="fas fa-map"></i> Active routes
            </div>
          </div>
        </div>

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
              <i class="fas fa-users"></i> Registered
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-md-4">
          <div class="stat-card">
            <h5><i class="fas fa-road me-2"></i>TRIPS</h5>
            <p class="number"><?php echo $total_trips; ?></p>
            <div class="trend up">
              <i class="fas fa-route"></i> Scheduled
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-md-4">
          <div class="stat-card">
            <h5><i class="fas fa-tools me-2"></i>MAINTENANCE</h5>
            <p class="number"><?php echo $maintenance_buses; ?></p>
            <div class="trend down">
              <i class="fas fa-wrench"></i> Under repair
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-md-4">
          <div class="stat-card">
            <h5><i class="fas fa-ban me-2"></i>INACTIVE</h5>
            <p class="number"><?php echo $inactive_buses; ?></p>
            <div class="trend down">
              <i class="fas fa-times-circle"></i> Not available
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

      <!-- Fleet Section - UPDATED WITH CORRECTED LOGIC -->
    <div id="manage-buses" class="dashboard-section hidden">
      <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0"><i class="fas fa-bus me-2"></i>Fleet Management</h4>
          <div>
            <a href="add_buses.php" class="btn btn-success">
              <i class="fas fa-plus me-2"></i>Add New Bus
            </a>
            <a href="?section=manage-buses&refresh_status=1" class="btn btn-info ms-2">
              <i class="fas fa-sync-alt me-2"></i>Refresh Status
            </a>
          </div>
        </div>

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

        <!-- Auto Status Update Indicator -->
        <div class="auto-update-indicator">
          <i class="fas fa-robot"></i>
          <span>Bus status automatically updates based on trip assignments</span>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="stats-card text-center">
              <div class="stat-number text-primary"><?php echo $total_buses; ?></div>
              <p class="mb-0 text-muted">Total Buses</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card text-center">
              <div class="stat-number text-success"><?php echo $available_buses; ?></div>
              <p class="mb-0 text-muted">Active Buses</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card text-center">
              <div class="stat-number text-warning"><?php echo $inactive_buses; ?></div>
              <p class="mb-0 text-muted">Inactive Buses</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card text-center">
              <div class="stat-number text-danger"><?php echo $maintenance_buses; ?></div>
              <p class="mb-0 text-muted">Maintenance</p>
            </div>
          </div>
        </div>

        <!-- Filter and Search Section -->
        <div class="filter-section">
          <form method="GET" action="" id="busFilterForm">
            <input type="hidden" name="section" value="manage-buses">
            <div class="row align-items-center">
              <div class="col-md-6 mb-3 mb-md-0">
                <h6 class="text-dark mb-3"><i class="fas fa-filter me-2"></i>Status Filter</h6>
                <select name="status_filter" class="form-select" onchange="this.form.submit()">
                  <option value="">All Statuses</option>
                  <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                  <option value="maintenance" <?php echo $status_filter == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                  <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
              </div>
              <div class="col-md-6">
                <h6 class="text-dark mb-3"><i class="fas fa-search me-2"></i>Search Fleet</h6>
                <div class="search-box">
                  <i class="fas fa-search"></i>
                  <input type="text" name="search" class="form-control" placeholder="Search by plates number or model..." value="<?php echo htmlspecialchars($search_term); ?>">
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Fleet Table with Corrected Logic -->
        <div class="table-responsive">
          <?php if ($all_buses->num_rows > 0): ?>
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Bus ID</th>
                  <th>Plates Number</th>
                  <th>Model</th>
                  <th>Seats</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while($bus = $all_buses->fetch_assoc()): 
                  // Determine status and styling based on actual database status
                  $status_class = '';
                  $status_icon = '';
                  $display_status = ucfirst($bus['status']); // Display the actual status from database
                  
                  switch($bus['status']) {
                    case 'Active': 
                      $status_class = 'badge-available';
                      $status_icon = 'fa-check-circle';
                      break;
                    case 'Maintenance': 
                      $status_class = 'badge-maintenance';
                      $status_icon = 'fa-tools';
                      break;
                    case 'Inactive': 
                      $status_class = 'badge-inactive';
                      $status_icon = 'fa-clock';
                      break;
                    default: 
                      $display_status = 'Active';
                      $status_class = 'badge-available';
                      $status_icon = 'fa-question-circle';
                  }
                ?>
                <tr>
                  <td>
                    <strong class="text-primary">#<?php echo $bus['bus_id']; ?></strong>
                  </td>
                  <td>
                    <strong class="text-primary"><?php echo htmlspecialchars($bus['plates_number']); ?></strong>
                  </td>
                  <td><?php echo htmlspecialchars($bus['model']); ?></td>
                  <td>
                    <span class="fw-bold"><?php echo $bus['number_of_seats']; ?> seats</span>
                  </td>
                  <td>
                    <span class="status-badge <?php echo $status_class; ?>">
                      <i class="fas <?php echo $status_icon; ?> me-1"></i>
                      <?php echo $display_status; ?>
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <!-- Only Edit Button as requested -->
                      <a href="update_buses.php?bus_id=<?php echo $bus['bus_id']; ?>" 
                        class="btn btn-outline-warning btn-sm"
                        title="Edit Bus">
                        <i class="fas fa-edit"></i> Edit
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="no-data">
              <i class="fas fa-bus fa-3x mb-3"></i>
              <h4>No Buses Found</h4>
              <p>You haven't added any buses yet or no buses match your search criteria.</p>
              <a href="add_buses.php" class="btn btn-primary mt-2">
                <i class="fas fa-plus me-2"></i>Add Your First Bus
              </a>
            </div>
          <?php endif; ?>
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

        <!-- Route Stats Cards -->
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="stats-card text-center">
              <div class="stat-number text-primary"><?php echo $total_routes; ?></div>
              <p class="mb-0 text-muted">Total Routes</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stats-card text-center">
              <div class="stat-number text-success"><?php echo $active_routes; ?></div>
              <p class="mb-0 text-muted">Active Routes</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stats-card text-center">
              <div class="stat-number text-warning"><?php echo $inactive_routes; ?></div>
              <p class="mb-0 text-muted">Inactive Routes</p>
            </div>
          </div>
        </div>

        <!-- Filter and Search Section for Routes -->
        <div class="filter-section">
          <form method="GET" action="" id="routeFilterForm">
            <input type="hidden" name="section" value="manage-routes">
            <div class="row align-items-center">
              <div class="col-md-6 mb-3 mb-md-0">
                <h6 class="text-dark mb-3"><i class="fas fa-filter me-2"></i>Status Filter</h6>
                <select name="route_status_filter" class="form-select" onchange="this.form.submit()">
                  <option value="">All Statuses</option>
                  <option value="active" <?php echo $route_status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                  <option value="inactive" <?php echo $route_status_filter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
              </div>
              <div class="col-md-6">
                <h6 class="text-dark mb-3"><i class="fas fa-search me-2"></i>Search Routes</h6>
                <div class="search-box">
                  <i class="fas fa-search"></i>
                  <input type="text" name="route_search" class="form-control" placeholder="Search by departure, destination, or price..." value="<?php echo htmlspecialchars($route_search_term); ?>">
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Routes Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Route ID</th>
                <th>Departure</th>
                <th>Destination</th>
                <th>Delay Time</th>
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
                    <td>
                      <?php if (!empty($route['delay_time'])): ?>
                        <span class="text-warning"><?php echo $route['delay_time']; ?>hr</span>
                      <?php else: ?>
                        <span class="text-muted">On time</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="fw-bold text-success"><?php echo number_format($route['price_per_seat']); ?> FRW</span>
                    </td>
                    <td>
                      <?php
                      $status_class = 'badge-secondary';
                      switch($route['status']) {
                        case 'active': 
                          $status_class = 'badge-success';
                          break;
                        case 'inactive': 
                          $status_class = 'badge-warning';
                          break;
                      }
                      ?>
                      <span class="badge <?php echo $status_class; ?>">
                        <?php echo ucfirst($route['status']); ?>
                      </span>
                    </td>
                    <td>
                      <div class="action-buttons">
                        <!-- Only Edit button - Delete button removed as requested -->
                        <a href="update_routes.php?route_id=<?php echo $route['route_id']; ?>" class="btn btn-sm btn-outline-warning">
                          <i class="fas fa-edit"></i> Edit
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">
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

        <!-- Trip Stats Cards -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="stats-card text-center">
              <div class="stat-number text-primary"><?php echo $total_trips; ?></div>
              <p class="mb-0 text-muted">Total Trips</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card text-center">
              <div class="stat-number text-success"><?php echo $available_trips; ?></div>
              <p class="mb-0 text-muted">Available</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card text-center">
              <div class="stat-number text-warning"><?php echo $ontrip_trips; ?></div>
              <p class="mb-0 text-muted">On Trip</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stats-card text-center">
              <div class="stat-number text-info"><?php echo $arrived_trips; ?></div>
              <p class="mb-0 text-muted">Arrived</p>
            </div>
          </div>
        </div>

        <!-- Filter and Search Section for Trips -->
        <div class="filter-section">
          <form method="GET" action="" id="tripFilterForm">
            <input type="hidden" name="section" value="manage-trips">
            <div class="row align-items-center">
              <div class="col-md-6 mb-3 mb-md-0">
                <h6 class="text-dark mb-3"><i class="fas fa-filter me-2"></i>Status Filter</h6>
                <select name="trip_status_filter" class="form-select" onchange="this.form.submit()">
                  <option value="">All Statuses</option>
                  <option value="available" <?php echo $trip_status_filter == 'available' ? 'selected' : ''; ?>>Available</option>
                  <option value="ontrip" <?php echo $trip_status_filter == 'ontrip' ? 'selected' : ''; ?>>On Trip</option>
                  <option value="arrived" <?php echo $trip_status_filter == 'arrived' ? 'selected' : ''; ?>>Arrived</option>
                  <option value="maintenance" <?php echo $trip_status_filter == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                </select>
              </div>
              <div class="col-md-6">
                <h6 class="text-dark mb-3"><i class="fas fa-search me-2"></i>Search Trips</h6>
                <div class="search-box">
                  <i class="fas fa-search"></i>
                  <input type="text" name="trip_search" class="form-control" placeholder="Search by bus plates, driver, departure, destination..." value="<?php echo htmlspecialchars($trip_search_term); ?>">
                </div>
              </div>
            </div>
          </form>
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
            
     <!-- STATUS COLUMN - UPDATED WITH CORRECT STATUS VALUES AND VISIBLE COLORS -->
<td>
  <?php
  $status_class = '';
  $status_icon = 'fa-question-circle';
  
  switch($trip['status']) {
    case 'available': 
      $status_class = 'bg-success text-white'; 
      $status_icon = 'fa-check-circle';
      break;
    case 'ontrip': 
      $status_class = 'bg-primary text-white'; 
      $status_icon = 'fa-road';
      break;
    case 'arrived': 
      $status_class = 'bg-info text-white'; 
      $status_icon = 'fa-flag-checkered';
      break;
    case 'maintenance': 
      $status_class = 'bg-warning text-dark'; 
      $status_icon = 'fa-tools';
      break;
  }
  ?>
  <span class="badge <?php echo $status_class; ?> px-3 py-2" style="font-size: 0.85rem;">
    <i class="fas <?php echo $status_icon; ?> me-1"></i>
    <?php echo ucfirst($trip['status']); ?>
  </span>
</td>

<td>
  <div class="action-buttons">
    <!-- Edit button for all trips -->
    <a href="update_trips.php?id=<?php echo $trip['trip_id']; ?>" class="btn btn-sm btn-outline-warning" title="Edit Trip">
      <i class="fas fa-edit"></i>
    </a>
    
    
    
    
    <!-- Reset to Available button for arrived trips -->
    <?php if ($trip['status'] == 'arrived'): ?>
      <a href="update_trips.php?id=<?php echo $trip['trip_id']; ?>&status=available" 
         class="btn btn-sm btn-success" 
         title="Reset to Available"
         onclick="return confirm('Reset this trip to available status?')">
        <i class="fas fa-redo me-1"></i>Reset
      </a>
    <?php endif; ?>
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

        <!-- Bookings Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>Trip</th>
                <th>Seats</th>
                <th>Amount</th>
                <th>Booking Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($all_bookings->num_rows > 0): ?>
                <?php while ($booking = $all_bookings->fetch_assoc()): ?>
                  <tr>
                    <td><strong>#BK<?php echo str_pad($booking['booking_id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                    <td><?php echo htmlspecialchars($booking['firstname'] . ' ' . $booking['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($booking['contact']); ?></td>
                    <td>
                      <?php echo htmlspecialchars($booking['departure'] . ' → ' . $booking['destination']); ?><br>
                      <small class="text-muted"><?php echo date('M j, Y H:i', strtotime($booking['departure_datetime'])); ?></small>
                    </td>
                    <td><?php echo $booking['number_of_seats']; ?></td>
                    <td><strong><?php echo number_format($booking['number_of_seats'] * $booking['price_per_seat']); ?> FRW</strong></td>
                    <td><?php echo date('M j, Y H:i', strtotime($booking['booking_date'])); ?></td>
                   
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">
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

    <!-- Manage Drivers Section -->
    <div id="manage-drivers" class="dashboard-section hidden">
      <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0"><i class="fas fa-users me-2"></i>Driver Management</h4>
          <div>
            <a href="add_drivers.php" class="btn btn-success">
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
                        <a href="update_driver.php?id=<?php echo $driver['driver_id']; ?>" class="btn btn-sm btn-outline-warning">
                          <i class="fas fa-edit"></i>
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
                    <a href="add_driver.php" class="btn btn-primary mt-2">
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
            <a href="add_user.php" class="btn btn-success">
              <i class="fas fa-plus me-2"></i>Add New User
            </a>
          </div>
        </div>
        
        <div class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          User management functionality - Manage system users and access control.
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
<!-- Reports Section -->
<div id="reports" class="dashboard-section hidden">
  <div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Trip Reports & Analytics</h4>
      <div class="action-buttons">
        <button class="btn btn-success" onclick="generatePDF()">
          <i class="fas fa-file-pdf me-2"></i>Download PDF
        </button>
        <button class="btn btn-primary" onclick="window.print()">
          <i class="fas fa-print me-2"></i>Print Report
        </button>
        <button class="btn btn-info" onclick="exportToExcel()">
          <i class="fas fa-file-excel me-2"></i>Export Excel
        </button>
      </div>
    </div>

    <!-- Report Filters -->
    <div class="filter-section">
      <form method="GET" action="" id="reportFilterForm">
        <input type="hidden" name="section" value="reports">
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label text-dark fw-bold">Date Range</label>
            <input type="date" name="start_date" class="form-control" 
                   value="<?php echo $start_date; ?>">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label text-dark fw-bold">To Date</label>
            <input type="date" name="end_date" class="form-control" 
                   value="<?php echo $end_date; ?>">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label text-dark fw-bold">Route</label>
            <select name="route_filter" class="form-select">
              <option value="">All Routes</option>
              <?php
              $routes = $conn->query("SELECT * FROM routes ORDER BY departure, destination");
              while($route = $routes->fetch_assoc()): 
                $selected = ($route_filter == $route['route_id']) ? 'selected' : '';
              ?>
                <option value="<?php echo $route['route_id']; ?>" <?php echo $selected; ?>>
                  <?php echo htmlspecialchars($route['departure'] . ' → ' . $route['destination']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-3 mb-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
              <i class="fas fa-filter me-2"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stats-card text-center">
          <div class="stat-number text-primary"><?php echo $summary['total_trips'] ?? 0; ?></div>
          <p class="mb-0 text-muted">Total Trips</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card text-center">
          <div class="stat-number text-success"><?php echo $summary['total_passengers'] ?? 0; ?></div>
          <p class="mb-0 text-muted">Total Passengers</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card text-center">
          <div class="stat-number text-info"><?php echo number_format($summary['avg_passengers_per_trip'] ?? 0, 1); ?></div>
          <p class="mb-0 text-muted">Avg Passengers/Trip</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card text-center">
          <div class="stat-number text-warning"><?php echo number_format($summary['total_revenue'] ?? 0); ?> FRW</div>
          <p class="mb-0 text-muted">Total Revenue</p>
        </div>
      </div>
    </div>

    <!-- Detailed Reports Table -->
    <div class="table-responsive" id="reportTable">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Trip ID</th>
            <th>Route</th>
            <th>Bus</th>
            <th>Driver</th>
            <th>Departure</th>
            <th>Arrival</th>
            <th>Passengers</th>
            <th>Revenue</th>
            <th>Status</th>
            <th>Completion</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($trip_reports->num_rows > 0): ?>
            <?php while ($report = $trip_reports->fetch_assoc()): 
              $completion_rate = $report['total_seats_sold'] > 0 ? 
                round(($report['total_seats_sold'] / ($report['total_seats_sold'] + $report['available_seats'])) * 100, 1) : 0;
            ?>
              <tr>
                <td><strong>#<?php echo $report['trip_id']; ?></strong></td>
                <td><?php echo htmlspecialchars($report['departure'] . ' → ' . $report['destination']); ?></td>
                <td><?php echo htmlspecialchars($report['plates_number']); ?></td>
                <td><?php echo htmlspecialchars($report['driver_name']); ?></td>
                <td><?php echo date('M j, Y H:i', strtotime($report['departure_datetime'])); ?></td>
                <td><?php echo date('M j, Y H:i', strtotime($report['estimated_arrival'])); ?></td>
                <td>
                  <span class="fw-bold"><?php echo $report['total_seats_sold'] ?? 0; ?></span>
                  <small class="text-muted">passengers</small>
                </td>
                <td>
                  <span class="fw-bold text-success"><?php echo number_format($report['total_revenue'] ?? 0); ?> FRW</span>
                </td>
                <td>
                  <?php
                  $status_class = '';
                  switch($report['status']) {
                    case 'available': $status_class = 'badge-success'; break;
                    case 'ontrip': $status_class = 'badge-primary'; break;
                    case 'arrived': $status_class = 'badge-info'; break;
                    case 'maintenance': $status_class = 'badge-warning'; break;
                  }
                  ?>
                  <span class="badge <?php echo $status_class; ?>">
                    <?php echo ucfirst($report['status']); ?>
                  </span>
                </td>
                <td>
                  <div class="progress" style="height: 20px;">
                    <div class="progress-bar 
                      <?php echo $completion_rate >= 80 ? 'bg-success' : ($completion_rate >= 50 ? 'bg-warning' : 'bg-info'); ?>" 
                         role="progressbar" 
                         style="width: <?php echo $completion_rate; ?>%"
                         aria-valuenow="<?php echo $completion_rate; ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                      <?php echo $completion_rate; ?>%
                    </div>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="10" class="text-center py-5 text-muted">
                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                <p>No trip data found for the selected period.</p>
                <p class="small">Try adjusting your date range or filters.</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Additional Analysis Sections -->
    <div class="row mt-4">
      <!-- Revenue Analysis -->
      <div class="col-md-6">
        <div class="table-container">
          <h5><i class="fas fa-money-bill-wave me-2"></i>Revenue by Route</h5>
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Route</th>
                  <th>Trips</th>
                  <th>Revenue</th>
                </tr>
              </thead>
              <tbody>
                <?php while($route_revenue = $revenue_by_route->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($route_revenue['departure'] . ' → ' . $route_revenue['destination']); ?></td>
                    <td><?php echo $route_revenue['trips_count'] ?? 0; ?></td>
                    <td class="fw-bold text-success"><?php echo number_format($route_revenue['route_revenue'] ?? 0); ?> FRW</td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Performance Analysis -->
      <div class="col-md-6">
        <div class="table-container">
          <h5><i class="fas fa-tachometer-alt me-2"></i>Trip Performance</h5>
          <div class="text-center py-4">
            <div class="display-4 fw-bold text-primary"><?php echo $completion_rate; ?>%</div>
            <p class="text-muted">Trip Completion Rate</p>
            <div class="row text-center">
              <div class="col-4">
                <div class="fw-bold text-success"><?php echo $performance_stats['completed_trips']; ?></div>
                <small>Completed</small>
              </div>
              <div class="col-4">
                <div class="fw-bold text-warning"><?php echo $performance_stats['ongoing_trips']; ?></div>
                <small>Ongoing</small>
              </div>
              <div class="col-4">
                <div class="fw-bold text-info"><?php echo $performance_stats['upcoming_trips']; ?></div>
                <small>Upcoming</small>
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
      </div>
    </div>
  </div>
</div>

    </div>
  </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Your existing JavaScript remains exactly the same
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
          title: 'Fleet Management 🚌',
          subtitle: 'Manage all buses, status, and maintenance'
        },
        'manage-routes': {
          title: 'Route Management 🗺️',
          subtitle: 'Manage bus routes and pricing'
        },
        'manage-trips': {
          title: 'Trip Management 🚗',
          subtitle: 'Manage scheduled trips and assignments'
        },
        'manage-bookings': {
          title: 'Booking Management 🎫',
          subtitle: 'View and manage all passenger bookings'
        },
        'manage-drivers': {
          title: 'Driver Management 👨‍💼',
          subtitle: 'Manage driver profiles and assignments'
        },
        'manage-customers': {
          title: 'Customer Management 👥',
          subtitle: 'Manage customer information and history'
        },
        'manage-users': {
          title: 'User Management 🔐',
          subtitle: 'Manage system users and accounts'
        },
        'reports': {
        title: 'Trip Reports & Analytics 📊',
        subtitle: 'Comprehensive trip analysis and performance reports'
    }
};

      function showSection(sectionId) {
        // Hide all sections
        sections.forEach(section => {
          section.classList.add('hidden');
        });

        // Show the selected section
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
          targetSection.classList.remove('hidden');
        }

        // Update page title and subtitle
        if (pageData[sectionId]) {
          pageTitle.textContent = pageData[sectionId].title;
          pageSubtitle.textContent = pageData[sectionId].subtitle;
        }

        // Update active state in sidebar
        sidebarLinks.forEach(link => {
          link.classList.remove('active');
          if (link.getAttribute('data-section') === sectionId) {
            link.classList.add('active');
          }
        });

        // Update URL without reloading
        const url = new URL(window.location);
        url.searchParams.set('section', sectionId);
        window.history.pushState({}, '', url);
      }

      // Make showSection function globally available
      window.showSection = showSection;

      // Check for section in URL on page load
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

      // Handle browser back/forward buttons
      window.addEventListener('popstate', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const sectionParam = urlParams.get('section');
        if (sectionParam && pageData[sectionParam]) {
          showSection(sectionParam);
        } else {
          showSection('dashboard');
        }
      });

      // Auto-submit search form when typing stops (with delay)
      let searchTimeout;
      const searchInput = document.querySelector('input[name="search"]');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          clearTimeout(searchTimeout);
          searchTimeout = setTimeout(() => {
            document.getElementById('busFilterForm').submit();
          }, 800);
        });
      }

      // Add confirmation for actions
      const actionButtons = document.querySelectorAll('a[href*="assign_bus"], a[href*="release_bus"]');
      actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
          const action = this.href.includes('assign_bus') ? 'assign' : 'release';
          const message = action === 'assign' 
            ? 'Are you sure you want to assign this bus to a new trip?' 
            : 'Are you sure you want to release this bus?';
          
          if (!confirm(message)) {
            e.preventDefault();
          }
        });
      });

      // Auto-refresh page every 2 minutes to update statuses
      setTimeout(() => {
        window.location.reload();
      }, 120000); // 2 minutes
      
      // Real-time departure time checker (every 30 seconds)
      setInterval(() => {
        const currentSection = document.querySelector('.dashboard-section:not(.hidden)');
        if (currentSection && currentSection.id === 'manage-buses') {
          console.log('Checking for bus departures...');
        }
      }, 30000); // 30 seconds
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      });
    }, 5000);
    
    // Function to manually trigger departure time check
    function checkDepartureTimes() {
      fetch('?section=manage-buses&refresh_status=1')
        .then(response => response.text())
        .then(data => {
          window.location.reload();
        })
        .catch(error => {
          console.error('Error checking departure times:', error);
        });
    }

    // Auto-submit route search form when typing stops (with delay)
const routeSearchInput = document.querySelector('input[name="route_search"]');
if (routeSearchInput) {
    let routeSearchTimeout;
    routeSearchInput.addEventListener('input', function() {
        clearTimeout(routeSearchTimeout);
        routeSearchTimeout = setTimeout(() => {
            document.getElementById('routeFilterForm').submit();
        }, 800);
    });
}
// Auto-submit trip search form when typing stops (with delay)
const tripSearchInput = document.querySelector('input[name="trip_search"]');
if (tripSearchInput) {
    let tripSearchTimeout;
    tripSearchInput.addEventListener('input', function() {
        clearTimeout(tripSearchTimeout);
        tripSearchTimeout = setTimeout(() => {
            document.getElementById('tripFilterForm').submit();
        }, 800);
    });
}

// Export functions
function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Add title
    doc.setFontSize(20);
    doc.text('Trip Reports - SwiftPass', 20, 20);
    
    // Add date range
    doc.setFontSize(12);
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    doc.text(`Date Range: ${startDate} to ${endDate}`, 20, 30);
    
    // Add summary statistics
    doc.text('Summary Statistics:', 20, 45);
    const stats = document.querySelectorAll('.stats-card .stat-number');
    doc.text(`Total Trips: ${stats[0].textContent}`, 30, 55);
    doc.text(`Total Passengers: ${stats[1].textContent}`, 30, 65);
    doc.text(`Average Passengers/Trip: ${stats[2].textContent}`, 30, 75);
    doc.text(`Total Revenue: ${stats[3].textContent}`, 30, 85);
    
    // Save the PDF
    doc.save(`trip-reports-${startDate}-to-${endDate}.pdf`);
}

function exportToExcel() {
    // Create a simple CSV export
    let csv = 'Trip ID,Route,Bus,Driver,Departure,Arrival,Passengers,Revenue,Status,Completion Rate\n';
    
    document.querySelectorAll('#reportTable tbody tr').forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 10) {
            const rowData = [
                cells[0].textContent.trim(),
                cells[1].textContent.trim(),
                cells[2].textContent.trim(),
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim(),
                cells[6].querySelector('.fw-bold')?.textContent.trim() || '0',
                cells[7].textContent.trim().replace(' FRW', ''),
                cells[8].textContent.trim(),
                cells[9].querySelector('.progress-bar')?.textContent.trim() || '0%'
            ];
            csv += rowData.map(field => `"${field}"`).join(',') + '\n';
        }
    });
    
    // Create download link
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `trip-reports-${document.querySelector('input[name="start_date"]').value}-to-${document.querySelector('input[name="end_date"]').value}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Auto-submit report filter form when dates change
const reportDateInputs = document.querySelectorAll('#reportFilterForm input[type="date"]');
reportDateInputs.forEach(input => {
    input.addEventListener('change', function() {
        document.getElementById('reportFilterForm').submit();
    });
});

  </script>
</body>
</html>
<?php
$conn->close();
?>