<?php
session_start();
include 'includes/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Get customer ID from session
$customer_id = $_SESSION['customer_id'];

// Fetch customer details
$sql = "SELECT Customer_ID, Name, Email, Phone, Address, Date_Of_Birth, Nationality 
        FROM Customer 
        WHERE Customer_ID = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();
} else {
    // Handle case where customer data is not found
    $error_message = "Customer information not found.";
}

// Fetch loyalty program details
$loyalty_sql = "SELECT Points, Membership_Level, Total_Bookings 
                FROM LoyaltyProgram 
                WHERE Customer_ID = ?";
$loyalty_stmt = $conn->prepare($loyalty_sql);
$loyalty_stmt->bind_param("i", $customer_id);
$loyalty_stmt->execute();
$loyalty_result = $loyalty_stmt->get_result();
$loyalty = $loyalty_result->fetch_assoc();

// Fetch booking history for this customer
$booking_sql = "SELECT b.Booking_ID, b.Booking_Date, b.Status, b.NumberOfPeople, 
                b.Total_Cost, tp.Name as PackageName, tp.Duration,
                p.Currency
                FROM Booking b
                JOIN TourPackage tp ON b.TourPackage_ID = tp.TourPackage_ID
                LEFT JOIN Payment p ON b.Booking_ID = p.Booking_ID
                WHERE b.Customer_ID = ?
                ORDER BY b.Booking_Date DESC";
                
$booking_stmt = $conn->prepare($booking_sql);
$booking_stmt->bind_param("i", $customer_id);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">    

    <title>Customer Profile - Travel Agency</title>

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/view_booking.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/nav.css">

</head>
<body style="margin-top: 80px">
  <!-- ***** Header Area Start ***** -->
<header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- ***** Logo Start ***** -->
                    <a href="index.html" class="logo">Travel <em>Agency</em></a>
                    <!-- ***** Logo End ***** -->

                    <!-- ***** Menu Start ***** -->
                    <ul class="nav">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="packages.php">Packages</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="about.php">About us</a></li>

                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <!-- Show Profile Dropdown when Logged In -->
                        <li class="nav-item dropdown">
                            <a href="#" class="dropdown active" id="profileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['name']); ?>
                            </a>
                            <div class="dropdown-menu custom-navbar-dropdown" aria-labelledby="profileDropdown">
                                <a class="dropdown-item" href="view_booking.php">Profile</a>
                                <a class="dropdown-item logout-btn" href="logout.php">Logout</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <!-- Show Login/Signup when Not Logged In -->
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
                    <!-- ***** Menu End ***** -->
                </nav>
            </div>
        </div>
    </div>
