<?php
session_start();
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
    </style>
</head>
<body>

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
                    <li><a href="index.php" >Home</a></li>
                    <li><a href="packages.php">Packages</a></li>
                    <li><a href="booking.php">Booking</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="tour_guide.php" class="active">Tour Guide</a></li>
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


    <div class="container">
        <div class="guide-container section-heading">
            <h2>Tour Guide <em>Information</em></h2>
            <form>

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter Full Name">
                </div>

                <!-- <div class="mb-3">
                    <label for="languages" class="form-label">Languages</label>
                    <select class="form-select" id="languages" multiple>
                        <option value="English">English</option>
                        <option value="Spanish">Spanish</option>
                        <option value="French">French</option>
                        <option value="German">German</option>
                        <option value="Mandarin">Mandarin</option>
                        <option value="Hindi">Hindi</option>
                    </select>
                </div> -->

                <div class="mb-3">
                    <label for="languages">Language </label>
                    <select id="languages" name="season" required>
                        <option value="English">English</option>
                        <option value="Hindi">Hindi</option>
                        <option value="Spanish">Spanish</option>
                        <option value="French">French</option>
                        
                    </select>
                </div>

                <div class="mb-3">
                    <label for="experience" class="form-label">Experience (Years)</label>
                    <input type="number" class="form-control" id="experience" placeholder="Enter Years of Experience" min="0">
                </div>

                <div class="mb-3">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea class="form-control" id="bio" rows="3" placeholder="Short description about the guide"></textarea>
                </div>

                <div class="mb-3">
                    <label for="contactNumber" class="form-label">Contact Number</label>
                    <input type="tel" class="form-control" id="contactNumber" placeholder="Enter Contact Number">
                </div>


                <button type="submit" class="btn btn-primary w-100">Submit Tour Guide</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
