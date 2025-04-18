<?php
session_start();
require_once 'includes/db_connection.php';

$package_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch package details
$sql = "SELECT * FROM TourPackage WHERE TourPackage_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();
$package = $result->fetch_assoc();

if (!$package) {
    header("Location: packages.php");
    exit();
}

// Fetch available dates and pricing
$sql = "SELECT * FROM TourPackagePricing 
        WHERE TourPackage_ID = ? 
        ORDER BY Start_Date";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $package_id);
$stmt->execute();
$dates_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">

    <title>PHPJabbers.com | Free Travel Agency Website Template</title>

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">

    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="assets/css/nav.css">

</head>

<body>

    <!-- ***** Preloader Start ***** -->
    <!-- <div id="js-preloader" class="js-preloader">
      <div class="preloader-inner">
        <span class="dot"></span>
        <div class="dots">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    </div> -->
    <!-- ***** Preloader End ***** -->


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
                    <li><a href="packages.php" class="active">Packages</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="about.php">About us</a></li>

                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <!-- Show Profile Dropdown when Logged In -->
                        <li class="nav-item dropdown">
                            <a href="#" class="dropdown" id="profileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

    <!-- ***** Call to Action Start ***** -->
    <section class="section section-bg" id="call-to-action" style="background-image: url(assets/images/banner-image-1-1920x500.jpg)">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="cta-content">
                        <br><br>
                        <h2><em>$<?php echo number_format($package['Price'], 2); ?></em></h2>
                        <p><?php echo htmlspecialchars($package['Name']); ?></p>
                        <div class="main-button">
                            <?php if (isset($_SESSION['customer_id'])): ?>
                                <a href="javascript:void(0)" onclick="scrollToAvailability()" class="scroll-to-availability">Book Now</a>
                            <?php else: ?>
                                <a href="login.php">Login to Book</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="trainers">
        <div class="container">
            <br><br>
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img class="d-block w-100" src="<?php echo htmlspecialchars($package['Image_URL']); ?>" alt="<?php echo htmlspecialchars($package['Name']); ?>">
                    </div>
                </div>
            </div>

            <br><br>

            <div class="row" id="tabs">
                <div class="col-lg-4">
                    <ul>
                        <li><a href='#tabs-1'><i class="fa fa-cog"></i> Package Info</a></li>
                        <li><a href='#tabs-2'><i class="fa fa-gift"></i> Package Description</a></li>
                        <li><a href='#tabs-3'><i class="fa fa-plus-circle"></i> Availability &amp; Prices</a></li>
                    </ul>
                </div>
                <div class="col-lg-8">
                    <section class='tabs-content' style="width: 100%;">
                        <article id='tabs-1'>
                            <h4>Package Info</h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Duration</label>
                                    <p><?php echo $package['Duration']; ?> days</p>
                                </div>

                                <div class="col-sm-6">
                                    <label>Max People</label>
                                    <p><?php echo $package['Max_People']; ?> persons</p>
                                </div>

                                <div class="col-sm-6">
                                    <label>Difficulty Level</label>
                                    <p><?php echo $package['Difficulty_Level']; ?></p>
                                </div>
                            </div>
                        </article>

                        <article id='tabs-2'>
                            <h4>Package Description</h4>
                            <p><?php echo nl2br(htmlspecialchars($package['Description'])); ?></p>
                        </article>

                        <article id='tabs-3'>
                            <div id="availability-section">
                                <h4>Availability &amp; Prices</h4>
                                <div class="table-responsive">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
                                        <thead>
                                            <tr>
                                                <th>Season</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Original Price</th>
                                                <th>Discounted Price</th>
                                                <?php if (isset($_SESSION['customer_id'])): ?>
                                                    <th>Action</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($dates_result->num_rows > 0) {
                                                while ($date = $dates_result->fetch_assoc()) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($date['Season']); ?></td>
                                                        <td><?php echo date('d M Y', strtotime($date['Start_Date'])); ?></td>
                                                        <td><?php echo date('d M Y', strtotime($date['End_Date'])); ?></td>
                                                        <td>$<?php echo number_format($date['Price'], 2); ?></td>
                                                        <td>$<?php echo number_format($date['Discounted_Price'], 2); ?></td>
                                                        <?php if (isset($_SESSION['customer_id'])): ?>
                                                            <td>
                                                                <a href="booking.php?package_id=<?php echo $package_id; ?>&pricing_id=<?php echo $date['Pricing_ID']; ?>&season=<?php echo urlencode($date['Season']); ?>&start_date=<?php echo urlencode($date['Start_Date']); ?>&end_date=<?php echo urlencode($date['End_Date']); ?>&duration=<?php echo $package['Duration']; ?>&difficulty=<?php echo urlencode($package['Difficulty_Level']); ?>&price=<?php echo $date['Discounted_Price']; ?>" class="btn btn-primary btn-sm">Book Now</a>
                                                            </td>
                                                        <?php endif; ?>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="<?php echo isset($_SESSION['customer_id']) ? '6' : '5'; ?>" class="text-center">
                                                        No available dates at the moment.
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </article>
                    </section>
                </div>
            </div>
        </div>
    </section>

<?php
$stmt->close();
$conn->close();
?>
    <!-- jQuery -->
    <script src="assets/js/jquery-2.1.0.min.js"></script>

    <!-- Bootstrap -->
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Plugins -->
    <script src="assets/js/scrollreveal.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/imgfix.min.js"></script> 
    <script src="assets/js/mixitup.js"></script> 
    <script src="assets/js/accordions.js"></script>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Global Init -->
    <script>
        function scrollToAvailability() {
            const element = document.getElementById('availability-section');
            if (element) {
                // First, make sure the tabs-3 is active
                const tabLinks = document.querySelectorAll('#tabs li a');
                tabLinks.forEach(link => {
                    if (link.getAttribute('href') === '#tabs-3') {
                        link.click();
                    }
                });
                
                // Then scroll to the element
                setTimeout(() => {
                    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        }
    </script>

</body>
</html>
