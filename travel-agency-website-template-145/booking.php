<?php
session_start();
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
    <!-- Header (same as before) -->
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
                    <li><a href="index.php" >Home</a></li>
                    <li><a href="packages.php">Packages</a></li>
                    <li><a href="booking.php" class="active">Booking</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="tour_guide.php">Tour Guide</a></li>
                    <li><a href="about.php">About us</a></li>

                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <!-- Show Profile Dropdown when Logged In -->
                        <li class="nav-item dropdown">
                            <a href="#" class="dropdown" id="profileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['name']); ?>
                            </a>
                            <div class="dropdown-menu custom-navbar-dropdown" aria-labelledby="profileDropdown">
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

    <!-- Booking Section (same as before) -->
    <section class="booking-section">
        <div class="container">
            <div class="booking-container">
                <div class="section-heading">
                    <h2>Book Your <em>Tour</em></h2>
                </div>
                <form id="bookingForm" action="#" method="POST">
                    <!-- <div class="form-group">
                        <label for="booking_id">Booking ID</label>
                        <input type="text" id="booking_id" name="booking_id" value="AUTO-GENERATED" readonly>
                    </div> -->
                    <div class="form-group">
                        <label for="customer_id">Customer Name </label>
                        <input type="text" id="number_of_people" name="number_of_people"  >
                        <!-- <select id="customer_id" name="customer_id" required>
                            <option value="" disabled selected>Select Customer</option>
                            <option value="CUST001">John Doe (CUST001)</option>
                            <option value="CUST002">Jane Smith (CUST002)</option>
                        </select> -->
                    </div>
                    <div class="form-group">
                        <label for="tourpackage_id">Tour Package</label>
                        <select id="tourpackage_id" name="tourpackage_id" >
                            <option value="" disabled selected>Select Tour Package</option>
                            <option value="TP001">Beach Getaway (TP001)</option>
                            <option value="TP002">Mountain Adventure (TP002)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="season">Season</label>
                        <select id="season" name="season" >
                            <option value="" disabled selected>Select Season </option>
                            <option value="Summer">Summer</option>
                            <option value="Winter">Winter</option>
                            <option value="Spring">Spring</option>
                            <option value="Autumn">Autumn</option>
                        </select>
                    </div>

                    <!-- <div class="mb-3">
                        <label for="season" class="form-label">Season</label>
                        <select class="form-select" id="season">
                            <option value="">Select Season</option>
                            <option value="Summer">Summer</option>
                            <option value="Winter">Winter</option>
                            <option value="Spring">Spring</option>
                            <option value="Autumn">Autumn</option>
                        </select>
                    </div> -->

                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate">
                    </div>
    
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate">
                    </div>

                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration (Days)</label>
                        <input type="text" class="form-control" id="duration" placeholder="Enter Duration">
                    </div>

                    <div class="mb-3">
                        <label for="difficultyLevel" class="form-label">Difficulty Level</label>
                        <select class="form-control" id="difficultyLevel">
                            <option value="Easy">Easy</option>
                            <option value="Moderate">Moderate</option>
                            <option value="Challenging">Challenging</option>
                            <option value="Difficult">Difficult</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="booking_date">Booking Date</label>
                        <input type="date" id="booking_date" name="booking_date" >
                    </div>
                    <div class="form-group">
                        
                    </div>
                    <div class="form-group">
                        <label for="number_of_people">Number of People</label>
                        <input type="number" id="number_of_people" name="number_of_people" min="1" >
                    </div>

                    <div class="mb-3">
                        <label for="discountedPrice" class="form-label">Discounted Code</label>
                        <input type="text" class="form-control" id="discountedCode" placeholder="Enter Discounted Code" min="0">
                    </div>

                    <div class="mb-3">
                        <label for="discountedPrice" class="form-label">Discounted Price</label>
                        <input type="text" class="form-control" id="discountedPrice" placeholder="Enter Discounted Price" min="0">
                    </div>

                    <div class="form-group">
                        <label for="total_cost">Total Cost ($)</label>
                        <input type="text" id="total_cost" name="total_cost" value="0.00" readonly>
                    </div>
                    <div class="form-group text-center">
                        <a href="payment.html" class="btn-submit">Book Tour</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Scripts (same as before) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="assets/js/jquery-2.1.0.min.js"></script>
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- <script src="assets/js/custom.js"></script> -->
    <script>
        $(document).ready(function() {
            $('#tourpackage_id, #number_of_people').on('change', function() {
                const packagePrices = {
                    'TP001': 300,
                    'TP002': 450
                };
                const packageId = $('#tourpackage_id').val();
                const people = parseInt($('#number_of_people').val()) || 0;
                const basePrice = packagePrices[packageId] || 0;
                const totalCost = basePrice * people;
                $('#total_cost').val(totalCost.toFixed(2));
            });
            const today = new Date().toISOString().split('T')[0];
            $('#booking_date').val(today);
        });
    </script>
</body>
</html>