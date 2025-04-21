<?php
session_start();

// Check if admin is authenticated
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['name'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "qwepoi"; // Replace with your actual DB password
$database = "TourTravelDB";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    $error = ['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error];
    header('Content-Type: application/json');
    echo json_encode($error);
    exit();
}

$tables = [
    'Customer', 'TourPackage', 'Booking', 'Payment', 'Review',
    'Destination', 'TourGuide', 'TourPackagePricing', 'TourCategory',
    'ActivityType', 'TourPackageSchedule', 'FAQ', 'Discount',
    'TourPackageAmenities', 'LoyaltyProgram'
];

$table = isset($_POST['table']) ? filter_var($_POST['table'], FILTER_SANITIZE_STRING) : '';
$data = isset($_POST['data']) ? (array)$_POST['data'] : [];

if (!in_array($table, $tables) || empty($data)) {
    $error = ['success' => false, 'message' => 'Invalid table or data.'];
    header('Content-Type: application/json');
    echo json_encode($error);
    exit();
}

// Fetch column details and constraints
$result = $conn->query("SHOW COLUMNS FROM `$table`");
if (!$result) {
    $error = ['success' => false, 'message' => 'Error fetching columns: ' . $conn->error];
    header('Content-Type: application/json');
    echo json_encode($error);
    exit();
}

$columns = [];
$types = [];
$nullables = [];
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
    $types[$row['Field']] = $row['Type'];
    $nullables[$row['Field']] = $row['Null'] === 'NO';
}

// Log incoming data for debugging
error_log("Insert attempt for table: $table, Data: " . print_r($data, true));

$values = [];
$params = [];
$param_types = '';
$errors = [];

foreach ($columns as $column) {
    if (isset($data[$column])) {
        $value = $data[$column];
        if ($nullables[$column] && empty($value)) {
            $value = null;
        } elseif (empty($value) && !$nullables[$column]) {
            $errors[$column] = 'This field is required.';
            continue;
        }
        if (strpos($types[$column], 'int') !== false || strpos($types[$column], 'decimal') !== false || strpos($types[$column], 'float') !== false) {
            $value = filter_var($value, FILTER_VALIDATE_FLOAT) !== false ? $value : null;
            if ($value === null && !$nullables[$column]) {
                $errors[$column] = 'Invalid number format.';
            }
            $param_types .= 'd';
        } elseif (strpos($types[$column], 'varchar') !== false || strpos($types[$column], 'text') !== false) {
            $value = filter_var($value, FILTER_SANITIZE_STRING);
            if (empty($value) && !$nullables[$column]) {
                $errors[$column] = 'This field is required.';
            }
            $param_types .= 's';
        } elseif (strpos($types[$column], 'date') !== false) {
            $value = date('Y-m-d', strtotime($value)) ?: null;
            if ($value === null && !$nullables[$column]) {
                $errors[$column] = 'Invalid date format.';
            }
            $param_types .= 's';
        } elseif (strpos($types[$column], 'datetime') !== false) {
            $value = date('Y-m-d H:i:s', strtotime($value)) ?: null;
            if ($value === null && !$nullables[$column]) {
                $errors[$column] = 'Invalid datetime format.';
            }
            $param_types .= 's';
        } else {
            $value = null;
            $param_types .= 's';
        }
        $values[] = $value;
    } elseif ($nullables[$column]) {
        $values[] = null;
        $param_types .= 's';
    } else {
        $errors[$column] = 'This field is required.';
    }
}

if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit();
}

if (empty($values)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No valid data provided.']);
    exit();
}

$placeholders = implode(',', array_fill(0, count($values), '?'));
$sql = "INSERT INTO `$table` (" . implode(',', $columns) . ") VALUES ($placeholders)";

// Log SQL for debugging
error_log("SQL: $sql, Values: " . print_r($values, true));

try {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Prepare statement failed: ' . $conn->error);
    }
    $stmt->bind_param($param_types, ...$values);
    if (!$stmt->execute()) {
        throw new Exception('Insert failed: ' . $stmt->error);
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Exception caught: " . $e->getMessage());
    header('Content-Type: application/json');
    $errors = ['general' => $e->getMessage()];
    echo json_encode(['success' => false, 'errors' => $errors]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>