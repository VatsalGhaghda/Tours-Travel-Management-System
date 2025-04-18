<?php
session_start();
require_once 'includes/db_connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Function to send JSON response
function sendResponse($success, $message, $data = []) {
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $data));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method');
}

try {
    $coupon_code = isset($_POST['coupon_code']) ? trim($_POST['coupon_code']) : '';
    $package_id = isset($_POST['package_id']) ? (int)$_POST['package_id'] : 0;
    $total_cost = isset($_POST['total_cost']) ? (float)$_POST['total_cost'] : 0;

    if (empty($coupon_code) || $package_id <= 0 || $total_cost <= 0) {
        sendResponse(false, 'Invalid input data');
    }

    // Check if coupon exists and is valid for this package
    $sql = "SELECT * FROM Discount 
            WHERE Discount_Code = ? 
            AND (TourPackage_ID = ? OR TourPackage_ID IS NULL)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        sendResponse(false, 'Database error: ' . $conn->error);
    }

    $stmt->bind_param("si", $coupon_code, $package_id);
    if (!$stmt->execute()) {
        sendResponse(false, 'Database error: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        sendResponse(false, 'Invalid coupon code for this package');
    }

    $coupon = $result->fetch_assoc();

    // Calculate discount amount (percentage of total cost)
    $discount_amount = ($total_cost * $coupon['Discount_Percentage']) / 100;

    sendResponse(true, 'Coupon applied successfully!', [
        'discount_amount' => $discount_amount,
        'coupon_id' => $coupon['Discount_ID']
    ]);

} catch (Exception $e) {
    sendResponse(false, 'An error occurred: ' . $e->getMessage());
} 