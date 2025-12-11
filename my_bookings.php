<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

// Get user's email to find their bookings
$user_email = $_SESSION["email"];

$sql = "SELECT t.ticket_number, t.booking_date, t.seat_number, t.ticket_class, 
        t.ticket_price, t.ticket_status,
        f.flight_number, f.airline_company,
        dep.airport_code as dep_airport, dep_city.city as dep_city,
        arr.airport_code as arr_airport, arr_city.city as arr_city,
        dep.departure_time, arr.arrival_time,
        p.passport_number, p.p_name,
        pay.payment_id, pay.payment_method
        FROM TICKET t
        JOIN ASSOCIATES_WITH aw ON t.ticket_number = aw.ticket_number
        JOIN FLIGHT f ON aw.flight_number = f.flight_number
        JOIN DEPARTS dep ON f.flight_number = dep.flight_number
        JOIN AIRPORT dep_city ON dep.airport_code = dep_city.airport_code
        JOIN ARRIVES arr ON f.flight_number = arr.flight_number
        JOIN AIRPORT arr_city ON arr.airport_code = arr_city.airport_code
        JOIN BOOKS b ON t.ticket_number = b.ticket_number
        JOIN PASSENGER p ON b.passport_number = p.passport_number
        JOIN PAYMENT pay ON b.payment_id = pay.payment_id
        WHERE p.email = ?
        ORDER BY t.booking_date DESC";

$bookings = array();
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "s", $user_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($result)){
        $bookings[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>My Bookings</h1>
    
    <p><a href="index.php">← Back to Home</a></p>
    
    <?php if(empty($bookings)): ?>
        <p>You have no bookings yet.</p>
        <p><a href="search.php">Search for flights</a></p>
    <?php else: ?>
        <h2>Your Flight Bookings</h2>
        
        <?php foreach($bookings as $booking): ?>
            <hr>
            <h3>Ticket #<?php echo $booking['ticket_number']; ?></h3>
            
            <table border="1" cellpadding="10">
                <tr>
                    <th>Flight Number</th>
                    <td><?php echo $booking['flight_number']; ?></td>
                </tr>
                <tr>
                    <th>Airline</th>
                    <td><?php echo $booking['airline_company']; ?></td>
                </tr>
                <tr>
                    <th>From</th>
                    <td><?php echo $booking['dep_city'] . " (" . $booking['dep_airport'] . ")"; ?></td>
                </tr>
                <tr>
                    <th>To</th>
                    <td><?php echo $booking['arr_city'] . " (" . $booking['arr_airport'] . ")"; ?></td>
                </tr>
                <tr>
                    <th>Departure</th>
                    <td><?php echo date('M d, Y H:i', strtotime($booking['departure_time'])); ?></td>
                </tr>
                <tr>
                    <th>Arrival</th>
                    <td><?php echo date('M d, Y H:i', strtotime($booking['arrival_time'])); ?></td>
                </tr>
                <tr>
                    <th>Passenger Name</th>
                    <td><?php echo $booking['p_name']; ?></td>
                </tr>
                <tr>
                    <th>Passport Number</th>
                    <td><?php echo $booking['passport_number']; ?></td>
                </tr>
                <tr>
                    <th>Seat Number</th>
                    <td><?php echo $booking['seat_number']; ?></td>
                </tr>
                <tr>
                    <th>Class</th>
                    <td><?php echo $booking['ticket_class']; ?></td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td>$<?php echo number_format($booking['ticket_price'], 2); ?></td>
                </tr>
                <tr>
                    <th>Booking Date</th>
                    <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?php echo ucfirst($booking['ticket_status']); ?></td>
                </tr>
                <tr>
                    <th>Payment Method</th>
                    <td><?php echo $booking['payment_method']; ?></td>
                </tr>
                <tr>
                    <th>Payment ID</th>
                    <td><?php echo $booking['payment_id']; ?></td>
                </tr>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>
    
</body>
</html>
