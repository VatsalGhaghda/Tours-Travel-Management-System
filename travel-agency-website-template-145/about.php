<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Travel Agency</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">

    <link rel="stylesheet" href="assets/css/about_us.css">
</head>
<body>

    <!-- Header -->
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
                    <li><a href="booking.php">Booking</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="tour_guide.php">Tour Guide</a></li>
                    <li><a href="about.php"  class="active">About us</a></li>

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

    <!-- About Section -->
    <section class="about">
        <div class="container">
            <h2>Who We Are</h2>
            <p>We are a passionate team of travel experts dedicated to creating memorable and personalized journeys for travelers worldwide.</p>
        </div>
    </section>

    <!-- Our Team -->
    <section class="team">
        <h2>Meet Our Team</h2>
        <div class="team-container">
            <div class="team-member">
                <img src="assets/images/ss4.jpg" alt="Bhavy Tanna">

                <h3>Bhavy Tanna</h3>
            </div>
            <div class="team-member">
                <img src="assets/images/20250327_190252_0000.jpg" alt="Vatsal Ghaghda">

                <h3>Vatsal Ghaghda</h3>
            </div>
            <div class="team-member">
                <img src="assets/images/ss4.jpg" alt="Jaiditya Chauhan">
                <h3>Jaiditya Chauhan</h3>
                
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <h2>Plan Your Next Adventure</h2>
        <p>Let us create an unforgettable experience for you.</p>
        <a href="contact.html" class="btn">Contact Us</a>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Travel Agency. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".team-member img").forEach(img => {
                img.addEventListener("click", function () {
                    let memberName = this.alt.toLowerCase().replace(/\s+/g, '-'); // Convert name to lowercase and replace spaces with "-"
                    window.location.href = memberName + ".php"; // Redirect to respective profile page
                });
            });
        });
    </script>
    
</body>
</html>
