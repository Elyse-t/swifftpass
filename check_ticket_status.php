<?php
include('connection.php');

$ticket_id = $_GET['ticket_id'];

$stmt = $conn->prepare("SELECT checked, checked_at FROM tickets WHERE ticket_id = ?");
$stmt->bind_param("s", $ticket_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo json_encode($result);
?>