<?php
// Start the session - this keeps track of logged in users
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Flight Booking System</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Flight Booking System</h1>
    
    <?php
    // Check if user is logged in
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        // User is logged in - show welcome message
        echo "<h2>Welcome, " . $_SESSION["username"] . "!</h2>";
        
        echo "<p>What would you like to do?</p>";
        echo "<ul>";
        echo "<li><a href='search.php'>Search Flights</a></li>";
        echo "<li><a href='my_bookings.php'>My Bookings</a></li>";
        echo "<li><a href='profile.php'>My Profile</a></li>";
        
        // If user is admin, show admin link
        if($_SESSION["role"] == "admin") {
            echo "<li><a href='admin/dashboard.php'>Admin Dashboard</a></li>";
        }
        
        echo "<li><a href='logout.php'>Logout</a></li>";
        echo "</ul>";
        
    } else {
        // User is NOT logged in - show login/register options
        echo "<p>Welcome! Please login or register to book flights.</p>";
        echo "<ul>";
        echo "<li><a href='login.php'>Login</a></li>";
        echo "<li><a href='register.php'>Register</a></li>";
        echo "</ul>";
    }
    ?>
    
</body>
</html>
