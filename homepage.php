<?php
session_start();
include('connection.php'); // Make sure $conn is your mysqli connection

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Default username
$username = '';

if ($userId) {
    $stmt = $conn->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $username = $row['firstname'] . ' ' . $row['lastname'];
        }
        $stmt->close();
    }
}

// Handle search and filtering
$route_search = $_POST['route_search'] ?? '';
$travel_date = $_POST['travel_date'] ?? '';

// Fetch trips with available seats using concatenated route search
if (!empty($route_search)) {
    // Split the search term by -> or similar separators
    $route_parts = preg_split('/->|→|-|to/i', $route_search);
    $departure_search = trim($route_parts[0] ?? '');
    $destination_search = trim($route_parts[1] ?? $route_search); // If no separator, search as destination
    
    $stmt = $conn->prepare("
        SELECT 
            t.trip_id,
            t.departure_datetime,
            t.estimated_arrival,
            t.available_seats,
            b.plates_number,
            b.model,
            b.number_of_seats,
            r.route_id,
            r.departure,
            r.destination,
            r.price_per_seat,
            d.name as driver_name,
            CONCAT(r.departure, ' → ', r.destination) as full_route
        FROM trips t
        JOIN buses b ON t.bus_id = b.bus_id
        JOIN routes r ON t.route_id = r.route_id
        JOIN drivers d ON t.driver_id = d.driver_id
        WHERE (r.departure LIKE ? OR r.destination LIKE ? OR CONCAT(r.departure, ' → ', r.destination) LIKE ?)
        AND DATE(t.departure_datetime) = ?
        AND t.available_seats > 0
        AND t.status IN ('available', 'ontrip')
        AND b.status = 'active'
        ORDER BY t.departure_datetime ASC
    ");
    $search_term = "%" . $route_search . "%";
    $departure_term = "%" . $departure_search . "%";
    $destination_term = "%" . $destination_search . "%";
    $stmt->bind_param("ssss", $departure_term, $destination_term, $search_term, $travel_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $search_performed = true;
} else {
    // Show available trips for today if no search
    $today = date('Y-m-d');
    $result = $conn->query("
        SELECT 
            t.trip_id,
            t.departure_datetime,
            t.estimated_arrival,
            t.available_seats,
            b.plates_number,
            b.model,
            b.number_of_seats,
            r.route_id,
            r.departure,
            r.destination,
            r.price_per_seat,
            d.name as driver_name,
            CONCAT(r.departure, ' → ', r.destination) as full_route
        FROM trips t
        JOIN buses b ON t.bus_id = b.bus_id
        JOIN routes r ON t.route_id = r.route_id
        JOIN drivers d ON t.driver_id = d.driver_id
        WHERE DATE(t.departure_datetime) >= '$today'
        AND t.available_seats > 0
        AND t.status IN ('available', 'ontrip')
        AND b.status = 'active'
        ORDER BY t.departure_datetime ASC
        LIMIT 12
    ");
    $search_performed = false;
}

// Get search statistics
$total_buses = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SwiftPass | Smart Bus Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
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
      text-decoration: none;
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
    
    .main-content {
      margin-left: var(--sidebar-width);
      padding: 2rem;
      transition: all 0.3s;
      min-height: 100vh;
    }
    
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

    .search-form {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
      backdrop-filter: blur(10px);
    }

    .search-form h5 {
      color: var(--primary);
      font-weight: 700;
      margin-bottom: 1.5rem;
    }

    .results-header {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 1.5rem 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
      backdrop-filter: blur(10px);
    }

    .results-count {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--primary);
    }

    .search-info {
      color: #6c757d;
      font-size: 0.9rem;
    }

    .bus-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 1.5rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      border: none;
      height: 100%;
      backdrop-filter: blur(10px);
      border-left: 5px solid var(--secondary);
      color: #333;
    }

    .bus-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .status-badge {
      padding: 0.5rem 1rem;
      border-radius: 50px;
      font-size: 0.8rem;
      font-weight: 600;
      display: inline-block;
      color: white !important;
    }
    
    .badge-available {
      background: linear-gradient(135deg, var(--success), #27ae60);
    }

    .badge-ontrip {
      background: linear-gradient(135deg, var(--primary), #2c3e50);
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      border: none;
      border-radius: 10px;
      padding: 0.75rem 2rem;
      font-weight: 600;
      transition: all 0.3s ease;
      color: white;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
      color: white;
    }

    .btn-success {
      background: linear-gradient(135deg, var(--success), #27ae60);
      border: none;
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
      color: white;
    }

    .btn-outline-primary {
      border: 2px solid var(--secondary);
      color: var(--secondary);
      background: transparent;
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
      background: var(--secondary);
      color: white;
      transform: translateY(-2px);
    }

    .live-search-results {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      max-height: 300px;
      overflow-y: auto;
      z-index: 1000;
      display: none;
      color: #333;
    }

    .search-result-item {
      padding: 1rem;
      border-bottom: 1px solid #e9ecef;
      cursor: pointer;
      transition: all 0.3s ease;
      color: #333;
    }

    .search-result-item:hover {
      background: rgba(52, 152, 219, 0.05);
    }

    .form-control, .form-select {
      border-radius: 10px;
      border: 2px solid #e9ecef;
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
      color: #333;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--secondary);
      box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
      color: #333;
    }

    .bus-info {
      color: #333 !important;
    }

    .bus-info .text-muted {
      color: #6c757d !important;
    }

    .bus-info .text-success {
      color: #198754 !important;
    }

    .bus-info .fw-semibold {
      color: #333 !important;
    }

    .loading-spinner {
      display: none;
      text-align: center;
      padding: 2rem;
    }

    .spinner {
      border: 4px solid #f3f3f3;
      border-top: 4px solid var(--secondary);
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 0 auto 1rem;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .bus-info-simple {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .info-row {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .info-icon {
      width: 20px;
      text-align: center;
      color: var(--secondary);
    }

    .info-text {
      flex: 1;
    }

    .no-results {
      text-align: center;
      padding: 3rem;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      color: #333;
    }

    .no-results i {
      font-size: 4rem;
      color: #bdc3c7;
      margin-bottom: 1rem;
    }

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
    }
    
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
      <a href="homepage.php" class="nav-link active">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
      </a>
      <!-- Removed Booking link from sidebar -->
      <a href="setting.php" class="nav-link">
        <i class="fas fa-cogs"></i>
        <span>Settings</span>
      </a>
      <a href="logout.php" class="nav-link">
        <i class="fas fa-sign-out-alt"></i>
        <span>Log Out</span>
      </a>
    </div>
  </div>

  <div class="main-content">
    <!-- Header -->
    <div class="header">
      <div>
        <h2>Welcome back, <?php echo htmlspecialchars($username); ?>! 👋</h2>
        <p>Plan your next journey with ease and comfort</p>
      </div>
      <div class="user-info">
        <div class="text-end">
          <div class="fw-bold text-dark"><?php echo htmlspecialchars($username); ?></div>
          <small class="text-muted">Passenger</small>
        </div>
        <div class="user-avatar"><?php echo substr($username, 0, 1); ?></div>
      </div>
    </div>

    <!-- Search Form -->
    <div class="search-form">
      <h5><i class="fas fa-search me-2"></i>Find Your Perfect Bus</h5>
      <form class="row g-3" method="POST" action="" id="searchForm">
        <div class="col-md-5 position-relative">
          <label for="route_search" class="form-label small text-muted">Route Search</label>
          <input type="text" class="form-control" id="route_search" name="route_search" 
                 placeholder="Enter route (e.g., Kigali → Musanze)" 
                 value="<?= htmlspecialchars($route_search) ?>" required autocomplete="off">
          <div class="live-search-results" id="liveSearchResults"></div>
        </div>

        <div class="col-md-4">
          <label for="date" class="form-label small text-muted">Travel Date</label>
          <input type="date" class="form-control" id="date" name="travel_date" value="<?= htmlspecialchars($travel_date) ?>" required>
        </div>

        <div class="col-md-3 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="fas fa-search me-2"></i> Search Trips
          </button>
        </div>
      </form>
    </div>

    <!-- Search Results Header -->
    <?php if ($search_performed && !empty($route_search)): ?>
    <div class="results-header">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5 class="results-count mb-1">
            <i class="fas fa-bus me-2"></i>
            <?php echo $total_buses; ?> Available Trip<?php echo $total_buses != 1 ? 's' : ''; ?> Found
          </h5>
          <p class="search-info mb-0">
            Showing results for route: <strong>"<?php echo htmlspecialchars($route_search); ?>"</strong>
            <?php if (!empty($travel_date)): ?>
              on <strong><?php echo date('M j, Y', strtotime($travel_date)); ?></strong>
            <?php endif; ?>
          </p>
        </div>
        <div>
          <button type="button" class="btn btn-outline-primary" onclick="clearSearch()">
            <i class="fas fa-times me-2"></i>Clear Search
          </button>
        </div>
      </div>
    </div>
    <?php elseif ($search_performed && empty($route_search)): ?>
    <div class="results-header">
      <div class="text-center">
        <h5 class="results-count mb-1">
          <i class="fas fa-info-circle me-2"></i>
          Please Enter a Route
        </h5>
        <p class="search-info mb-0">Enter a route to search for available trips</p>
      </div>
    </div>
    <?php else: ?>
   
    <?php endif; ?>

    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
      <div class="spinner"></div>
      <p class="text-muted">Searching for trips...</p>
    </div>

    <!-- Trip Results -->
    <div class="container-fluid" id="busResults">
      <div class="row g-4 px-3">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Calculate trip duration
                $departure = new DateTime($row['departure_datetime']);
                $arrival = new DateTime($row['estimated_arrival']);
                $duration = $departure->diff($arrival);
                $duration_hours = $duration->h;
                $duration_minutes = $duration->i;

                // Format dates and times
                $departure_date = date('M j, Y', strtotime($row['departure_datetime']));
                $departure_time = date('H:i', strtotime($row['departure_datetime']));
                $arrival_time = date('H:i', strtotime($row['estimated_arrival']));
                
                // Determine status badge
                $status_badge = $row['available_seats'] > 0 ? 'badge-available' : 'available';
                $status_text = $row['available_seats'] > 0 ? $row['available_seats'] . ' Seats Left' : 'available';
                
                echo '
                <div class="col-xl-4 col-lg-6 col-md-6">
                  <div class="bus-card bus-info">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <h6 class="fw-bold text-primary mb-0">'.htmlspecialchars($row['model']).'</h6>
                      <span class="status-badge '.$status_badge.'">'.$status_text.'</span>
                    </div>

                    <div class="bus-info-simple mb-3">
                      <div class="info-row">
                        <div class="info-icon">
                          <i class="fas fa-route"></i>
                        </div>
                        <div class="info-text">
                          <span class="fw-semibold">'.htmlspecialchars($row['full_route']).'</span>
                        </div>
                      </div>
                      
                      <div class="info-row">
                        <div class="info-icon">
                          <i class="far fa-calendar"></i>
                        </div>
                        <div class="info-text">
                          <span class="fw-semibold">Date: '.$departure_date.'</span>
                        </div>
                      </div>
                      
                      <div class="info-row">
                        <div class="info-icon">
                          <i class="far fa-clock"></i>
                        </div>
                        <div class="info-text">
                          <span class="fw-semibold">Time: '.$departure_time.' - '.$arrival_time.'</span>
                        </div>
                      </div>
                      
                      <div class="info-row">
                        <div class="info-icon">
                          <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="info-text">
                          <span class="fw-semibold">Duration: '.$duration_hours.'h '.$duration_minutes.'m</span>
                        </div>
                      </div>
                      
                      <div class="info-row">
                        <div class="info-icon">
                          <i class="fas fa-bus"></i>
                        </div>
                        <div class="info-text">
                          <span class="fw-semibold">Bus: '.htmlspecialchars($row['model']).' ('.htmlspecialchars($row['plates_number']).')</span>
                        </div>
                      </div>

                      <div class="info-row">
                        <div class="info-icon">
                          <i class="fas fa-id-card"></i>
                        </div>
                        <div class="info-text">
                          <span class="fw-semibold">Driver: '.htmlspecialchars($row['driver_name']).'</span>
                        </div>
                      </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                      <div>
                        <h5 class="text-success mb-0">'.number_format($row['price_per_seat']).' FRW</h5>
                        <small class="text-muted">per seat</small>
                      </div>
                      <a href="bookingpage.php?trip_id='.$row['trip_id'].'&price='.$row['price_per_seat'].'" class="btn btn-success">
                        <i class="fas fa-ticket-alt me-2"></i>Book Now
                      </a>
                    </div>
                  </div>
                </div>';
            }
        } else {
            echo '
            <div class="col-12">
              <div class="no-results">
                <i class="fas fa-bus"></i>
                <h4>No Available Trips Found</h4>
                <p class="text-muted mb-3">';
                
            if ($search_performed) {
                echo 'No available trips found for your search criteria. Try adjusting your search or date.';
            } else {
                echo 'No available trips are currently scheduled. Please check back later.';
            }
            
            echo '</p>
                <button type="button" class="btn btn-primary" onclick="clearSearch()">
                  <i class="fas fa-refresh me-2"></i>Refresh Search
                </button>
              </div>
            </div>';
        }

        $conn->close();
        ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Set default date to today
    document.getElementById('date').valueAsDate = new Date();

    // Show loading when form is submitted
    document.getElementById('searchForm').addEventListener('submit', function() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('busResults').style.opacity = '0.5';
    });

    // Clear search function
    function clearSearch() {
        document.getElementById('route_search').value = '';
        document.getElementById('date').valueAsDate = new Date();
        document.getElementById('searchForm').submit();
    }

    // Live Search Functionality for routes
    const routeSearchInput = document.getElementById('route_search');
    const liveSearchResults = document.getElementById('liveSearchResults');
    let searchTimeout;

    routeSearchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim();
        
        if (searchTerm.length < 2) {
            liveSearchResults.style.display = 'none';
            return;
        }

        // Show loading in search results
        liveSearchResults.innerHTML = '<div class="search-result-item text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Searching routes...</div>';
        liveSearchResults.style.display = 'block';

        searchTimeout = setTimeout(() => {
            fetchLiveSearchResults(searchTerm);
        }, 500);
    });

    function fetchLiveSearchResults(searchTerm) {
        fetch('live_search_destinations.php?search=' + encodeURIComponent(searchTerm))
            .then(response => response.json())
            .then(data => {
                displayLiveSearchResults(data);
            })
            .catch(error => {
                console.error('Error fetching search results:', error);
                liveSearchResults.innerHTML = '<div class="search-result-item text-muted">Error loading results</div>';
            });
    }

    function displayLiveSearchResults(results) {
        liveSearchResults.innerHTML = '';
        
        if (results.length === 0) {
            liveSearchResults.innerHTML = '<div class="search-result-item text-muted">No routes found</div>';
        } else {
            results.forEach(route => {
                const resultItem = document.createElement('div');
                resultItem.className = 'search-result-item';
                resultItem.innerHTML = `
                    <div class="fw-semibold text-primary">${route.full_route}</div>
                    <small class="text-muted">${route.price_per_seat ? new Intl.NumberFormat().format(route.price_per_seat) + ' FRW' : 'Price varies'}</small>
                `;
                resultItem.addEventListener('click', function() {
                    routeSearchInput.value = route.full_route;
                    liveSearchResults.style.display = 'none';
                    // Show loading and submit form
                    document.getElementById('loadingSpinner').style.display = 'block';
                    document.getElementById('busResults').style.opacity = '0.5';
                    document.getElementById('searchForm').submit();
                });
                liveSearchResults.appendChild(resultItem);
            });
        }
        
        liveSearchResults.style.display = 'block';
    }

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!routeSearchInput.contains(e.target) && !liveSearchResults.contains(e.target)) {
            liveSearchResults.style.display = 'none';
        }
    });

    // Add entrance animation
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.bus-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
  </script>
</body>
</html>