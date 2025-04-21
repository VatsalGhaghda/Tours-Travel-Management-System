<?php
session_start();

$host = "localhost";
$username = "root";
$password = "qwepoi"; // Replace with your actual DB password
$database = "TourTravelDB";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['registerUsername']) ? filter_var($_POST['registerUsername'], FILTER_SANITIZE_STRING) : '';
    $email = isset($_POST['registerEmail']) ? filter_var($_POST['registerEmail'], FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['registerPhone']) ? filter_var($_POST['registerPhone'], FILTER_SANITIZE_STRING) : '';
    $plain_password = isset($_POST['registerPassword']) ? $_POST['registerPassword'] : '';
    $terms_agreed = isset($_POST['license']) && $_POST['license'] === 'on';

    // Validate inputs
    if (empty($username) || empty($email) || empty($phone) || empty($plain_password)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (!preg_match('/^\+?[1-9]\d{1,14}$/', $phone)) {
        $error_message = "Please enter a valid phone number (e.g., +1234567890).";
    } elseif (strlen($plain_password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif (!$terms_agreed) {
        $error_message = "You must agree to the terms and policy.";
    } else {
        // Check if email or phone already exists
        $sql = "SELECT Email, Phone FROM Customer WHERE Email = ? OR Phone = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['Email'] === $email) {
                $error_message = "This email is already registered.";
            } elseif ($row['Phone'] === $phone) {
                $error_message = "This phone number is already registered.";
            }
            $stmt->close();
        } else {
            $stmt->close();
            // Hash password with SHA-1, truncate to 16 characters
            $hashed_password = substr(sha1($plain_password), 0, 16);
            // Insert new admin user
            $sql = "INSERT INTO Customer (Name, Email, Phone, Password, User_Type) VALUES (?, ?, ?, ?, 'Admin')";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $error_message = "Prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param("ssss", $username, $email, $phone, $hashed_password);
                if ($stmt->execute()) {
                    $success_message = "Registration successful! Redirecting to login...";
                    // Debug: Verify User_Type
                    $sql = "SELECT User_Type FROM Customer WHERE Email = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        if ($row['User_Type'] !== 'Admin') {
                            $error_message = "Debug: User_Type not set to 'Admin'. Got: " . ($row['User_Type'] ?? 'NULL');
                            $success_message = '';
                        }
                    } else {
                        $error_message = "Debug: Could not verify User_Type.";
                        $success_message = '';
                    }
                    $stmt->close();
                    if (!$error_message) {
                        header("Refresh: 3; url=login.php");
                    }
                } else {
                    $error_message = "Error creating account: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bootstrap Material Admin by Bootstrapious.com</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="css/fontastic.css">
    <!-- Google fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,700">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="img/favicon.ico">
    <!-- Tweaks for older IEs-->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
  </head>
  <body>
    <div class="page login-page">
      <div class="container d-flex align-items-center">
        <div class="form-holder has-shadow">
          <div class="row">
            <!-- Logo & Information Panel-->
            <div class="col-lg-6">
              <div class="info d-flex align-items-center">
                <div class="content">
                  <div class="logo">
                    <h1>Admin Signup</h1>
                  </div>
                  <p>Create an admin account for the TourTravel Dashboard.</p>
                </div>
              </div>
            </div>
            <!-- Form Panel -->
            <div class="col-lg-6 bg-white">
              <div class="form d-flex align-items-center">
                <div class="content">
                  <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                      <?php echo htmlspecialchars($error_message); ?>
                    </div>
                  <?php endif; ?>
                  <?php if ($success_message): ?>
                    <div class="alert alert-success" role="alert">
                      <?php echo htmlspecialchars($success_message); ?>
                    </div>
                  <?php endif; ?>
                  <form id="register-form" method="post" action="register.php">
                    <div class="form-group">
                      <input id="register-username" type="text" name="registerUsername" required class="input-material">
                      <label for="register-username" class="label-material">User Name</label>
                    </div>
                    <div class="form-group">
                      <input id="register-email" type="email" name="registerEmail" required class="input-material">
                      <label for="register-email" class="label-material">Email Address</label>
                    </div>
                    <div class="form-group">
                      <input id="register-phone" type="tel" name="registerPhone" required class="input-material">
                      <label for="register-phone" class="label-material">Phone Number</label>
                    </div>
                    <div class="form-group">
                      <input id="register-passowrd" type="password" name="registerPassword" required class="input-material">
                      <label for="register-passowrd" class="label-material">Password</label>
                    </div>
                    <div class="form-group terms-conditions">
                      <input id="license" type="checkbox" name="license" class="checkbox-template">
                      <label for="license">Agree to the terms and policy</label>
                    </div>
                    <input id="register" type="submit" value="Register" class="btn btn-primary">
                  </form>
                  <small>Already have an account? </small><a href="login.php" class="signup">Login</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="copyrights text-center">
        <p>Design by <a href="https://bootstrapious.com" class="external">Bootstrapious</a></p>
      </div>
    </div>
    <!-- Javascript files-->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="vendor/popper.js/umd/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/jquery.cookie/jquery.cookie.js"></script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
    <!-- Main File-->
    <script src="js/front.js"></script>
  </body>
</html>