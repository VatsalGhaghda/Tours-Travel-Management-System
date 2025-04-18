<?php
session_start();
require_once 'includes/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $nationality = $_POST['nationality'];
    $password = $_POST['password'];

    // Check if email already exists
    $check_email = "SELECT Email FROM Customer WHERE Email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Email already exists. Please use a different email.']);
        exit();
    }

    // Check if phone number already exists
    $check_phone = "SELECT Phone FROM Customer WHERE Phone = ?";
    $stmt = $conn->prepare($check_phone);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Phone number already exists. Please use a different phone number.']);
        exit();
    }

    // If no duplicates found, proceed with registration
    $sql = "INSERT INTO Customer (Name, Email, Phone, Address, Date_Of_Birth, Nationality, Password) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $name, $email, $phone, $address, $dob, $nationality, $password);

    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Registration failed. Please try again.']);
        exit();
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Signup</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="./assets/css/signup.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .booking-section {
            padding: 80px 0;
        }

        .booking-container {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .section-heading h2 em {
            font-style: normal;
            color: #ed563b;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 500;
            color: #1e1e1e;
            margin-bottom: 5px;
            display: block;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
        }

        .password-input-container {
            position: relative;
            width: 100%;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
        }
        .toggle-password:hover {
            color: #ed563b;
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
                        <li><a href="login.php" class="active">Login</a></li>
                    <?php endif; ?>
                </ul>
                    <!-- ***** Menu End ***** -->
                </nav>
            </div>
        </div>
    </div>
</header>
<!-- ***** Header Area End ***** -->

    <section class="booking-section">
        <div class="container">
            <div class="booking-container">
                <div class="section-heading">
                    <h2>Sign <em>Up</em></h2>
                </div>
                <form id="signupForm" method="POST" action="signup.php" novalidate>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea id="address" name="address" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label>Nationality</label>
                        <select id="nationality" name="nationality" required>
                            <option value="">Select a country</option>
                            <option value="India">India</option>
                            <option value="USA">USA</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-input-container">
                            <input type="password" id="password" name="password" required minlength="8" maxlength="30">
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" value="Signup" class="btn-submit">
                        <p style="margin-top: 10px;">Already a user? <a href="login.php">Login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('signupForm').addEventListener('submit', function(event) {
            event.preventDefault();
            let fields = [
                { id: 'name', message: "Name is required" },
                { id: 'email', message: "Valid email is required" },
                { id: 'phone', message: "Phone number must be 10 digits" },
                { id: 'address', message: "Address is required" },
                { id: 'dob', message: "Date of birth is required" },
                { id: 'nationality', message: "Please select nationality" },
            ];

            // Check if user is at least 18 years old
            const dobInput = document.getElementById('dob');
            const dob = new Date(dobInput.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }

            if (age < 18) {
                Swal.fire({
                    title: 'Age Restriction',
                    text: 'You must be at least 18 years old to sign up',
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                return;
            }

            for (let field of fields) {
                let input = document.getElementById(field.id);
                if (!input.checkValidity()) {
                    Swal.fire({
                        title: 'Validation Error',
                        text: field.message,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    return;
                }
            }

            const email = document.getElementById('email');
            const emailValue = email.value;
            let messages = [];
            if (!emailValue.includes('@')) {
                messages.push("Email must contain '@'.");
            }
            if (!emailValue.endsWith('.com')) {
                messages.push("Email must end with '.com'.");
            }
            if (messages.length > 0) {
                Swal.fire({
                    title: 'Email Validation Error',
                    text: messages.join("\n"),
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                return;
            }

            const password = document.getElementById('password');
            const passwordValue = password.value;
            const passwordChecks = [
                { regex: /[a-z]/, message: "Must include at least one lowercase letter" },
                { regex: /[A-Z]/, message: "Must include at least one uppercase letter" },
                { regex: /\d/, message: "Must include at least one digit" },
                { regex: /[!@#$%^&*]/, message: "Must include at least one special character (!@#$%^&*)" }
            ];

            let passwordErrors = [];
            for (let check of passwordChecks) {
                if (!check.regex.test(passwordValue)) {
                    passwordErrors.push(check.message);
                }
            }

            if (passwordErrors.length > 0) {
                Swal.fire({
                    title: 'Password Requirements',
                    html: passwordErrors.join('<br>'),
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                return;
            }

            // Collect form data
            const formData = new FormData(this);

            // Submit the form using AJAX
            fetch('signup.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    return;
                }

                // Show success message and redirect
                Swal.fire({
                    title: 'Success!',
                    text: 'Your account has been created successfully!',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    willClose: () => {
                        // Show login success message before redirecting
                        Swal.fire({
                            title: 'Logging In...',
                            text: 'Redirecting to login page',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            willClose: () => {
                                window.location.href = 'login.php';
                            }
                        });
                    }
                });
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

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.classList.remove('fa-eye');
                toggleButton.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleButton.classList.remove('fa-eye-slash');
                toggleButton.classList.add('fa-eye');
            }
        }
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'92677c636aabbf78',t:'MTc0MzAwMDg5NC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>