<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">

    <title>Travel Agency - Tour Packages</title>

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/nav.css">
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
    <section class="section section-bg" id="call-to-action" style="background-image: url(assets/images/banner-image-1-1920x500.jpg)">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="cta-content">
                        <br><br>
                        <h2>Our <em>Packages</em></h2>
                        <p>Discover your next adventure with our carefully curated tour packages</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="trainers">
        <div class="container">
            <br><br>
            <div class="row">
                <div class="col-lg-4">
                    <div class="trainer-item">
                        <div class="image-thumb">
                            <img src="assets/images/1.jpg" alt="United Arab Emirates Tour">
                        </div>
                        <div class="down-content">
                            <span><sup>$</sup>2905.37</span>
                            <h4>United Arab Emirates Adventure</h4>
                            <p>
                                <i class="fa fa-calendar"></i> 29 days &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-users"></i> Max 15 people &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-map-marker"></i> UAE &nbsp;&nbsp;&nbsp;
                            </p>
                            <ul class="social-icons">
                                <li><a href="package-details.php?id=1">+ View Package</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="trainer-item">
                        <div class="image-thumb">
                            <img src="assets/images/2.jpg" alt="French Guiana Tour">
                        </div>
                        <div class="down-content">
                            <span><sup>$</sup>712.71</span>
                            <h4>French Guiana Explorer</h4>
                            <p>
                                <i class="fa fa-calendar"></i> 14 days &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-users"></i> Max 46 people &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-map-marker"></i> French Guiana &nbsp;&nbsp;&nbsp;
                            </p>
                            <ul class="social-icons">
                                <li><a href="package-details.php?id=2">+ View Package</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="trainer-item">
                        <div class="image-thumb">
                            <img src="assets/images/3.jpg" alt="Andorra Tour">
                        </div>
                        <div class="down-content">
                            <span><sup>$</sup>4157.75</span>
                            <h4>Andorra Discovery</h4>
                            <p>
                                <i class="fa fa-calendar"></i> 30 days &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-users"></i> Max 44 people &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-map-marker"></i> Andorra &nbsp;&nbsp;&nbsp;
                            </p>
                            <ul class="social-icons">
                                <li><a href="package-details.php?id=3">+ View Package</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="trainer-item">
                        <div class="image-thumb">
                            <img src="assets/images/4.jpg" alt="Guinea-Bissau Tour">
                        </div>
                        <div class="down-content">
                            <span><sup>$</sup>2876.17</span>
                            <h4>Guinea-Bissau Experience</h4>
                            <p>
                                <i class="fa fa-calendar"></i> 27 days &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-users"></i> Max 32 people &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-map-marker"></i> Guinea-Bissau &nbsp;&nbsp;&nbsp;
                            </p>
                            <ul class="social-icons">
                                <li><a href="package-details.php?id=4">+ View Package</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="trainer-item">
                        <div class="image-thumb">
                            <img src="assets/images/5.jpeg" alt="Montserrat Tour">
                        </div>
                        <div class="down-content">
                            <span><sup>$</sup>1216.16</span>
                            <h4>Montserrat Adventure</h4>
                            <p>
                                <i class="fa fa-calendar"></i> 4 days &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-users"></i> Max 16 people &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-map-marker"></i> Montserrat &nbsp;&nbsp;&nbsp;
                            </p>
                            <ul class="social-icons">
                                <li><a href="package-details.php?id=5">+ View Package</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="trainer-item">
                        <div class="image-thumb">
                            <img src="assets/images/6.jpeg" alt="South Georgia Tour">
                        </div>
                        <div class="down-content">
                            <span><sup>$</sup>1070.63</span>
                            <h4>South Georgia Island Explorer</h4>
                            <p>
                                <i class="fa fa-calendar"></i> 7 days &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-users"></i> Max 48 people &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-map-marker"></i> South Georgia &nbsp;&nbsp;&nbsp;
                            </p>
                            <ul class="social-icons">
                                <li><a href="package-details.php?id=6">+ View Package</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="trainer-item">
                        <div class="image-thumb">
                            <img src="assets/images/7.webp" alt="Svalbard Tour">
                        </div>
                        <div class="down-content">
                            <span><sup>$</sup>3690.38</span>
                            <h4>Svalbard & Jan Mayen Explorer</h4>
                            <p>
                                <i class="fa fa-calendar"></i> 5 days &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-users"></i> Max 17 people &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-map-marker"></i> Svalbard &nbsp;&nbsp;&nbsp;
                            </p>
                            <ul class="social-icons">
                                <li><a href="package-details.php?id=7">+ View Package</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="trainer-item">
                        <div class="image-thumb">
                            <img src="assets/images/8.jpg" alt="South Georgia Tour">
                        </div>
                        <div class="down-content">
                            <span><sup>$</sup>3258.11</span>
                            <h4>South Georgia Adventure</h4>
                            <p>
                                <i class="fa fa-calendar"></i> 21 days &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-users"></i> Max 26 people &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-map-marker"></i> South Georgia &nbsp;&nbsp;&nbsp;
                            </p>
                            <ul class="social-icons">
                                <li><a href="package-details.php?id=8">+ View Package</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="trainer-item">
                        <div class="image-thumb">
                            <img src="assets/images/9.jpg" alt="French Polynesia Tour">
                        </div>
                        <div class="down-content">
                            <span><sup>$</sup>926.90</span>
                            <h4>French Polynesia Discovery</h4>
                            <p>
                                <i class="fa fa-calendar"></i> 10 days &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-users"></i> Max 43 people &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-map-marker"></i> French Polynesia &nbsp;&nbsp;&nbsp;
                            </p>
                            <ul class="social-icons">
                                <li><a href="package-details.php?id=9">+ View Package</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

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
</body>
</html>