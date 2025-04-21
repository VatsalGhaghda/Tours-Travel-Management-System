<?php
session_start();

// Check if user is authenticated
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "qwepoi"; // Replace with your actual DB password
$database = "TourTravelDB";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

header('Content-Type: application/json');

// Get POST data
$booking_id = $_POST['booking_id'] ?? '';
$status = $_POST['status'] ?? '';

// Validate input
if (empty($booking_id) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

// Debug: Log received data
error_log("Received: booking_id=$booking_id, status=$status");

// Update booking status
$sql = "UPDATE Booking SET Status = ? WHERE Booking_ID = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ss", $status, $booking_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No rows updated. Check Booking_ID match.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $conn->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $conn->error]);
}

$conn->close();
?>