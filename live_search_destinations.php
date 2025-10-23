<?php
include('connection.php');

$search = $_GET['search'] ?? '';

if (strlen($search) >= 2) {
    $stmt = $conn->prepare("
        SELECT DISTINCT 
            departure, 
            destination,
            CONCAT(departure, ' → ', destination) as full_route,
            price_per_seat
        FROM routes 
        WHERE departure LIKE ? OR destination LIKE ? OR CONCAT(departure, ' → ', destination) LIKE ?
        AND status = 'active'
        ORDER BY departure, destination
        LIMIT 10
    ");
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $routes = [];
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
    
    echo json_encode($routes);
} else {
    echo json_encode([]);
}
?>

