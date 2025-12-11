<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] != "admin"){
    header("location: ../login.php");
    exit;
}

require_once "../config.php";

$sql = "SELECT t.ticket_number, t.booking_date, t.seat_number, t.ticket_class, 
        t.ticket_price, t.ticket_status,
        f.flight_number, f.airline_company,
        p.p_name, p.passport_number, p.email,
        pay.payment_id, pay.payment_method
        FROM TICKET t
        JOIN ASSOCIATES_WITH aw ON t.ticket_number = aw.ticket_number
        JOIN FLIGHT f ON aw.flight_number = f.flight_number
        JOIN BOOKS b ON t.ticket_number = b.ticket_number
        JOIN PASSENGER p ON b.passport_number = p.passport_number
        JOIN PAYMENT pay ON b.payment_id = pay.payment_id
        ORDER BY t.booking_date DESC";

$result = mysqli_query($conn, $sql);
$bookings = array();

while($row = mysqli_fetch_assoc($result)){
    $bookings[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Bookings</title>
</head>
<body>
    <h1>All Bookings</h1>
    
    <p><a href="dashboard.php">← Back to Dashboard</a></p>
    
    <h2>All Flight Bookings (<?php echo count($bookings); ?>)</h2>
    
    <table border="1" cellpadding="10">
        <tr>
            <th>Ticket Number</th>
            <th>Flight Number</th>
            <th>Airline</th>
            <th>Passenger</th>
            <th>Email</th>
            <th>Passport</th>
            <th>Seat</th>
            <th>Class</th>
            <th>Price</th>
            <th>Payment Method</th>
            <th>Booking Date</th>
            <th>Status</th>
        </tr>
        <?php foreach($bookings as $booking): ?>
        <tr>
            <td><?php echo $booking['ticket_number']; ?></td>
            <td><?php echo $booking['flight_number']; ?></td>
            <td><?php echo $booking['airline_company']; ?></td>
            <td><?php echo $booking['p_name']; ?></td>
            <td><?php echo $booking['email']; ?></td>
            <td><?php echo $booking['passport_number']; ?></td>
            <td><?php echo $booking['seat_number']; ?></td>
            <td><?php echo $booking['ticket_class']; ?></td>
            <td>$<?php echo number_format($booking['ticket_price'], 2); ?></td>
            <td><?php echo $booking['payment_method']; ?></td>
            <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
            <td><?php echo ucfirst($booking['ticket_status']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
</body>
</html>
