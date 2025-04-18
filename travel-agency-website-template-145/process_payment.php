<?php
session_start();
require_once 'includes/db_connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    die("User not logged in");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Display POST data for debugging
    echo "<pre>";
    echo "POST Data:\n";
    print_r($_POST);
    echo "\nSession Data:\n";
    print_r($_SESSION);
    echo "</pre>";
    
    // Get form data with validation
    $package_id = isset($_POST['package_id']) ? (int)$_POST['package_id'] : 0;
    $pricing_id = isset($_POST['pricing_id']) ? (int)$_POST['pricing_id'] : 0;
    $number_of_people = isset($_POST['number_of_people']) ? (int)$_POST['number_of_people'] : 0;
    $amount = isset($_POST['amount']) ? str_replace(['$', ','], '', $_POST['amount']) : 0;
    $currency = isset($_POST['currency']) ? $_POST['currency'] : '';
    $payment_date = isset($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d');
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
    $customer_id = $_SESSION['customer_id'];
    $guide_id = isset($_POST['guide_id']) ? (int)$_POST['guide_id'] : 0;

    // Debug log
    error_log("Process Payment - Received Parameters:");
    error_log("Guide ID: " . $guide_id);
    error_log("Package ID: " . $package_id);
    error_log("Customer ID: " . $customer_id);

    // Calculate total cost (amount * number of people)
    $total_cost = $amount * $number_of_people;

    // Display processed data
    echo "<pre>";
    echo "Processed Data:\n";
    echo "Package ID: $package_id\n";
    echo "Pricing ID: $pricing_id\n";
    echo "Number of People: $number_of_people\n";
    echo "Amount: $amount\n";
    echo "Total Cost: $total_cost\n";
    echo "Currency: $currency\n";
    echo "Payment Date: $payment_date\n";
    echo "Payment Method: $payment_method\n";
    echo "Customer ID: $customer_id\n";
    echo "Guide ID: $guide_id\n";
    echo "</pre>";

    // Verify package exists
    $check_package = $conn->prepare("SELECT TourPackage_ID FROM TourPackage WHERE TourPackage_ID = ?");
    if (!$check_package) {
        die("Package check prepare failed: " . $conn->error);
    }
    $check_package->bind_param("i", $package_id);
    if (!$check_package->execute()) {
        die("Package check execute failed: " . $check_package->error);
    }
    $package_result = $check_package->get_result();
    if (!$package_result->fetch_assoc()) {
        die("Package ID does not exist: " . $package_id);
    }

    // Start transaction
    $conn->begin_transaction();
    
    try {
        // 1. Insert into Booking table
        $booking_sql = "INSERT INTO Booking (
            Customer_ID, 
            TourPackage_ID, 
            NumberOfPeople, 
            Booking_Date,
            Total_Cost,
            Status
        ) VALUES (?, ?, ?, CURDATE(), ?, 'Pending')";
        
        echo "Booking SQL: $booking_sql<br>";
        
        $booking_stmt = $conn->prepare($booking_sql);
        if (!$booking_stmt) {
            throw new Exception("Booking prepare failed: " . $conn->error);
        }
        
        $booking_stmt->bind_param("iiid", $customer_id, $package_id, $number_of_people, $total_cost);
        if (!$booking_stmt->execute()) {
            throw new Exception("Booking execute failed: " . $booking_stmt->error);
        }
        
        $booking_id = $conn->insert_id;
        echo "Booking created with ID: $booking_id<br>";
        
        // 2. Insert into Payment table
        $payment_sql = "INSERT INTO Payment (
            Booking_ID, 
            Amount, 
            Currency, 
            Payment_Date, 
            Payment_Method, 
            Transaction_ID, 
            Status
        ) VALUES (?, ?, ?, ?, ?, ?, 'Completed')";
        
        echo "Payment SQL: $payment_sql<br>";
        
        $payment_stmt = $conn->prepare($payment_sql);
        if (!$payment_stmt) {
            throw new Exception("Payment prepare failed: " . $conn->error);
        }
        
        $transaction_id = 'TRX' . strtoupper(substr(uniqid(), -8));
        
        $payment_stmt->bind_param("idssss", 
            $booking_id, 
            $total_cost,
            $currency, 
            $payment_date, 
            $payment_method, 
            $transaction_id
        );
        
        if (!$payment_stmt->execute()) {
            throw new Exception("Payment execute failed: " . $payment_stmt->error);
        }
        
        echo "Payment created with Transaction ID: $transaction_id<br>";
        
        // 3. Update guide availability status
        if ($guide_id > 0) {
            error_log("Updating guide status for Guide ID: " . $guide_id);
            $update_guide_sql = "UPDATE TourGuide SET Availability_Status = 'Unavailable' WHERE Guide_ID = ?";
            $update_guide_stmt = $conn->prepare($update_guide_sql);
            if (!$update_guide_stmt) {
                throw new Exception("Guide update prepare failed: " . $conn->error);
            }
            
            $update_guide_stmt->bind_param("i", $guide_id);
            if (!$update_guide_stmt->execute()) {
                throw new Exception("Guide update execute failed: " . $update_guide_stmt->error);
            }
            error_log("Guide status updated successfully");
        }
        
        // 4. Handle Loyalty Program
        // Check if customer exists in loyalty program
        $check_loyalty_sql = "SELECT * FROM LoyaltyProgram WHERE Customer_ID = ?";
        $check_loyalty_stmt = $conn->prepare($check_loyalty_sql);
        $check_loyalty_stmt->bind_param("i", $customer_id);
        $check_loyalty_stmt->execute();
        $loyalty_result = $check_loyalty_stmt->get_result();
        $loyalty = $loyalty_result->fetch_assoc();

        if ($loyalty_result->num_rows == 0) {
            // Create new loyalty program entry
            $insert_loyalty_sql = "INSERT INTO LoyaltyProgram (Customer_ID, Points, Membership_Level, Total_Bookings) VALUES (?, 0, 'Bronze', 0)";
            $insert_loyalty_stmt = $conn->prepare($insert_loyalty_sql);
            $insert_loyalty_stmt->bind_param("i", $customer_id);
            if (!$insert_loyalty_stmt->execute()) {
                throw new Exception("Failed to create loyalty program entry: " . $insert_loyalty_stmt->error);
            }
        }

        // Calculate points earned (1 point per $10 spent)
        $points_earned = 0;
        
        // Convert all currencies to USD for consistent points calculation
        $usd_amount = 0;
        switch ($currency) {
            case 'USD':
                $usd_amount = $total_cost;
                break;
            case 'EUR':
                $usd_amount = $total_cost * 1.08; // 1 EUR = 1.08 USD
                break;
            case 'GBP':
                $usd_amount = $total_cost * 1.26; // 1 GBP = 1.26 USD
                break;
            case 'INR':
                $usd_amount = $total_cost / 83; // 1 USD = 83 INR
                break;
        }
        
        $points_earned = floor($usd_amount / 10);
        
        // Check if points were used for discount
        $points_used = 0;
        if (isset($_POST['use_points']) && $_POST['use_points'] == 'on') {
            // Calculate points used based on the discount applied
            $points_used = min($loyalty['Points'], 100); // Max 100 points (10% discount)
            
            // Verify customer has enough points
            if ($loyalty['Points'] < $points_used) {
                throw new Exception("Insufficient loyalty points");
            }
        }
        
        // Update loyalty program
        $update_loyalty_sql = "UPDATE LoyaltyProgram 
                              SET Points = Points + ? - ?,
                                  Total_Bookings = Total_Bookings + 1,
                                  Membership_Level = CASE
                                      WHEN Total_Bookings + 1 >= 20 THEN 'Platinum'
                                      WHEN Total_Bookings + 1 >= 15 THEN 'Gold'
                                      WHEN Total_Bookings + 1 >= 5 THEN 'Silver'
                                      ELSE 'Bronze'
                                  END
                              WHERE Customer_ID = ?";
        $update_loyalty_stmt = $conn->prepare($update_loyalty_sql);
        $update_loyalty_stmt->bind_param("iii", $points_earned, $points_used, $customer_id);
        if (!$update_loyalty_stmt->execute()) {
            throw new Exception("Failed to update loyalty program: " . $update_loyalty_stmt->error);
        }

        // Commit transaction
        $conn->commit();
        error_log("Transaction committed successfully");
        
        // Redirect back to packages page
        header("Location: packages.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Payment processing error: " . $e->getMessage());
        die("Payment processing error: " . $e->getMessage());
    }
} else {
    die("Invalid request method: " . $_SERVER["REQUEST_METHOD"]);
}
?> 