<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Payment - Travel Agency</title>

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/payment.css"> <!-- External CSS -->
  </head>  
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .payment-container {
            max-width: 600px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }

        .payment-container h2 {
            text-align: center;
            color: #232d39;
            font-weight: 700;
        }

        .btn-submit {
            background: #ed563b;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #c94c32;
        }
    </style>

<body>

    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <a href="index.html" class="logo">Travel Agency </a>
                        <ul class="nav">
                            <li ><a href="index.html">Home</a></li>
                            <li><a href="packages.html">Packages</a></li>
                            <li><a href="booking.html"  >Booking</a></li>
                            <li><a href="faq.html">FAQ</a></li>
                            <li><a href="tour_guide.html">Tour Guide</a></li>
                            <li><a href="signup.html">Login/Signup</a></li>
                            <li><a href="payment.html"  class="active">Payment</a></li>
                        </ul>
                        <a class='menu-trigger'><span>Menu</span></a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

   

    <div class="container">
        <div class="payment-container">
            <h2>Payment <em>Details</em></h2>
            <form id="paymentForm">
                
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount ($)</label>
                    <input type="number" class="form-control" id="amount" placeholder="Enter amount" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="currency" class="form-label">Currency</label>
                    <select class="form-control" id="currency" required>
                        <option value="USD">USD - US Dollar</option>
                        <option value="EUR">EUR - Euro</option>
                        <option value="GBP">GBP - British Pound</option>
                        <option value="INR">INR - Indian Rupee</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="payment_date" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="payment_date" required>
                </div>

                

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-control" id="payment_method" required>
                        <option value="Credit Card">Credit Card</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Submit Payment</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Set today's date as default
            const today = new Date().toISOString().split('T')[0];
            document.getElementById("payment_date").value = today;
        });
    </script>

</body>
</html>
