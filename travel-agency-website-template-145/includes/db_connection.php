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
?>