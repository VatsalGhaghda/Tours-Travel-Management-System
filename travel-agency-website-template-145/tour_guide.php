<?php
session_start();
require_once 'includes/db_connection.php';

// Fetch tour guide details based on package_id if provided
$package_id = isset($_GET['package_id']) ? (int)$_GET['package_id'] : 0;

$guide_sql = "SELECT * FROM TourGuide WHERE TourPackage_ID = ?";
$guide_stmt = $conn->prepare($guide_sql);
$guide_stmt->bind_param("i", $package_id);
$guide_stmt->execute();
$guide_result = $guide_stmt->get_result();
$guides = $guide_result->fetch_all(MYSQLI_ASSOC);
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
    <link rel="stylesheet" type="text/css" href="./assets/css/tour_guide.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/nav.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .guide-container {
            max-width: 650px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }

        .guide-container h2 {
            text-align: center;
            color: #232d39;
            font-weight: 700;
        }

        .btn-primary {
            background: #ed563b;
            border: none;
        }

        .btn-primary:hover {
            background: #c94c32;
        }

        .guide-card {
            transition: transform 0.3s ease;
        }
        .guide-card:hover {
            transform: translateY(-5px);
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .badge {
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: 500;
        }
        .bg-success {
            background-color: #28a745 !important;
            color: white;
        }
        .bg-danger {
            background-color: #dc3545 !important;
            color: white;
        }
        .guide-info p {
            margin-bottom: 10px;
        }
    </style>
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

    <div class="container">
        <div class="guide-container section-heading">
            <h2>Tour Guide <em>Information</em></h2>
            <?php if (empty($guides)): ?>
                <div class="alert alert-info">No tour guides found for this package.</div>
            <?php else: ?>
                <?php foreach ($guides as $guide): ?>
                    <div class="guide-card mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($guide['Name']); ?></h5>
                                <div class="guide-info">
                                    <p><strong>Languages:</strong> <?php echo htmlspecialchars($guide['Languages']); ?></p>
                                    <p><strong>Experience:</strong> <?php echo htmlspecialchars($guide['Experience']); ?> years</p>
                                    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($guide['Contact_Number']); ?></p>
                                    <p><strong>Availability Status:</strong> 
                                        <span class="badge <?php echo $guide['Availability_Status'] == 'Available' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo htmlspecialchars($guide['Availability_Status']); ?>
                                        </span>
                                    </p>
                                </div>
                                <?php if ($guide['Availability_Status'] == 'Available'): ?>
                                    <?php
                                    // Get all parameters from URL, with fallback to empty string if not present
                                    $pricing_id = isset($_GET['pricing_id']) ? $_GET['pricing_id'] : '';
                                    $season = isset($_GET['season']) ? urlencode($_GET['season']) : '';
                                    $start_date = isset($_GET['start_date']) ? urlencode($_GET['start_date']) : '';
                                    $end_date = isset($_GET['end_date']) ? urlencode($_GET['end_date']) : '';
                                    $duration = isset($_GET['duration']) ? $_GET['duration'] : '';
                                    $difficulty = isset($_GET['difficulty']) ? urlencode($_GET['difficulty']) : '';
                                    $price = isset($_GET['price']) ? $_GET['price'] : '';
                                    
                                    // Build the URL with all parameters
                                    $booking_url = "booking.php?package_id=" . $package_id;
                                    if ($pricing_id) $booking_url .= "&pricing_id=" . $pricing_id;
                                    if ($season) $booking_url .= "&season=" . $season;
                                    if ($start_date) $booking_url .= "&start_date=" . $start_date;
                                    if ($end_date) $booking_url .= "&end_date=" . $end_date;
                                    if ($duration) $booking_url .= "&duration=" . $duration;
                                    if ($difficulty) $booking_url .= "&difficulty=" . $difficulty;
                                    if ($price) $booking_url .= "&price=" . $price;
                                    $booking_url .= "&guide_id=" . $guide['Guide_ID'] . "&guide_name=" . urlencode($guide['Name']);
                                    ?>
                                    <a href="<?php echo $booking_url; ?>" class="btn btn-primary w-100 mt-3">Select Guide</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100 mt-3" disabled>Guide Unavailable</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- jQuery -->
    <script src="assets/js/jquery-2.1.0.min.js"></script>

    <!-- Bootstrap -->
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
     
</body>
</html>
