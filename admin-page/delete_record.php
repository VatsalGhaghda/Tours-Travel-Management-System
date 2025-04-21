<?php
session_start();

// Check if admin is authenticated
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
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Validate and sanitize input
$table = isset($_POST['table']) ? filter_var($_POST['table'], FILTER_SANITIZE_STRING) : '';
$id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_SANITIZE_STRING) : '';

$tables = [
    'Customer', 'TourPackage', 'Booking', 'Payment', 'Review',
    'Destination', 'TourGuide', 'TourPackagePricing', 'TourCategory',
    'ActivityType', 'TourPackageSchedule', 'FAQ', 'Discount',
    'TourPackageAmenities', 'LoyaltyProgram'
];

$primary_keys = [
    'Customer' => 'Customer_ID',
    'TourPackage' => 'TourPackage_ID',
    'Booking' => 'Booking_ID',
    'Payment' => 'Payment_ID',
    'Review' => 'Review_ID',
    'Destination' => 'Destination_ID',
    'TourGuide' => 'TourGuide_ID',
    'TourPackagePricing' => 'Pricing_ID',
    'TourCategory' => 'Category_ID',
    'ActivityType' => 'ActivityType_ID',
    'TourPackageSchedule' => 'Schedule_ID',
    'FAQ' => 'FAQ_ID',
    'Discount' => 'Discount_ID',
    'TourPackageAmenities' => 'Amenities_ID',
    'LoyaltyProgram' => 'LoyaltyProgram_ID'
];

if (!in_array($table, $tables) || !array_key_exists($table, $primary_keys) || empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid table or ID.']);
    exit();
}

$primary_key = $primary_keys[$table];

// Prepare and execute delete query
$stmt = $conn->prepare("DELETE FROM `$table` WHERE `$primary_key` = ?");
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("s", $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>