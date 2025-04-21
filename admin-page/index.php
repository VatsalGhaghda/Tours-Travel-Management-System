<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "qwepoi"; // Replace with your actual DB password
$database = "TourTravelDB";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// List of all tables
$tables = [
    'Customer', 'TourPackage', 'Booking', 'Payment', 'Review',
    'Destination', 'TourGuide', 'TourPackagePricing', 'TourCategory',
    'ActivityType', 'TourPackageSchedule', 'FAQ', 'Discount',
    'TourPackageAmenities', 'LoyaltyProgram'
];

// Map tables to their presumed primary keys (adjust if different)
$primary_keys = [
    'Customer' => 'Customer_ID',
    'TourPackage' => 'TourPackage_ID',
    'Booking' => 'Booking_ID',
    'Payment' => 'Payment_ID',
    'Review' => 'Review_ID',
    'Destination' => 'Destination_ID',
    'TourGuide' => 'TourGuide_ID',
    'TourPackagePricing' => 'Pricing_ID',
    'TourCategory' => 'Category_ID',
    'ActivityType' => 'ActivityType_ID',
    'TourPackageSchedule' => 'Schedule_ID',
    'FAQ' => 'FAQ_ID',
    'Discount' => 'Discount_ID',
    'TourPackageAmenities' => 'Amenities_ID',
    'LoyaltyProgram' => 'LoyaltyProgram_ID'
];

$selected_table = isset($_GET['table']) ? $_GET['table'] : '';
$invalid_emails = []; // Array to collect invalid emails for debugging

// Fetch dashboard counts
$total_clients = $conn->query("SELECT COUNT(*) as count FROM Customer WHERE User_Type = 'Regular'")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM Booking WHERE Status = 'Confirmed'")->fetch_assoc()['count'];
$total_tour_packages = $conn->query("SELECT COUNT(*) as count FROM TourPackage")->fetch_assoc()['count'];
$total_reviews = $conn->query("SELECT COUNT(*) as count FROM Review")->fetch_assoc()['count'];

// Fetch pending bookings for Daily Feeds and notification
$pending_bookings = $conn->query("SELECT Booking_ID, Customer_ID, Booking_Date FROM Booking WHERE Status = 'Pending' ORDER BY Booking_Date DESC");
$total_pending = ($pending_bookings && $pending_bookings->num_rows !== null) ? $pending_bookings->num_rows : 0;
if (!$pending_bookings) {
    error_log("Pending bookings query failed: " . $conn->error);
}

// Fetch new left container counts
$total_tour_guides = $conn->query("SELECT COUNT(*) as count FROM TourGuide")->fetch_assoc()['count'];
$total_destinations = $conn->query("SELECT COUNT(*) as count FROM Destination")->fetch_assoc()['count'];
$total_tour_amenities = $conn->query("SELECT COUNT(*) as count FROM TourPackageAmenities")->fetch_assoc()['count'];

// Fetch payment data for graph, converting to USD
$payment_data = $conn->query("SELECT DATE_FORMAT(Payment_Date, '%Y-%m') as payment_month, 
                             SUM(CASE 
                                 WHEN Currency = 'EUR' THEN Amount * 1.10
                                 WHEN Currency = 'GBP' THEN Amount * 1.30
                                 WHEN Currency = 'INR' THEN Amount * 0.012
                                 ELSE Amount
                             END) as total_usd
                             FROM Payment 
                             WHERE Status = 'Completed' 
                             GROUP BY DATE_FORMAT(Payment_Date, '%Y-%m') 
                             ORDER BY DATE_FORMAT(Payment_Date, '%Y-%m')");
$labels = [];
$overdue_data = [];

if ($payment_data) {
    while ($row = $payment_data->fetch_assoc()) {
        $labels[] = $row['payment_month'];
        $overdue_data[] = floatval($row['total_usd']);
        error_log("Fetched payment data: Month " . $row['payment_month'] . ", Total USD: " . $row['total_usd']);
    }
} else {
    error_log("Payment query failed: " . $conn->error);
}

// Calculate total earnings in USD
$total_earnings = array_sum($overdue_data);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Dashboard | TourTravel</title>
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
    <!-- Theme stylesheet-->
    <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="img/favicon.ico">
    <!-- DataTables CSS-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <!-- Custom styles for responsive table -->
    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table.table-sm {
            min-width: 800px;
        }
        .table td, .table th {
            white-space: nowrap;
            font-size: 0.9rem;
        }
        .card {
            width: 100%;
        }
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 10px;
        }
        .new-request {
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .feed-item {
            display: block;
        }
        .daily-feeds .card-body {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 10px;
        }
        .delete-btn {
            padding: 2px 6px;
            font-size: 0.75rem;
        }
        .insert-btn {
            margin-bottom: 10px;
        }
        .modal-body .form-group {
            margin-bottom: 15px;
        }
        .modal-body .form-control.is-invalid {
            border-color: #dc3545;
        }
        .modal-body .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 0.875rem;
        }
        .modal-body .form-control.is-invalid + .invalid-feedback {
            display: block;
        }
    </style>
    <!-- Tweaks for older IEs-->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>
