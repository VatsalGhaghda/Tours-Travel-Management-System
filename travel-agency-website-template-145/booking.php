<?php
session_start();
require_once 'includes/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Get parameters from URL
$package_id = isset($_GET['package_id']) ? (int)$_GET['package_id'] : 0;
$pricing_id = isset($_GET['pricing_id']) ? (int)$_GET['pricing_id'] : 0;
$season = isset($_GET['season']) ? $_GET['season'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$duration = isset($_GET['duration']) ? (int)$_GET['duration'] : 0;
$difficulty = isset($_GET['difficulty']) ? $_GET['difficulty'] : '';
$price = isset($_GET['price']) ? (float)$_GET['price'] : 0;

// Fetch customer details
$customer_sql = "SELECT Name FROM Customer WHERE Customer_ID = ?";
$customer_stmt = $conn->prepare($customer_sql);
$customer_stmt->bind_param("i", $_SESSION['customer_id']);
$customer_stmt->execute();
$customer_result = $customer_stmt->get_result();
$customer = $customer_result->fetch_assoc();

// Fetch loyalty program details
$loyalty_sql = "SELECT Points, Membership_Level FROM LoyaltyProgram WHERE Customer_ID = ?";
$loyalty_stmt = $conn->prepare($loyalty_sql);
$loyalty_stmt->bind_param("i", $_SESSION['customer_id']);
$loyalty_stmt->execute();
$loyalty_result = $loyalty_stmt->get_result();
$loyalty = $loyalty_result->fetch_assoc();

// Calculate membership discount
$membership_discount = 0;
if ($loyalty) {
    switch ($loyalty['Membership_Level']) {
        case 'Silver':
            $membership_discount = 5;
            break;
        case 'Gold':
            $membership_discount = 10;
            break;
        case 'Platinum':
            $membership_discount = 20;
            break;
    }
}

// Basic validation
if (!$customer || !$package_id) {
    header("Location: packages.php");
    exit();
}

// Fetch package details with all required fields
$package_sql = "SELECT tp.Name, tp.Duration, tp.Max_People, tp.Difficulty_Level, 
                tpp.Season, tpp.Start_Date, tpp.End_Date, tpp.Price, tpp.Discounted_Price
                FROM TourPackage tp
                LEFT JOIN TourPackagePricing tpp ON tp.TourPackage_ID = tpp.TourPackage_ID
                WHERE tp.TourPackage_ID = ?";
if ($pricing_id) {
    $package_sql .= " AND tpp.Pricing_ID = ?";
}

$package_stmt = $conn->prepare($package_sql);
if ($pricing_id) {
    $package_stmt->bind_param("ii", $package_id, $pricing_id);
} else {
    $package_stmt->bind_param("i", $package_id);
}
$package_stmt->execute();
$package_result = $package_stmt->get_result();
$package = $package_result->fetch_assoc();

// If we have a guide_id but no pricing_id, we're coming from guide selection
if (isset($_GET['guide_id']) && !$pricing_id) {
    // Keep the current page but show an error message
    $error_message = "Please select a tour package first.";
} else if (!$package) {
    header("Location: packages.php");
    exit();
}

// Use package details from database if available
$season = $package['Season'] ?? $season;
$start_date = $package['Start_Date'] ?? $start_date;
$end_date = $package['End_Date'] ?? $end_date;
$duration = $package['Duration'] ?? $duration;
$difficulty = $package['Difficulty_Level'] ?? $difficulty;
$price = $package['Discounted_Price'] ?? $price;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Booking - Travel Agency</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="./assets/css/booking.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/nav.css"> 
</head>

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

    <!-- Booking Section -->
    <section class="booking-section">
        <div class="container">
            <div class="booking-container">
                <div class="section-heading">
                    <h2>Book Your <em>Tour</em></h2>
                </div>
                <form id="bookingForm" action="process_booking.php" method="POST">
                    <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
                    <input type="hidden" name="pricing_id" value="<?php echo $pricing_id; ?>">
                    
                    <div class="form-group">
                        <label for="customer_name">Customer Name</label>
                        <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($customer['Name']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="tour_package">Tour Package</label>
                        <input type="text" id="tour_package" name="tour_package" value="<?php echo htmlspecialchars($package['Name']); ?>" readonly>
                        <a href="tour_guide.php?package_id=<?php echo $package_id; ?>" class="btn btn-info mt-2 w-100">View Tour Guide Details</a>
                    </div>

                    <div class="form-group">
                        <label for="tour_guide">Tour Guide</label>
                        <?php 
                        $guide_id = isset($_GET['guide_id']) ? (int)$_GET['guide_id'] : 0;
                        $guide_name = isset($_GET['guide_name']) ? $_GET['guide_name'] : '';
                        ?>
                        <input type="text" id="tour_guide" name="tour_guide" value="<?php echo htmlspecialchars($guide_name); ?>" readonly>
                        <input type="hidden" name="guide_id" value="<?php echo $guide_id; ?>">
                        <?php if (empty($guide_name)): ?>
                            <small class="form-text text-muted">Please select a tour guide by clicking the button above.</small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="season">Season</label>
                        <input type="text" id="season" name="season" value="<?php echo htmlspecialchars($season); ?>" readonly>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start_date">Start Date</label>
                            <input type="text" id="start_date" name="start_date" value="<?php echo date('d M Y', strtotime($start_date)); ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_date">End Date</label>
                            <input type="text" id="end_date" name="end_date" value="<?php echo date('d M Y', strtotime($end_date)); ?>" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="duration">Duration (days)</label>
                            <input type="text" id="duration" name="duration" value="<?php echo $duration; ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="difficulty">Difficulty Level</label>
                            <input type="text" id="difficulty" name="difficulty" value="<?php echo htmlspecialchars($difficulty); ?>" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="number_of_people">Number of People</label>
                        <input type="number" id="number_of_people" name="number_of_people" min="1" max="<?php echo $package['Max_People']; ?>" value="1" required>
                        <small class="form-text text-muted">Maximum <?php echo $package['Max_People']; ?> people allowed</small>
                    </div>

                    <div class="form-group">
                        <label for="original_price">Original Price ($)</label>
                        <input type="text" id="original_price" name="original_price" value="<?php echo number_format($package['Price'], 2); ?>" readonly>
                    </div>

                    <?php if ($loyalty): ?>
                    <div class="form-group loyalty-info">
                        <h4 class="loyalty-title">Loyalty Program Benefits</h4>
                        <div class="loyalty-details">
                            <div class="membership-level">
                                <div class="label">Membership Level:</div>
                                <div class="value">
                                    <span class="badge badge-<?php 
                                        echo $loyalty['Membership_Level'] === 'Platinum' ? 'primary' : 
                                            ($loyalty['Membership_Level'] === 'Gold' ? 'warning' : 
                                            ($loyalty['Membership_Level'] === 'Silver' ? 'secondary' : 'light')); 
                                    ?>">
                                        <?php echo $loyalty['Membership_Level']; ?>
                                    </span>
                                    <span class="discount-info">(<?php echo $membership_discount; ?>% discount)</span>
                                </div>
                            </div>
                            <div class="points-info">
                                <div class="label">Available Points:</div>
                                <div class="value">
                                    <span class="points"><?php echo $loyalty['Points']; ?></span>
                                    <span class="points-info-text">(10 points = 1% discount)</span>
                                </div>
                            </div>
                            <div class="use-points">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="use_points" name="use_points" style="margin-left: 10px; margin-top: 7px">
                                    <label class="form-check-label" for="use_points">Use points for additional discount</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="discounted_price">Discounted Price ($)</label>
                        <input type="text" id="discounted_price" name="discounted_price" value="<?php echo number_format($package['Discounted_Price'], 2); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="total_cost">Total Cost ($)</label>
                        <input type="text" id="total_cost" name="total_cost" value="<?php echo number_format($price, 2); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="coupon_code">Discount Coupon Code (Optional)</label>
                        <div class="input-group">
                            <input type="text" id="coupon_code" name="coupon_code" class="form-control" placeholder="Enter coupon code">
                            <div class="input-group-append">
                                <button type="button" id="apply_coupon" class="btn btn-primary">Apply</button>
                            </div>
                        </div>
                        <small id="coupon_message" class="form-text text-muted"></small>
                    </div>

                    <div class="form-group">
                        <label for="final_price">Final Price ($)</label>
                        <input type="text" id="final_price" name="final_price" readonly>
                        <input type="hidden" id="membership_discount" value="<?php echo $membership_discount; ?>">
                        <input type="hidden" id="available_points" value="<?php echo $loyalty ? $loyalty['Points'] : 0; ?>">
                    </div>

                    <style>
                        .loyalty-info {
                            background-color: #f8f9fa;
                            padding: 20px;
                            border-radius: 8px;
                            margin-bottom: 25px;
                            border: 1px solid #e9ecef;
                        }
                        .loyalty-title {
                            color: #333;
                            font-size: 18px;
                            margin-bottom: 15px;
                            font-weight: 600;
                        }
                        .loyalty-details {
                            display: flex;
                            flex-direction: column;
                            gap: 15px;
                        }
                        .membership-level, .points-info {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            padding: 10px 0;
                            border-bottom: 1px solid #e9ecef;
                        }
                        .membership-level:last-child, .points-info:last-child {
                            border-bottom: none;
                        }
                        .label {
                            font-weight: 500;
                            color: #495057;
                        }
                        .value {
                            display: flex;
                            align-items: center;
                            gap: 10px;
                        }
                        .badge {
                            padding: 6px 12px;
                            font-size: 14px;
                            font-weight: 500;
                        }
                        .badge-primary { background-color: #007bff; }
                        .badge-warning { background-color: #ffc107; color: #000; }
                        .badge-secondary { background-color: #6c757d; }
                        .badge-light { background-color: #f8f9fa; color: #000; }
                        .discount-info, .points-info-text {
                            color: #6c757d;
                            font-size: 14px;
                        }
                        .points {
                            font-weight: 600;
                            color: #28a745;
                        }
                        .use-points {
                            margin-top: 10px;
                            padding-top: 10px;
                            border-top: 1px solid #e9ecef;
                        }
                        .form-check {
                            margin: 0;
                        }
                        .form-check-input {
                            margin-top: 0.3rem;
                        }
                    </style>

                    <div class="form-group text-center">
                        <button type="button" id="confirmBooking" class="btn-submit">Confirm Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="assets/js/jquery-2.1.0.min.js"></script>
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script>
        $(document).ready(function() {
            let currentTotal = <?php echo $price; ?>;
            let appliedDiscount = 0;
            let appliedCouponId = null;

            // Calculate initial total cost based on default value (1 person)
            const initialDiscountedPrice = <?php echo $package['Discounted_Price']; ?>;
            currentTotal = initialDiscountedPrice * 1;
            $('#total_cost').val(currentTotal.toFixed(2));
            $('#final_price').val(currentTotal.toFixed(2));

            // Calculate total cost based on number of people
            $('#number_of_people').on('change', function() {
                const discountedPrice = <?php echo $package['Discounted_Price']; ?>;
                const people = parseInt($(this).val()) || 0;
                currentTotal = discountedPrice * people;
                $('#total_cost').val(currentTotal.toFixed(2));
                updateFinalPrice();
            });

            // Handle coupon application
            $('#apply_coupon').on('click', function() {
                const couponCode = $('#coupon_code').val().trim();
                if (!couponCode) {
                    $('#coupon_message').text('Please enter a coupon code').removeClass('text-success').addClass('text-danger');
                    return;
                }

                // Show loading message
                $('#coupon_message').text('Checking coupon...').removeClass('text-success text-danger').addClass('text-info');

                $.ajax({
                    url: 'check_coupon.php',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        coupon_code: couponCode,
                        package_id: <?php echo $package_id; ?>,
                        total_cost: currentTotal
                    },
                    success: function(response) {
                        if (response.success) {
                            appliedDiscount = response.discount_amount;
                            appliedCouponId = response.coupon_id;
                            $('#coupon_message').text(response.message).removeClass('text-danger text-info').addClass('text-success');
                            updateFinalPrice();
                        } else {
                            appliedDiscount = 0;
                            appliedCouponId = null;
                            $('#coupon_message').text(response.message).removeClass('text-success text-info').addClass('text-danger');
                            updateFinalPrice();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        console.error('Response:', xhr.responseText);
                        $('#coupon_message').text('Error checking coupon. Please try again.').removeClass('text-success text-info').addClass('text-danger');
                    }
                });
            });

            function updateFinalPrice() {
                let totalPrice = currentTotal;
                let discount = appliedDiscount;

                // Add points discount if checkbox is checked
                if ($('#use_points').is(':checked')) {
                    const availablePoints = parseFloat($('#available_points').val());
                    if (availablePoints > 0) {
                        const pointsDiscount = Math.min(availablePoints / 10, 10); // Max 10% discount from points
                        discount += (totalPrice * pointsDiscount / 100);
                    }
                }

                const finalPrice = totalPrice - discount;
                $('#final_price').val(finalPrice.toFixed(2));
                $('#discounted_price').val((totalPrice - discount).toFixed(2));
            }

            // Handle points checkbox change
            $('#use_points').on('change', function() {
                updateFinalPrice();
            });

            // Handle confirm booking button click
            $('#confirmBooking').on('click', function() {
                const finalPrice = $('#final_price').val();
                const numberOfPeople = $('#number_of_people').val();
                const packageId = <?php echo $package_id; ?>;
                const pricingId = <?php echo $pricing_id; ?>;
                const guideId = <?php echo isset($_GET['guide_id']) ? $_GET['guide_id'] : 0; ?>;
                const guideName = '<?php echo isset($_GET['guide_name']) ? urlencode($_GET['guide_name']) : ''; ?>';

                // Redirect to payment page with all necessary parameters
                window.location.href = `payment.php?amount=${finalPrice}&people=${numberOfPeople}&package_id=${packageId}&pricing_id=${pricingId}&guide_id=${guideId}&guide_name=${guideName}`;
            });
        });
    </script>
</body>
</html>