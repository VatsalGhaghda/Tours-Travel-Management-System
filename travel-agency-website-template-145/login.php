<?php
session_start();

$host = "localhost";
$username = "root";
$password = "root";
$database = "TourTravelDB";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateShortHashedPassword($plainPassword) {
    return substr(sha1($plainPassword), 0, 16);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $plain_password = $_POST['password'];

    $sql = "SELECT Customer_ID, Name, Password FROM Customer WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $stored_hash = $row['Password'];
        $hashed_input = generateShortHashedPassword($plain_password);

        if ($hashed_input === $stored_hash) {
            $_SESSION['customer_id'] = $row['Customer_ID'];
            $_SESSION['name'] = $row['Name'];
            header("Location: index.php");
            exit();
        } else {
            echo "<script>
                    alert('Incorrect password. Please try again.');
                    window.location.href = 'login.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('User does not exist. Please check your email or sign up.');
                window.location.href = 'login.php';
              </script>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Travel Agency</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="./assets/css/login.css">
</head>
<body>
    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <a href="index.php" class="logo">Travel Agency</a>
                        <ul class="nav">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="packages.html">Packages</a></li>
                            <li><a href="booking.html">Booking</a></li>
                            <li><a href="faq.html">FAQ</a></li>
                            <li><a href="tour_guide.html">Tour Guide</a></li>
                            <li><a href="signup.html" class="active">Login/Signup</a></li>
                        </ul>
                        <a class='menu-trigger'><span>Menu</span></a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <section class="booking-section">
        <div class="container">
            <div class="booking-container">
                <div class="section-heading">
                    <h2>Login to Your <em>Account</em></h2>
                </div>
                <form id="loginForm" method="POST" action="login.php" novalidate>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required minlength="8">
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" value="Login" class="btn-submit">
                        <p style="margin-top: 10px;">Not a member? <a href="signup.html">Sign up</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="assets/js/jquery-2.1.0.min.js"></script>
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            let valid = true;

            function showError(field, message) {
                field.setCustomValidity(message);
                field.reportValidity();
                valid = false;
            }

            function clearError(field) {
                field.setCustomValidity("");
            }

            const email = document.getElementById("email");
            const emailValue = email.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (emailValue === "") {
                showError(email, "Email is required.");
            } else if (!emailValue.includes('@')) {
                showError(email, "Email must contain '@' symbol.");
            } else if (!emailValue.includes('.')) {
                showError(email, "Email must contain a domain with a dot.");
            } else if (!emailRegex.test(emailValue)) {
                showError(email, "Please enter a valid email address.");
            } else {
                clearError(email);
            }

            const password = document.getElementById("password");
            if (password.value.trim() === "") {
                showError(password, "Password is required.");
            } else {
                clearError(password);
            }

            if (!valid) event.preventDefault();
        });
    </script>
</body>
</html>