</header>
<!-- ***** Header Area End ***** -->

    <!-- Page Content -->
    <div class="container profile-container">
        <div class="row profile-header">
            <div class="col-md-12">
                <h2>Customer Profile</h2>
                <p class="text-muted">View and manage your personal information and booking history</p>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php else: ?>
            <div class="row profile-details">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Personal Information</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Customer ID:</th>
                                    <td><?php echo htmlspecialchars($customer['Customer_ID']); ?></td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td><?php echo htmlspecialchars($customer['Name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?php echo htmlspecialchars($customer['Email']); ?></td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td><?php echo htmlspecialchars($customer['Phone']); ?></td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td><?php echo htmlspecialchars($customer['Address']); ?></td>
                                </tr>
                                <tr>
                                    <th>Date of Birth:</th>
                                    <td><?php echo htmlspecialchars($customer['Date_Of_Birth']); ?></td>
                                </tr>
                                <tr>
                                    <th>Nationality:</th>
                                    <td><?php echo htmlspecialchars($customer['Nationality']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Loyalty Program Status</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Membership Level:</th>
                                    <td>
                                        <span class="badge badge-<?php 
                                            switch($loyalty['Membership_Level']) {
                                                case 'Platinum': echo 'dark'; break;
                                                case 'Gold': echo 'warning'; break;
                                                case 'Silver': echo 'secondary'; break;
                                                default: echo 'danger';
                                            }
                                        ?>">
                                            <?php echo htmlspecialchars($loyalty['Membership_Level']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Points:</th>
                                    <td><?php echo number_format($loyalty['Points']); ?></td>
                                </tr>
                                <tr>
                                    <th>Total Bookings:</th>
                                    <td><?php echo $loyalty['Total_Bookings']; ?></td>
                                </tr>
                                <tr>
                                    <th>Next Level:</th>
                                    <td>
                                        <?php
                                        $next_level = '';
                                        $bookings_needed = 0;
                                        switch($loyalty['Membership_Level']) {
                                            case 'Bronze':
                                                $next_level = 'Silver';
                                                $bookings_needed = 5 - $loyalty['Total_Bookings'];
                                                break;
                                            case 'Silver':
                                                $next_level = 'Gold';
                                                $bookings_needed = 15 - $loyalty['Total_Bookings'];
                                                break;
                                            case 'Gold':
                                                $next_level = 'Platinum';
                                                $bookings_needed = 20 - $loyalty['Total_Bookings'];
                                                break;
                                            default:
                                                $next_level = 'Maximum Level';
                                        }
                                        if ($next_level != 'Maximum Level') {
                                            echo "Need " . $bookings_needed . " more bookings to reach " . $next_level;
                                        } else {
                                            echo "You have reached the highest membership level!";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="booking-history">
                <h3>Booking History</h3>
                
                <?php 
                // Display success/error messages if they exist
                if (isset($_SESSION['success_message'])) {
                    echo '<script>
                        Swal.fire({
                            title: "Success!",
                            text: "' . $_SESSION['success_message'] . '",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    </script>';
                    unset($_SESSION['success_message']);
                }
                if (isset($_SESSION['error_message'])) {
                    echo '<script>
                        Swal.fire({
                            title: "Error!",
                            text: "' . $_SESSION['error_message'] . '",
                            icon: "error",
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    </script>';
                    unset($_SESSION['error_message']);
                }
                
                if ($booking_result->num_rows > 0): ?>
                    <?php while ($booking = $booking_result->fetch_assoc()): ?>
                        <div class="card booking-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Booking #<?php echo $booking['Booking_ID']; ?></span>
                                <span class="booking-status status-<?php echo strtolower($booking['Status']); ?>">
                                    <?php echo $booking['Status']; ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5><?php echo htmlspecialchars($booking['PackageName']); ?></h5>
                                        <p>Duration: <?php echo $booking['Duration']; ?> days</p>
                                        <p>People: <?php echo $booking['NumberOfPeople']; ?></p>
                                        <p>Booked on: <?php echo date('F j, Y', strtotime($booking['Booking_Date'])); ?></p>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <h4>
                                            <?php 
                                            $currency_symbol = '';
                                            switch($booking['Currency']) {
                                                case 'USD':
                                                    $currency_symbol = '$';
                                                    break;
                                                case 'EUR':
                                                    $currency_symbol = '€';
                                                    break;
                                                case 'GBP':
                                                    $currency_symbol = '£';
                                                    break;
                                                case 'INR':
                                                    $currency_symbol = '₹';
                                                    break;
                                                default:
                                                    $currency_symbol = '$'; // Default to USD
                                            }
                                            echo $currency_symbol . number_format($booking['Total_Cost'], 2);
                                            ?>
                                        </h4>
                                        <a href="#" 
                                           class="btn btn-sm btn-danger mt-3" 
                                           onclick="cancelBooking(<?php echo $booking['Booking_ID']; ?>)">
                                            Cancel Booking
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        You haven't made any bookings yet. <a href="packages.php">Browse our packages</a> to book your next adventure!
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

      <!-- Font Awesome CDN -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Scripts -->
    <script src="assets/js/jquery-2.1.0.min.js"></script>
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/scrollreveal.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/imgfix.min.js"></script> 
    <script src="assets/js/mixitup.js"></script> 
    <script src="assets/js/accordions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function cancelBooking(bookingId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Cancelling Booking',
                        text: 'Please wait while we process your request...',
                        icon: 'info',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        willClose: () => {
                            window.location.href = 'cancel_booking.php?id=' + bookingId;
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>