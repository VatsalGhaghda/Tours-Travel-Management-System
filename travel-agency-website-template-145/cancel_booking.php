<?php
session_start();
require_once 'includes/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($booking_id <= 0) {
    header("Location: view_booking.php");
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // 1. Verify the booking exists and belongs to the customer
    $check_booking_sql = "SELECT Booking_ID FROM Booking WHERE Booking_ID = ? AND Customer_ID = ?";
    $check_booking_stmt = $conn->prepare($check_booking_sql);
    $check_booking_stmt->bind_param("ii", $booking_id, $_SESSION['customer_id']);
    $check_booking_stmt->execute();
    $booking_result = $check_booking_stmt->get_result();
    
    if (!$booking_result->fetch_assoc()) {
        throw new Exception("Booking not found or unauthorized access");
    }

    // 2. Delete payment record
    $delete_payment_sql = "DELETE FROM Payment WHERE Booking_ID = ?";
    $delete_payment_stmt = $conn->prepare($delete_payment_sql);
    $delete_payment_stmt->bind_param("i", $booking_id);
    $delete_payment_stmt->execute();

    // 3. Delete booking record
    $delete_booking_sql = "DELETE FROM Booking WHERE Booking_ID = ? AND Customer_ID = ?";
    $delete_booking_stmt = $conn->prepare($delete_booking_sql);
    $delete_booking_stmt->bind_param("ii", $booking_id, $_SESSION['customer_id']);
    $delete_booking_stmt->execute();

    // Commit transaction
    $conn->commit();
    
    // Redirect back to view_booking.php with success message
    $_SESSION['success_message'] = "Booking has been cancelled successfully.";
    header("Location: view_booking.php");
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error_message'] = "Error cancelling booking: " . $e->getMessage();
    header("Location: view_booking.php");
    exit();
}
?> 