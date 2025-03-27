<?php
session_start();

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
            header("Location: index.html");
            exit();
        } else {
            echo "<script>
                    alert('Incorrect password. Please try again.');
                    window.location.href = 'login.html';
                  </script>";
        }
    } else {
        echo "<script>
                alert('User does not exist. Please check your email or sign up.');
                window.location.href = 'login.html';
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>