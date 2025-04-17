<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>FAQ - Travel Agency</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/faq.css">
    <link rel="stylesheet" href="assets/css/nav.css">
    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    
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
                    <li><a href="faq.php" class="active">FAQ</a></li>
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

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="faq-container">
                <div class="section-heading">
                    <h2>Frequently Asked <em>Questions</em></h2>
                </div>
                <!-- FAQ Items -->
                <div class="faq-item" id="FAQ_ID_1">
                    <div class="faq-question">What is included in a tour package?</div>
                    <div class="faq-answer">
                        Our tour packages typically include accommodation, transportation, guided tours, and some meals. Specific inclusions depend on the package details, which you can find on the package page.
                    </div>
                </div>
                <div class="faq-item" id="FAQ_ID_2">
                    <div class="faq-question">How can I book a tour?</div>
                    <div class="faq-answer">
                        You can book a tour directly on our website by selecting a package, choosing your dates, and completing the booking form. Payment options will be provided during checkout.
                    </div>
                </div>
                <div class="faq-item" id="FAQ_ID_3">
                    <div class="faq-question">What is your cancellation policy?</div>
                    <div class="faq-answer">
                        Cancellations made 30 days before the tour start date are fully refundable. Within 30 days, a 50% fee may apply. Please check the specific package terms for details.
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

<!-- Custom JavaScript for Click Event -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');

            question.addEventListener('click', function() {
                // Check if the clicked item is already active
                const isActive = item.classList.contains('active');

                // Remove active class from all items
                faqItems.forEach(i => {
                    i.classList.remove('active');
                    i.querySelector('.faq-answer').style.maxHeight = '0';
                });

                // Toggle the clicked item
                if (!isActive) {
                    item.classList.add('active');
                    const answer = item.querySelector('.faq-answer');
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                }
            });
        });
    });
</script>


</body>
</html>

</body>
</html>