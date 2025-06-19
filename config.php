<?php
$host = 'localhost';
$user = 'root';
$password = 'Database@123'; // Leave this empty if you're using XAMPP's default
$database = 'bus_bookingdb';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

