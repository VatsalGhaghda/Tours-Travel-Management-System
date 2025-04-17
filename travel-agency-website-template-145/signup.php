<?php
$host = "localhost";
$username = "root";
$password = "1234";
$database = "TourTravelDB";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateShortHashedPassword($plainPassword) {
    return substr(sha1($plainPassword), 0, 16);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $nationality = $_POST['nationality'];
    $plain_password = $_POST['password'];
    $hashed_password = generateShortHashedPassword($plain_password);

    // Check if email or phone already exists
    $check_sql = "SELECT Email, Phone FROM Customer WHERE Email = ? OR Phone = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $email, $phone);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['Email'] === $email) {
            echo "<script>alert('Error: Email already exists.'); window.location.href = 'signup.html';</script>";
        } elseif ($row['Phone'] === $phone) {
            echo "<script>alert('Error: Phone number already exists.'); window.location.href = 'signup.html';</script>";
        }
        $check_stmt->close();
    } else {
        $sql = "INSERT INTO Customer (Name, Email, Phone, Address, Date_Of_Birth, Nationality, User_Type, Password) 
                VALUES (?, ?, ?, ?, ?, ?, 'Regular', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $name, $email, $phone, $address, $dob, $nationality, $hashed_password);

        if ($stmt->execute()) {
            header("Location: login.html"); // Redirect to login page after signup
            exit();
        } else {
            echo "<script>alert('Error: Registration failed.'); window.location.href = 'signup.html';</script>";
        }
        $stmt->close();
    }
}

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
    </style>
</head>
<body>
    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <a href="index.php" class="logo">Travel Agency </a>
                        <ul class="nav">
                            <li ><a href="index.php">Home</a></li>
                            <li><a href="packages.">Packages</a></li>
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
                        <input type="password" id="password" name="password" required minlength="8" maxlength="30">
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" value="Signup" class="btn-submit">
                        <p style="margin-top: 10px;">Already a user? <a href="login.html">Login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const fields = [
                { id: 'name', message: "Name is required" },
                { id: 'email', message: "Valid email is required" },
                { id: 'phone', message: "Phone number must be 10 digits" },
                { id: 'address', message: "Address is required" },
                { id: 'dob', message: "Date of birth is required" },
                { id: 'nationality', message: "Please select nationality" },
            ];

            for (let field of fields) {
                let input = document.getElementById(field.id);
                input.setCustomValidity("");
                if (!input.checkValidity()) {
                    input.setCustomValidity(field.message);
                    input.reportValidity();
                    return;
                }
            }

            const email = document.getElementById('email');
            email.addEventListener('input', function () {
                const emailValue = email.value;
                let messages = [];
                if (!emailValue.includes('@')) {
                    messages.push("Email must contain '@'.");
                }
                if (!emailValue.endsWith('.com')) {
                    messages.push("Email must end with '.com'.");
                }
                if (messages.length > 0) {
                    email.setCustomValidity(messages.join("\n"));
                } else {
                    email.setCustomValidity("");
                }
                email.reportValidity();
            });

            const password = document.getElementById('password');
            const passwordValue = password.value;
            const passwordChecks = [
                { regex: /[a-z]/, message: "Must include at least one lowercase letter" },
                { regex: /[A-Z]/, message: "Must include at least one uppercase letter" },
                { regex: /\d/, message: "Must include at least one digit" },
                { regex: /[!@#$%^&*]/, message: "Must include at least one special character (!@#$%^&*)" }
            ];

            for (let check of passwordChecks) {
                if (!check.regex.test(passwordValue)) {
                    password.setCustomValidity(check.message);
                    password.reportValidity();
                    return;
                }
            }

            this.submit();
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'92677c636aabbf78',t:'MTc0MzAwMDg5NC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>