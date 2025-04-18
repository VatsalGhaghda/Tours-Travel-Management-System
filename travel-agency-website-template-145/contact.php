<?php
session_start();
require_once 'includes/db_connection.php';

// Handle FAQ question submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faq_question = $_POST['faq_question'] ?? '';
    
    if (!empty($faq_question)) {
        $insert_faq = "INSERT INTO FAQ (Question, Answer) VALUES (?, 'Pending review')";
        $stmt = $conn->prepare($insert_faq);
        $stmt->bind_param("s", $faq_question);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Thank you for your question! We will review it and add it to our FAQs soon.']);
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Failed to submit question. Please try again.']);
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Submit FAQ Question - Travel Agency</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/nav.css">
    <style>
        .faq-form {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }
        .success-message {
            color: #28a745;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #d4edda;
            border-radius: 5px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .main-button {
            background-color: #ed563b;
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .main-button:hover {
            background-color: #f9735b;
        }
        .contact-info {
            text-align: center;
            margin-top: 30px;
        }
        .contact-info .icon {
            margin-bottom: 15px;
        }
        .contact-info h5 {
            margin-bottom: 30px;
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
                    <li><a href="contact.php"  class="active">Contact Us</a></li>
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
                        <br>
                        <br>
                        <h2>Submit Your <em>Question</em></h2>
                        <p>Have a question you'd like to see in our FAQ? Ask it here!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="faq-form">
                        <?php if (isset($success_message)): ?>
                            <div class="success-message"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        
                        <form action="" method="post">
                            <textarea name="faq_question" rows="5" placeholder="Type your question here..." required></textarea>
                            <button type="submit" class="main-button">Submit Question</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-info">
                        <div class="icon">
                            <i class="fa fa-phone"></i>
                        </div>
                        <h5><a href="#">+1 333 4040 5566</a></h5>
                        <div class="icon">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <h5><a href="#">contact@company.com</a></h5>
                        <div class="icon">
                            <i class="fa fa-map-marker"></i>
                        </div>
                        <h5>212 Barrington Court New York</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- jQuery -->
    <script src="assets/js/jquery-2.1.0.min.js"></script>
    <!-- Bootstrap -->
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        // Clear the textarea
                        document.querySelector('textarea').value = '';
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });
        });
    </script>
</body>
</html>