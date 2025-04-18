<?php
session_start();
require_once 'includes/db_connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get parameters from URL and clean the amount
$amount = isset($_GET['amount']) ? str_replace(',', '', $_GET['amount']) : 0;
$amount = (float)$amount;
$people = isset($_GET['people']) ? (int)$_GET['people'] : 0;
$package_id = isset($_GET['package_id']) ? (int)$_GET['package_id'] : 0;
$pricing_id = isset($_GET['pricing_id']) ? (int)$_GET['pricing_id'] : 0;
$guide_id = isset($_GET['guide_id']) ? (int)$_GET['guide_id'] : 0;
$guide_name = isset($_GET['guide_name']) ? $_GET['guide_name'] : '';

// Debug log
error_log("Payment Page - Received Parameters:");
error_log("Amount: " . $amount);
error_log("People: " . $people);
error_log("Package ID: " . $package_id);
error_log("Pricing ID: " . $pricing_id);
error_log("Guide ID: " . $guide_id);
error_log("Customer ID: " . (isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 'Not set'));

// If no amount is provided, redirect back to packages
if ($amount <= 0) {
    header("Location: packages.php");
    exit();
}

// Get today's date in YYYY-MM-DD format
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Payment - Travel Agency</title>

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/payment.css"> 
    <link rel="stylesheet" type="text/css" href="assets/css/nav.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  </head>  
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .payment-container {
            max-width: 600px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }

        .payment-container h2 {
            text-align: center;
            color: #232d39;
            font-weight: 700;
        }

        .btn-submit {
            background: #ed563b;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #c94c32;
        }
    </style>

<body>

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
    <div class="container">
        <div class="payment-container">
            <h2>Payment <em>Details</em></h2>
            <form id="paymentForm" action="process_payment.php" method="POST">
                <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
                <input type="hidden" name="pricing_id" value="<?php echo $pricing_id; ?>">
                <input type="hidden" name="number_of_people" value="<?php echo $people; ?>">
                <input type="hidden" name="guide_id" value="<?php echo $guide_id; ?>">
                <input type="hidden" name="guide_name" value="<?php echo htmlspecialchars($guide_name); ?>">
                
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (<span id="currency_symbol">$</span>)</label>
                    <input type="text" class="form-control" id="amount" name="amount" value="<?php echo number_format($amount, 2); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="currency" class="form-label">Currency</label>
                    <select class="form-control" id="currency" name="currency" required>
                        <option value="USD">USD - US Dollar</option>
                        <option value="EUR">EUR - Euro</option>
                        <option value="GBP">GBP - British Pound</option>
                        <option value="INR">INR - Indian Rupee</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="payment_date" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo $today; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-control" id="payment_method" name="payment_method" required>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Debit Card">Debit Card</option>
                        <option value="UPI">UPI</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Submit Payment</button>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="assets/js/jquery-2.1.0.min.js"></script>
    <!-- Bootstrap -->
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Processing Payment',
                text: 'Please wait while we process your payment...',
                icon: 'info',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                willClose: () => {
                    this.submit();
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            // Handle currency change
            $('#currency').on('change', function() {
                const selectedCurrency = $(this).val();
                const originalAmount = <?php echo $amount; ?>;
                let convertedAmount = originalAmount;

                // Simple conversion rates (you should use real-time rates in production)
                const conversionRates = {
                    'USD': 1,
                    'EUR': 0.92,
                    'GBP': 0.79,
                    'INR': 83.12
                };

                // Update currency symbol
                const currencySymbols = {
                    'USD': '$',
                    'EUR': '€',
                    'GBP': '£',
                    'INR': '₹'
                };
                $('#currency_symbol').text(currencySymbols[selectedCurrency]);

                convertedAmount = originalAmount * conversionRates[selectedCurrency];
                $('#amount').val(convertedAmount.toFixed(2));
            });
        });
    </script>

</body>
</html>