<body>
    <div class="page">
        <!-- Main Navbar-->
        <header class="header">
            <nav class="navbar">
                <div class="search-box">
                    <button class="dismiss"><i class="icon-close"></i></button>
                    <form id="searchForm" action="#" role="search">
                        <input type="search" placeholder="What are you looking for..." class="form-control">
                    </form>
                </div>
                <div class="container-fluid">
                    <div class="navbar-holder d-flex align-items-center justify-content-between">
                        <div class="navbar-header">
                            <a href="index.php" class="navbar-brand">
                                <div class="brand-text brand-big"><span>TourTravel </span><strong>Dashboard</strong></div>
                                <div class="brand-text brand-small"><strong>TT</strong></div>
                            </a>
                            <a id="toggle-btn" href="#" class="menu-btn active"><span></span><span></span><span></span></a>
                        </div>
                        <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                            <li class="nav-item dropdown">
                                <a id="messages" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link"><i class="fa fa-envelope-o"></i><span class="badge bg-orange"><?php echo $total_pending; ?></span></a>
                                <ul aria-labelledby="messages" class="dropdown-menu">
                                    <?php
                                    if ($pending_bookings && $pending_bookings->num_rows > 0) {
                                        $pending_bookings->data_seek(0);
                                        $count = 0;
                                        while ($booking = $pending_bookings->fetch_assoc()) {
                                            if ($count >= 3) break;
                                            $count++;
                                            $booking_id = htmlspecialchars($booking['Booking_ID'] ?? 'N/A');
                                            $customer_id = htmlspecialchars($booking['Customer_ID'] ?? 'N/A');
                                            $booking_date = htmlspecialchars($booking['Booking_Date'] ?? 'N/A');
                                            if (strtotime($booking_date) !== false) {
                                                $formatted_date = date('Y-m-d H:i', strtotime($booking_date));
                                            } else {
                                                $formatted_date = 'Invalid Date';
                                            }
                                            ?>
                                            <li>
                                                <a href="#" class="dropdown-item d-flex">
                                                    <div class="msg-profile">
                                                        <img src="img/user.png" alt="..." class="img-fluid rounded-circle">
                                                    </div>
                                                    <div class="msg-body">
                                                        <h5>Booking ID: <?php echo $booking_id; ?> (Customer ID: <?php echo $customer_id; ?>)</h5>
                                                        <span>Booking Date: <?php echo $formatted_date; ?></span>
                                                    </div>
                                                </a>
                                            </li>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <li>
                                            <a href="#" class="dropdown-item text-center">
                                                <strong>No pending requests</strong>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    if ($total_pending > 0) {
                                        ?>
                                        <li>
                                            <a href="index.php" class="dropdown-item all-notifications text-center">
                                                <strong>View All (<?php echo $total_pending; ?>)</strong>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                            <li class="nav-item"><a href="logout.php" class="nav-link logout">Logout<i class="fa fa-sign-out"></i></a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <div class="page-content d-flex align-items-stretch">
            <!-- Side Navbar -->
            <nav class="side-navbar">
                <div class="sidebar-header d-flex align-items-center">
                    <div class="avatar"><img src="img/avatar-1.jpg" alt="..." class="img-fluid rounded-circle"></div>
                    <div class="title">
                        <h1 class="h4"><?php echo htmlspecialchars($_SESSION['name']); ?></h1>
                        <p>Admin</p>
                    </div>
                </div>
                <span class="heading">Main</span>
                <ul class="list-unstyled">
                    <li <?php echo !$selected_table ? 'class="active"' : ''; ?>><a href="index.php"><i class="icon-home"></i>Home</a></li>
                    <li><a href="#exampledropdownDropdown" aria-expanded="false" data-toggle="collapse"><i class="icon-interface-windows"></i>Tables</a>
                        <ul id="exampledropdownDropdown" class="collapse list-unstyled">
                            <?php foreach ($tables as $table): ?>
                                <li <?php echo $selected_table === $table ? 'class="active"' : ''; ?>>
                                    <a href="index.php?table=<?php echo urlencode($table); ?>"><?php echo htmlspecialchars($table); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </nav>
            <div class="content-inner">
                <!-- Page Header-->
                <header class="page-header">
                    <div class="container-fluid">
                        <h2 class="no-margin-bottom"><?php echo $selected_table ? htmlspecialchars($selected_table) : 'Dashboard'; ?></h2>
                    </div>
                </header>
                <!-- Breadcrumb-->
                <div class="breadcrumb-holder container-fluid">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active"><?php echo $selected_table ? htmlspecialchars($selected_table) : 'Dashboard'; ?></li>
                    </ul>
                </div>
                <?php if ($selected_table): ?>
                    <!-- Table Display -->
                    <section class="tables">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-close">
                                            <div class="dropdown">
                                                <button type="button" id="closeCard1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
                                                <div aria-labelledby="closeCard1" class="dropdown-menu dropdown-menu-right has-shadow">
                                                    <a href="#" class="dropdown-item remove"><i class="fa fa-times"></i>Close</a>
                                                    <a href="#" class="dropdown-item edit"><i class="fa fa-gear"></i>Edit</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-header d-flex align-items-center">
                                            <h3 class="h4"><?php echo htmlspecialchars($selected_table); ?> Data</h3>
                                        </div>
                                        <div class="card-body">
                                            <button type="button" class="btn btn-success insert-btn" data-toggle="modal" data-target="#insertModal">Insert Record</button>
                                            <?php
                                            if (!in_array($selected_table, $tables)) {
                                                echo "<div class='alert alert-danger'>Invalid table selected.</div>";
                                            } else {
                                                if (!$conn->ping()) {
                                                    echo "<div class='alert alert-danger'>Database connection is closed. Please refresh the page.</div>";
                                                } else {
                                                    $result = $conn->query("SHOW COLUMNS FROM `$selected_table`");
                                                    if ($result) {
                                                        $columns = [];
                                                        while ($row = $result->fetch_assoc()) {
                                                            $columns[] = $row['Field'];
                                                        }

                                                        $result = $conn->query("SELECT * FROM `$selected_table`");
                                                        if ($result) {
                                                            if ($result->num_rows == 0) {
                                                                echo "<div class='alert alert-info'>No data found in table '$selected_table'.</div>";
                                                            } else {
                                                                $table_id = 'table-' . htmlspecialchars($selected_table);
                                                                $primary_key = $primary_keys[$selected_table];
                                                                echo "<div class='table-responsive'>";
                                                                echo "<table id='$table_id' class='table table-striped table-hover table-sm'>";
                                                                echo "<thead><tr>";
                                                                foreach ($columns as $column) {
                                                                    echo "<th>" . htmlspecialchars($column) . "</th>";
                                                                }
                                                                echo "<th>Action</th>";
                                                                echo "</tr></thead><tbody>";
                                                                $row_count = 0;
                                                                while ($row = $result->fetch_assoc()) {
                                                                    $row_count++;
                                                                    $id = htmlspecialchars($row[$primary_key] ?? $row_count);
                                                                    echo "<tr>";
                                                                    foreach ($columns as $column) {
                                                                        $value = isset($row[$column]) ? $row[$column] : '';
                                                                        if ($selected_table === 'Customer' && in_array($column, ['Email', 'Phone', 'Password'])) {
                                                                            if ($column === 'Email') {
                                                                                if (!empty($value) && is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                                                                    $parts = explode('@', $value);
                                                                                    $value = substr($parts[0], 0, 2) . '****@' . $parts[1];
                                                                                } else {
                                                                                    $value = '<span class="text-danger">Invalid: ' . htmlspecialchars($value ?: 'NULL') . '</span>';
                                                                                    $invalid_emails[] = [
                                                                                        'row' => $row_count,
                                                                                        'email' => $row['Email'] ?: 'NULL'
                                                                                    ];
                                                                                }
                                                                            } elseif ($column === 'Phone' && !empty($value)) {
                                                                                $value = substr($value, 0, 3) . '****' . substr($value, -2);
                                                                            } elseif ($column === 'Password') {
                                                                                $value = '****';
                                                                            }
                                                                            $value = "<span class='font-monospace'>$value</span>";
                                                                        } elseif ($selected_table === 'Payment' && $column === 'Transaction_ID' && !empty($value)) {
                                                                            $value = substr($value, 0, 4) . '****' . substr($value, -4);
                                                                            $value = "<span class='font-monospace'>$value</span>";
                                                                        } elseif (is_null($value)) {
                                                                            $value = 'NULL';
                                                                        } else {
                                                                            $value = strlen($value) > 50 ? substr($value, 0, 47) . '...' : $value;
                                                                            $value = htmlspecialchars($value);
                                                                        }
                                                                        echo "<td>$value</td>";
                                                                    }
                                                                    echo "<td><button class='btn btn-danger btn-sm delete-btn' data-table='$selected_table' data-id='$id'>Delete</button></td>";
                                                                    echo "</tr>";
                                                                }
                                                                echo "</tbody></table>";
                                                                echo "</div>";
                                                            }
                                                        } else {
                                                            error_log("Error fetching data for table '$selected_table': " . $conn->error);
                                                            echo "<div class='alert alert-danger'>Error fetching data: " . htmlspecialchars($conn->error) . "</div>";
                                                        }
                                                    } else {
                                                        error_log("Error fetching columns for table '$selected_table': " . $conn->error);
                                                        echo "<div class='alert alert-danger'>Error fetching columns: " . htmlspecialchars($conn->error) . "</div>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php if (!empty($invalid_emails)): ?>
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> Found <?php echo count($invalid_emails); ?> invalid emails in the Customer table. Please review and update these records.
                            <ul>
                                <?php foreach ($invalid_emails as $invalid): ?>
                                    <li>Row <?php echo $invalid['row']; ?>: <?php echo htmlspecialchars($invalid['email']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <!-- Insert Record Modal -->
                    <div id="insertModal" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel" aria-hidden="true" class="modal fade text-left">
                        <div role="document" class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 id="insertModalLabel" class="modal-title">Insert Record into <?php echo htmlspecialchars($selected_table); ?></h4>
                                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                    <form id="insertForm">
                                        <?php
                                        if (in_array($selected_table, $tables) && $conn->ping()) {
                                            $result = $conn->query("SHOW COLUMNS FROM `$selected_table`");
                                            if ($result) {
                                                $columns = [];
                                                while ($row = $result->fetch_assoc()) {
                                                    $columns[$row['Field']] = $row['Type'];
                                                }
                                                foreach ($columns as $field => $type) {
                                                    $input_type = 'text';
                                                    if (strpos($type, 'int') !== false || strpos($type, 'decimal') !== false || strpos($type, 'float') !== false) {
                                                        $input_type = 'number';
                                                    } elseif (strpos($type, 'varchar') !== false || strpos($type, 'text') !== false) {
                                                        $input_type = 'text';
                                                    } elseif (strpos($type, 'date') !== false) {
                                                        $input_type = 'date';
                                                    } elseif (strpos($type, 'datetime') !== false) {
                                                        $input_type = 'datetime-local';
                                                    } elseif (strpos($type, 'enum') !== false) {
                                                        $input_type = 'select';
                                                    }
                                                    echo "<div class='form-group'>";
                                                    echo "<label for='insert_$field' class='form-control-label'>" . htmlspecialchars(ucfirst(str_replace('_', ' ', $field))) . "</label>";
                                                    if ($input_type === 'select') {
                                                        preg_match('/enum\((.*?)\)/', $type, $matches);
                                                        $options = explode(',', str_replace("'", '', $matches[1]));
                                                        echo "<select class='form-control' id='insert_$field' name='$field' required>";
                                                        foreach ($options as $option) {
                                                            echo "<option value='" . trim($option) . "'>" . htmlspecialchars(trim($option)) . "</option>";
                                                        }
                                                        echo "</select>";
                                                    } else {
                                                        echo "<input type='$input_type' class='form-control' id='insert_$field' name='$field' required>";
                                                    }
                                                    echo "<div class='invalid-feedback'>This field is required.</div>";
                                                    echo "</div>";
                                                }
                                            }
                                        }
                                        ?>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Dashboard Counts Section-->
                    <section class="dashboard-counts no-padding-bottom">
                        <div class="container-fluid">
                            <div class="row bg-white has-shadow">
                                <div class="col-xl-3 col-sm-6">
                                    <div class="item d-flex align-items-center">
                                        <div class="icon bg-violet"><i class="icon-user"></i></div>
                                        <div class="title"><span>Total<br>Clients</span>
                                            <div class="progress">
                                                <div role="progressbar" style="width: <?php echo min(100, ($total_clients / 100) * 100); ?>%; height: 4px;" aria-valuenow="<?php echo $total_clients; ?>" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-violet"></div>
                                            </div>
                                        </div>
                                        <div class="number"><strong><?php echo $total_clients; ?></strong></div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-sm-6">
                                    <div class="item d-flex align-items-center">
                                        <div class="icon bg-red"><i class="icon-padnote"></i></div>
                                        <div class="title"><span>Total<br>Bookings</span>
                                            <div class="progress">
                                                <div role="progressbar" style="width: <?php echo min(100, ($total_bookings / 100) * 100); ?>%; height: 4px;" aria-valuenow="<?php echo $total_bookings; ?>" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-red"></div>
                                            </div>
                                        </div>
                                        <div class="number"><strong><?php echo $total_bookings; ?></strong></div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-sm-6">
                                    <div class="item d-flex align-items-center">
                                        <div class="icon bg-green"><i class="icon-bill"></i></div>
                                        <div class="title"><span>Total<br>Tour Packages</span>
                                            <div class="progress">
                                                <div role="progressbar" style="width: <?php echo min(100, ($total_tour_packages / 100) * 100); ?>%; height: 4px;" aria-valuenow="<?php echo $total_tour_packages; ?>" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-green"></div>
                                            </div>
                                        </div>
                                        <div class="number"><strong><?php echo $total_tour_packages; ?></strong></div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-sm-6">
                                    <div class="item d-flex align-items-center">
                                        <div class="icon bg-orange"><i class="fa fa-star"></i></div>
                                        <div class="title"><span>Total<br>Reviews</span>
                                            <div class="progress">
                                                <div role="progressbar" style="width: <?php echo min(100, ($total_reviews / 100) * 100); ?>%; height: 4px;" aria-valuenow="<?php echo $total_reviews; ?>" aria-valuemin="0" aria-valuemax="100" class="progress-bar bg-orange"></div>
                                            </div>
                                        </div>
                                        <div class="number"><strong><?php echo $total_reviews; ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Combined Dashboard Section (Header, Daily Feeds, Total Earning) -->
                    <section class="dashboard-combined">
                        <div class="container-fluid">
                            <div class="row">
                                <!-- Dashboard Header -->
                                <div class="col-lg-4 col-md-12">
                                    <div class="statistics">
                                        <div class="statistic d-flex align-items-center bg-white has-shadow">
                                            <div class="icon bg-red"><i class="fa fa-users"></i></div>
                                            <div class="text"><strong><?php echo $total_tour_guides; ?></strong><br><small>Tour Guides</small></div>
                                        </div>
                                        <div class="statistic d-flex align-items-center bg-white has-shadow">
                                            <div class="icon bg-green"><i class="fa fa-map-marker"></i></div>
                                            <div class="text"><strong><?php echo $total_destinations; ?></strong><br><small>Destinations</small></div>
                                        </div>
                                        <div class="statistic d-flex align-items-center bg-white has-shadow">
                                            <div class="icon bg-orange"><i class="fa fa-list"></i></div>
                                            <div class="text"><strong><?php echo $total_tour_amenities; ?></strong><br><small>Tour Amenities</small></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Daily Feeds -->
                                <div class="col-lg-8 col-md-12">
                                    <div class="daily-feeds card">
                                        <div class="card-header">
                                            <h3 class="h4">Daily Feeds</h3>
                                            <?php if ($total_pending > 0): ?>
                                                <span class="badge bg-danger new-request">New Requests</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body no-padding">
                                            <?php
                                            if ($pending_bookings && $pending_bookings->num_rows > 0) {
                                                $pending_bookings->data_seek(0);
                                                while ($booking = $pending_bookings->fetch_assoc()) {
                                                    $booking_id = htmlspecialchars($booking['Booking_ID'] ?? 'N/A');
                                                    $customer_id = htmlspecialchars($booking['Customer_ID'] ?? 'N/A');
                                                    $booking_date = htmlspecialchars($booking['Booking_Date'] ?? 'N/A');
                                                    ?>
                                                    <div class="item feed-item">
                                                        <div class="feed d-flex justify-content-between">
                                                            <div class="feed-body d-flex justify-content-between">
                                                                <div class="content">
                                                                    <h5>Booking ID: <?php echo $booking_id; ?> (Customer ID: <?php echo $customer_id; ?>)</h5>
                                                                    <span>Booking Date: <?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($booking_date))); ?></span>
                                                                    <div class="CTAs mt-2">
                                                                        <button class="btn btn-xs btn-success accept-btn" data-booking-id="<?php echo $booking_id; ?>">Accept</button>
                                                                        <button class="btn btn-xs btn-danger reject-btn" data-booking-id="<?php echo $booking_id; ?>">Reject</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="date text-right">
                                                                <small><?php echo htmlspecialchars(date('H:i', strtotime($booking_date))); ?> ago</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <div class="item">
                                                    <div class="feed d-flex justify-content-center">
                                                        <div class="content text-center">
                                                            <span>No pending requests at the moment.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- Total Earning -->
                                <div class="col-lg-12 col-md-12">
                                    <div class="overdue card">
                                        <div class="card-body">
                                            <h3>Total Earning</h3>
                                            <p>Our Monthly Earning in USD Chart.</p>
                                            <div class="number text-center"><?php echo $total_earnings > 0 ? '$' . number_format($total_earnings, 2) : '$0.00'; ?></div>
                                            <?php if (empty($labels)): ?>
                                                <div class="alert alert-info text-center">No completed payment data available to display.</div>
                                            <?php else: ?>
                                                <div class="chart">
                                                    <canvas id="lineChart1"></canvas>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>
                <!-- Page Footer-->
                <footer class="main-footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <p>Your company © 2024-2025</p>
                            </div>
                            <div class="col-sm-6 text-right">
                                <p>Design by <a href="#" class="external">Team</a></p>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>
    <?php
    // Close database connection after all queries
    if ($conn && $conn->ping()) {
        $conn->close();
    }
    ?>
    <!-- JavaScript files-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="vendor/popper.js/umd/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <!-- DataTables JS (compatible versions) -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <!-- Chart.js (non-module version) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="vendor/jquery.cookie/jquery.cookie.js"></script>
    <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="js/charts-home.js"></script>
    <script src="js/front.js"></script>

    <!-- Initialize DataTables, Charts, and Daily Feeds -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded, initializing scripts...');

            // Initialize DataTable for the current table if one is selected
            <?php if ($selected_table): ?>
                var tableId = 'table-<?php echo htmlspecialchars($selected_table); ?>';
                console.log('Checking table: ' + tableId);
                if ($('#' + tableId).length) {
                    try {
                        console.log('Initializing DataTable for: ' + tableId);
                        if ($.fn.DataTable.isDataTable('#' + tableId)) {
                            $('#' + tableId).DataTable().destroy();
                            console.log('Destroyed existing DataTable instance for: ' + tableId);
                        }
                        $('#' + tableId).DataTable({
                            pageLength: 10,
                            lengthMenu: [10, 20, 50, 100],
                            searching: true,
                            ordering: true,
                            info: true,
                            paging: true,
                            responsive: true,
                            language: {
                                search: "Search records:",
                                lengthMenu: "Show _MENU_ entries"
                            }
                        });
                        console.log('DataTable successfully initialized for: ' + tableId);
                    } catch (e) {
                        console.error('Failed to initialize DataTable for ' + tableId + ':', e);
                    }
                } else {
                    console.warn('Table #' + tableId + ' not found in DOM.');
                }
            <?php endif; ?>

            // Handle sidebar table link clicks
            $('a[href*="index.php?table="]').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                console.log('Navigating to: ' + url);
                window.location.href = url;
            });

            // Initialize chart with payment data in USD
            var ctx = document.getElementById('lineChart1');
            if (ctx) {
                try {
                    console.log('Initializing chart for lineChart1 with USD data');
                    new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: <?php echo json_encode($labels); ?>,
                            datasets: [{
                                label: 'Total Earnings (USD)',
                                data: <?php echo json_encode($overdue_data); ?>,
                                borderColor: 'rgb(30, 255, 0)',
                                backgroundColor: 'rgba(30, 255, 0, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Amount (USD)'
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return '$' + value.toLocaleString();
                                        }
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Month'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Earnings: $' + context.parsed.y.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                    console.log('Chart initialized for lineChart1');
                } catch (e) {
                    console.error('Failed to initialize chart:', e);
                }
            } else {
                console.warn('Canvas element lineChart1 not found. Chart initialization skipped.');
            }

            // Function to handle booking status updates
            function updateBookingStatus(bookingId, status, button) {
                $.ajax({
                    url: 'update_booking_status.php',
                    method: 'POST',
                    data: {
                        booking_id: bookingId,
                        status: status
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('AJAX success, response:', response);
                        if (response.success) {
                            alert('Booking ' + (status === 'Confirmed' ? 'accepted' : 'rejected') + ' successfully.');
                            location.reload(true); // Force full page refresh
                        } else {
                            alert('Error: ' + (response.message || 'Failed to update booking status.'));
                            location.reload(true); // Refresh even on error to reflect any changes
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                        alert('An error occurred while updating the booking. Please try again.');
                        location.reload(true); // Refresh to ensure UI consistency
                    }
                });
            }

            // Handle Accept button clicks
            $('.accept-btn').on('click', function() {
                var bookingId = $(this).data('booking-id');
                console.log('Accept button clicked:', bookingId);
                updateBookingStatus(bookingId, 'Confirmed', this);
            });

            // Handle Reject button clicks
            $('.reject-btn').on('click', function() {
                var bookingId = $(this).data('booking-id');
                console.log('Reject button clicked:', bookingId);
                updateBookingStatus(bookingId, 'Cancelled', this);
            });

            // Auto-refresh pending bookings every 30 seconds
            function refreshFeeds() {
                $.ajax({
                    url: 'get_pending_bookings.php',
                    method: 'GET',
                    success: function(data) {
                        console.log('Refreshing feeds with new data');
                        $('.daily-feeds .card-body').html(data);
                        // Re-bind event listeners for new buttons
                        $('.accept-btn').off('click').on('click', function() {
                            var bookingId = $(this).data('booking-id');
                            console.log('Accept button clicked (refreshed):', bookingId);
                            updateBookingStatus(bookingId, 'Confirmed', this);
                        });
                        $('.reject-btn').off('click').on('click', function() {
                            var bookingId = $(this).data('booking-id');
                            console.log('Reject button clicked (refreshed):', bookingId);
                            updateBookingStatus(bookingId, 'Cancelled', this);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to refresh feeds:', status, error);
                    }
                });
            }

            // Initialize auto-refresh
            setInterval(refreshFeeds, 30000);
            refreshFeeds();

            // Handle delete button clicks
            $('.delete-btn').on('click', function() {
                var table = $(this).data('table');
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this record from ' + table + ' with ID ' + id + '?')) {
                    $.ajax({
                        url: 'delete_record.php',
                        method: 'POST',
                        data: {
                            table: table,
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log('AJAX success, response:', response);
                            if (response.success) {
                                alert('Record deleted successfully.');
                                location.reload(true); // Refresh page to reflect changes
                            } else {
                                alert('Error: ' + (response.message || 'Failed to delete record.'));
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                            alert('An error occurred while deleting the record. Please try again.');
                        }
                    });
                }
            });

            // Handle insert form submission
            $('#insertForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serializeArray();
                var table = '<?php echo htmlspecialchars($selected_table); ?>';
                var data = {};
                $.each(formData, function(index, field) {
                    data[field.name] = field.value;
                });
                $.ajax({
                    url: 'insert_record.php',
                    method: 'POST',
                    data: { table: table, data: data },
                    dataType: 'json',
                    beforeSend: function() {
                        $('#insertForm .form-control').removeClass('is-invalid');
                        $('#insertForm .invalid-feedback').hide();
                    },
                    success: function(response) {
                        console.log('AJAX success, response:', response);
                        if (response.success) {
                            alert('Record inserted successfully.');
                            $('#insertModal').modal('hide');
                            location.reload(true);
                        } else {
                            $.each(response.errors || {}, function(field, error) {
                                $('#insert_' + field).addClass('is-invalid').next('.invalid-feedback').text(error).show();
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                        alert('An error occurred while inserting the record. Please try again.');
                    }
                });
            });

            // Validate form fields on input
            $('#insertForm .form-control').on('input', function() {
                var input = $(this);
                input.removeClass('is-invalid');
                input.next('.invalid-feedback').hide();
            });
        });
    </script>
</body>
</html>