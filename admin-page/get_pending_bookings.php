<?php
header('Content-Type: text/html');

// Database connection
$host = "localhost";
$username = "root";
$password = "qwepoi"; // Replace with your actual DB password
$database = "TourTravelDB";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    echo "<div class='alert alert-danger'>Connection failed: " . $conn->connect_error . "</div>";
    exit();
}

$pending_bookings = $conn->query("SELECT Booking_ID, Customer_ID, Booking_Date FROM Booking WHERE Status = 'pending' ORDER BY Booking_Date DESC");
if ($pending_bookings->num_rows > 0) {
    while ($booking = $pending_bookings->fetch_assoc()) {
        echo "<div class='item'>";
        echo "<div class='feed d-flex justify-content-between'>";
        echo "<div class='feed-body d-flex justify-content-between'>";
        echo "<div class='content'>";
        echo "<h5>Booking ID: " . htmlspecialchars($booking['Booking_ID']) . " (Customer ID: " . htmlspecialchars($booking['Customer_ID']) . ")</h5>";
        echo "<span>Booking Date: " . htmlspecialchars(date('Y-m-d H:i:s', strtotime($booking['Booking_Date']))) . "</span>";
        echo "<div class='CTAs mt-2'>";
        echo "<button class='btn btn-xs btn-success accept-btn' data-booking-id='" . htmlspecialchars($booking['Booking_ID']) . "'>Accept</button>";
        echo "<button class='btn btn-xs btn-danger reject-btn' data-booking-id='" . htmlspecialchars($booking['Booking_ID']) . "'>Reject</button>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "<div class='date text-right'><small>" . htmlspecialchars(date('H:i', strtotime($booking['Booking_Date']))) . " ago</small></div>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<div class='item'>";
    echo "<div class='feed d-flex justify-content-center'>";
    echo "<div class='content text-center'>";
    echo "<span>No pending requests at the moment.</span>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}

$conn->close();
?>