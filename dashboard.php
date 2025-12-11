<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] != "admin"){
    header("location: ../login.php");
    exit;
}

require_once "../config.php";

$total_users = 0;
$total_bookings = 0;
$total_flights = 0;
$total_revenue = 0;

$sql = "SELECT COUNT(*) as count FROM users";
$result = mysqli_query($conn, $sql);
$total_users = mysqli_fetch_assoc($result)['count'];

$sql = "SELECT COUNT(*) as count FROM TICKET";
$result = mysqli_query($conn, $sql);
$total_bookings = mysqli_fetch_assoc($result)['count'];

$sql = "SELECT COUNT(*) as count FROM FLIGHT";
$result = mysqli_query($conn, $sql);
$total_flights = mysqli_fetch_assoc($result)['count'];

$sql = "SELECT SUM(amount) as total FROM PAYMENT";
$result = mysqli_query($conn, $sql);
$total_revenue = mysqli_fetch_assoc($result)['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
     <link rel="stylesheet" href="../style.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    
    <p>Welcome, <?php echo $_SESSION["username"]; ?>!</p>
    
    <p><a href="../index.php">← Back to Home</a></p>
    
    <h2>System Statistics</h2>
    
    <table border="1" cellpadding="10">
        <tr>
            <th>Total Users</th>
            <td><?php echo $total_users; ?></td>
        </tr>
        <tr>
            <th>Total Bookings</th>
            <td><?php echo $total_bookings; ?></td>
        </tr>
        <tr>
            <th>Total Flights</th>
            <td><?php echo $total_flights; ?></td>
        </tr>
        <tr>
            <th>Total Revenue</th>
            <td>$<?php echo number_format($total_revenue, 2); ?></td>
        </tr>
    </table>
    
    <h2>Admin Menu</h2>
    <ul>
        <li><a href="users.php">View All Users</a></li>
        <li><a href="bookings.php">View All Bookings</a></li>
        <li><a href="flights.php">View All Flights</a></li>
    </ul>
    
</body>
</html>
