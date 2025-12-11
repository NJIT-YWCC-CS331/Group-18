<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'flight_booking_db');

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn === false){
    die("ERROR: Could not connect to database. " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

function generateUniqueId($prefix = '') {
    return $prefix . strtoupper(uniqid());
}
function generateTicketNumber() {
    return 'TKT' . date('Ymd') . rand(1000, 9999);
}
function generatePaymentId() {
    return 'PAY' . date('Ymd') . rand(1000, 9999);
}
?>