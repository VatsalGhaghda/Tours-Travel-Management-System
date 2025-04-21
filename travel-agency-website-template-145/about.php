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
    <link rel="stylesheet" href="assets/css/nav.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
         /* Custom styles for the new YETI-inspired section */
         .yeti-style-section {
            display: flex;
            justify-content: space-between;
            margin: 40px 0;
            padding: 20px;
            background-color: #f9f9f9;
            gap: 30px;
        }

        .yeti-style-card {
            text-align: center;
            padding: 30px;
            background-color: #fff;
            border: none;
            border-radius: 15px;
            width: 30%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .yeti-style-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .yeti-style-card img {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .yeti-style-card:hover img {
            transform: scale(1.05);
        }

        .yeti-style-card h3 {
            font-size: 1.5em;
            margin: 15px 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .yeti-style-card p {
            font-size: 1em;
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .yeti-style-card .highlight {
            color: #e74c3c;
            font-weight: bold;
        }

        .yeti-style-card hr {
            border: none;
            height: 2px;
            background: linear-gradient(to right, transparent, #3498db, transparent);
            margin: 20px 0;
        }

        .yeti-style-card ul.languages {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .yeti-style-card ul.languages li {
            margin: 5px;
            padding: 8px 15px;
            background-color: #f8f9fa;
            border-radius: 25px;
            font-size: 0.9em;
            color: #2c3e50;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .yeti-style-card ul.languages li:hover {
            background-color: #3498db;
            color: #fff;
            transform: translateY(-2px);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 25px;
        }

        .linkedin-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background-color: #0A66C2;
            color: white;
            border-radius: 50%;
            font-size: 20px;
            transition: transform 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-decoration: none;
        }

        .linkedin-btn:hover {
            transform: scale(1.2);
            color: white;
        }

        .linkedin-btn i {
            color: white;
            transition: color 0.3s ease;
        }

        .linkedin-btn:hover i {
            color: white;
        }

        .github-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background-color: #333;
            color: white;
            border-radius: 50%;
            font-size: 20px;
            transition: transform 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-decoration: none;
        }

        .github-btn:hover {
            transform: scale(1.2);
            color: white;
        }

        .github-btn i {
            color: white;
            transition: color 0.3s ease;
        }

        .github-btn:hover i {
            color: white;
        }

        @media (max-width: 992px) {
            .yeti-style-section {
                flex-direction: column;
                align-items: center;
            }

            .yeti-style-card {
                width: 80%;
                margin-bottom: 30px;
            }
        }

        @media (max-width: 768px) {
            .yeti-style-card {
                width: 95%;
            }
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
                    <li><a href="about.php" class="active">About us</a></li>

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

    <!-- About Section -->
    <section class="about">
        <div class="container" style="margin-top: 80px;">
            <h2>Who We Are</h2>
            <p>We are a passionate team of travel experts dedicated to creating memorable and personalized journeys for travelers worldwide.</p>
        </div>
    </section>

    <!-- New YETI-inspired Section with Team Data -->
    <section class="yeti-style-section">
        <div class="yeti-style-card">
            <img src="assets/images/bhavy.jpg" alt="Bhavy Tanna" style="width: 300px; height:350px; border-radius: 50%; object-fit: cover;">
            <h3>Bhavy Tanna</h3>
            <p><span class="highlight">Travel Enthusiast</span> with a flair for crafting unique itineraries that bring your dream vacations to life. As an <span class="highlight">Application Developer</span>, Bhavy blends technology with travel expertise to enhance your journey.</p>
            <h3><b>Education</b></h3>
            <hr>
            <p><span class="highlight">Diploma in Information Technology</span> - Gujarat Technological University (2021 - 2024)</p>
            <h3><b>Languages & Skills</b></h3>
            <hr>
            <ul class="languages">
                <li>Java</li>
                <li>C Language</li>
                <li>Android</li>
                <li>Python</li>
                <li>Flutter</li>
                <li> SQL</li>
            </ul>
            <h3><b>Hobbies</b></h3>
            <hr>
            <!-- <p>Bhavy unwinds with a variety of passions: <span class="highlight">Drawing</span> to spark creativity, <span class="highlight">Coding</span> for innovation, playing <span class="highlight">musical instruments</span> for harmony, and diving into <span class="highlight">eSports</span> for excitement.</p> -->
            <ul class="languages">
                <li> Drawing</li>
                <li>Coding</li>
                <li>Playing musical instrument</li>
                <li>Sports</li>
                
            </ul>
            <a href="https://www.linkedin.com/in/bhavy-tanna-453538243/" class="linkedin-btn" target="_blank">
                <i class="fab fa-linkedin"></i>
            </a>
            
            <!-- Add margin/space between icons -->
            <span style="margin: 0 10px;"></span>

            <a href="https://github.com/bhavytanna" class="github-btn" target="_blank">
                <i class="fab fa-github"></i>
            </a>

        </div>
        <div class="yeti-style-card">
            <img src="assets/images/vatsal.jpg" alt="Vatsal Ghaghda" style="width: 300px; height: 350px; border-radius: 50%; object-fit: cover;">
            <h3>Vatsal Ghaghda</h3>
            <p><span class="highlight">Backend Developer</span> with a passion for building robust systems that power seamless travel experiences. Vatsal excels in adventure tours and destination planning.</p>
            <h3><b>Education</b></h3>
            <hr>
            <p><span class="highlight">Diploma in Computer Science</span> - Marwadi University (2021 - 2024)</p>
            <h3><b>Languages & Skills</b></h3>
            <hr>
            <ul class="languages">
                <li>HTML & CSS</li>
                <li>JavaScript</li>
                <li>Java</li>
                <li>C Language</li>
                <li>C++</li>
                <li>SQL</li>
                <li>Python</li>
            </ul>
            <h3><b>Hobbies</b></h3>
            <hr>
            <ul class="languages">
                <li>Travelling</li>
                <li>Reading</li>
            </ul>

            <!-- For Vatsal's card -->
            <a href="https://www.linkedin.com/in/vatsal-ghaghda/" class="linkedin-btn" target="_blank">
                <i class="fab fa-linkedin"></i>
            </a>
            
            <span style="margin: 0 10px;"></span>

            <a href="https://github.com/vatsalghaghda" class="github-btn" target="_blank">
                <i class="fab fa-github"></i>
            </a>        
        </div>
        <div class="yeti-style-card">
            <img src="assets/images/jaiditya.jpg" alt="Jaiditya Chauhan" style="width: 300px; height: 350px; border-radius: 50%; object-fit: cover;">
            <h3>Jaiditya Chauhan</h3>
            <p><span class="highlight">Full Stack Developer</span> with expertise in creating end-to-end solutions, specializing in cultural tours and historical site visits.</p>
            <h3><b>Education</b></h3>
            <hr>
            <p><span class="highlight">Diploma in Computer Science</span> - Gujarat Technological University (2021 - 2024)</p>
            <h3><b>Languages & Skills</b></h3>
            <hr>
            <ul class="languages">
                <li>HTML & CSS</li>
                <li>JavaScript</li>
                <li>Java</li>
                <li>C Language</li>
                <li>SQL/NO SQL</li>
                <li>ASP.net</li>
            </ul>
            <h3><b>Hobbies</b></h3>
            <hr>
            <ul class="languages">
                <li> Travelling</li>
                <li>Reading</li>
               
                
            </ul>
            <a href="https://www.linkedin.com/in/jaiditya-chauhan-a2a58b298/" class="linkedin-btn" target="_blank">
                <i class="fab fa-linkedin"></i>
            </a>

            <span style="margin: 0 10px;"></span>

            <a href="https://github.com/Jaiditya-01" class="github-btn" target="_blank">
                <i class="fab fa-github"></i>
            </a>   
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".team-member img").forEach(img => {
                img.addEventListener("click", function () {
                    let memberName = this.alt.toLowerCase().replace(/\s+/g, '-');
                    window.location.href = memberName + ".php";
                });
            });
        });
    </script>
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Add these Bootstrap JS files -->
    <script src="assets/js/jquery-2.1.0.min.js"></script>
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    
</body>
</html>