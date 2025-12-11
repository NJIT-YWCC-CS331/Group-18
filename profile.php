<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

// Get user information
$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM users WHERE user_id = ?";
$user = null;

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>My Profile</h1>
    
    <p><a href="index.php">← Back to Home</a></p>
    
    <h2>Account Information</h2>
    
    <table border="1" cellpadding="10">
        <tr>
            <th>Username</th>
            <td><?php echo $user['username']; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $user['email']; ?></td>
        </tr>
        <tr>
            <th>First Name</th>
            <td><?php echo $user['first_name']; ?></td>
        </tr>
        <tr>
            <th>Last Name</th>
            <td><?php echo $user['last_name']; ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?php echo $user['phone']; ?></td>
        </tr>
        <tr>
            <th>Account Type</th>
            <td><?php echo ucfirst($user['role']); ?></td>
        </tr>
        <tr>
            <th>Member Since</th>
            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
        </tr>
    </table>
    
    <h2>Quick Links</h2>
    <ul>
        <li><a href="search.php">Search Flights</a></li>
        <li><a href="my_bookings.php">My Bookings</a></li>
        <?php if($_SESSION["role"] == "admin"): ?>
        <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
        <?php endif; ?>
    </ul>
    
</body>
</html>
