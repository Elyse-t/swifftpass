<?php
session_start();
include('connection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trip_id = $_POST['trip_id'] ?? '';
    $number_of_seats = $_POST['number_of_seats'] ?? '';
    $action = $_POST['action'] ?? '';
    
    if ($action === 'subtract_seats' && !empty($trip_id) && !empty($number_of_seats)) {
        // Get current available seats
        $sql = "SELECT available_seats FROM trips WHERE trip_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $trip_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $trip = $result->fetch_assoc();
            $old_seats = $trip['available_seats'];
            $new_seats = $old_seats - $number_of_seats;
            
            // Ensure we don't go below 0
            if ($new_seats < 0) $new_seats = 0;
            
            // Update the seats
            $update_sql = "UPDATE trips SET available_seats = ? WHERE trip_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $new_seats, $trip_id);
            
            if ($update_stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Seats updated successfully',
                    'old_seats' => $old_seats,
                    'new_seats' => $new_seats,
                    'seats_booked' => $number_of_seats
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update seats in database'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Trip not found'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid parameters'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

$conn->close();
?>