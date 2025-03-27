<?php
$host = "localhost";
$username = "root";
$password = "qwepoi";
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