<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

// Get flight number from URL
$flight_number = $_GET['flight'];

// Get flight details
$sql = "SELECT f.flight_number, f.airline_company, f.flight_duration,
        dep.airport_code as dep_airport, dep_city.city as dep_city,
        arr.airport_code as arr_airport, arr_city.city as arr_city,
        dep.departure_time, arr.arrival_time
        FROM FLIGHT f
        JOIN DEPARTS dep ON f.flight_number = dep.flight_number
        JOIN AIRPORT dep_city ON dep.airport_code = dep_city.airport_code
        JOIN ARRIVES arr ON f.flight_number = arr.flight_number
        JOIN AIRPORT arr_city ON arr.airport_code = arr_city.airport_code
        WHERE f.flight_number = ?";

$flight = null;
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "s", $flight_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $flight = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if(!$flight){
    echo "Flight not found!";
    exit;
}

// Process booking
$booking_success = false;
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $passenger_name = $_POST['passenger_name'];
    $passport_number = $_POST['passport_number'];
    $date_of_birth = $_POST['date_of_birth'];
    $nationality = $_POST['nationality'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $seat_number = $_POST['seat_number'];
    $ticket_class = $_POST['ticket_class'];
    $payment_method = $_POST['payment_method'];
    
    // Calculate price based on class
    $base_price = 299.99;
    if($ticket_class == "Business") $price = $base_price * 3;
    elseif($ticket_class == "First Class") $price = $base_price * 5;
    else $price = $base_price;
    
    // Generate IDs
    $ticket_number = 'TKT' . date('Ymd') . rand(1000, 9999);
    $payment_id = 'PAY' . date('Ymd') . rand(1000, 9999);
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert passenger if not exists
        $sql = "INSERT INTO PASSENGER (passport_number, p_name, date_of_birth, nationality, phone, email) 
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE p_name=p_name";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $passport_number, $passenger_name, $date_of_birth, $nationality, $phone, $email);
        mysqli_stmt_execute($stmt);
        
        // Insert ticket
        $sql = "INSERT INTO TICKET (ticket_number, booking_date, seat_number, ticket_class, ticket_price, ticket_status) 
                VALUES (?, CURDATE(), ?, ?, ?, 'booked')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssd", $ticket_number, $seat_number, $ticket_class, $price);
        mysqli_stmt_execute($stmt);
        
        // Insert payment
        $sql = "INSERT INTO PAYMENT (payment_id, payment_method, amount, payment_date) 
                VALUES (?, ?, ?, CURDATE())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssd", $payment_id, $payment_method, $price);
        mysqli_stmt_execute($stmt);
        
        // Link ticket to flight
        $sql = "INSERT INTO ASSOCIATES_WITH (ticket_number, flight_number) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $ticket_number, $flight_number);
        mysqli_stmt_execute($stmt);
        
        // Link passenger to booking
        $sql = "INSERT INTO BOOKS (passport_number, ticket_number, payment_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $passport_number, $ticket_number, $payment_id);
        mysqli_stmt_execute($stmt);
        
        mysqli_commit($conn);
        $booking_success = true;
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = "Booking failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Flight</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Book Flight</h1>
    
    <p><a href="search.php">← Back to Search</a></p>
    
    <?php if($booking_success): ?>
        <h2>Booking Successful!</h2>
        <p>Your booking has been confirmed.</p>
        <p><strong>Ticket Number:</strong> <?php echo $ticket_number; ?></p>
        <p><strong>Payment ID:</strong> <?php echo $payment_id; ?></p>
        <p><a href="my_bookings.php">View My Bookings</a></p>
    <?php else: ?>
        <h2>Flight Details</h2>
        <table border="1" cellpadding="10">
            <tr>
                <th>Flight Number</th>
                <td><?php echo $flight['flight_number']; ?></td>
            </tr>
            <tr>
                <th>Airline</th>
                <td><?php echo $flight['airline_company']; ?></td>
            </tr>
            <tr>
                <th>From</th>
                <td><?php echo $flight['dep_city'] . " (" . $flight['dep_airport'] . ")"; ?></td>
            </tr>
            <tr>
                <th>To</th>
                <td><?php echo $flight['arr_city'] . " (" . $flight['arr_airport'] . ")"; ?></td>
            </tr>
            <tr>
                <th>Departure</th>
                <td><?php echo date('M d, Y H:i', strtotime($flight['departure_time'])); ?></td>
            </tr>
            <tr>
                <th>Arrival</th>
                <td><?php echo date('M d, Y H:i', strtotime($flight['arrival_time'])); ?></td>
            </tr>
            <tr>
                <th>Duration</th>
                <td><?php echo $flight['flight_duration']; ?></td>
            </tr>
        </table>
        
        <h2>Passenger Information</h2>
        <form method="post" action="">
            <p>
                <label>Full Name:</label><br>
                <input type="text" name="passenger_name" required>
            </p>
            
            <p>
                <label>Passport Number:</label><br>
                <input type="text" name="passport_number" required>
            </p>
            
            <p>
                <label>Date of Birth:</label><br>
                <input type="date" name="date_of_birth" required>
            </p>
            
            <p>
                <label>Nationality:</label><br>
                <input type="text" name="nationality" required>
            </p>
            
            <p>
                <label>Phone:</label><br>
                <input type="text" name="phone" required>
            </p>
            
            <p>
                <label>Email:</label><br>
                <input type="email" name="email" required>
            </p>
            
            <p>
                <label>Seat Number:</label><br>
                <input type="text" name="seat_number" placeholder="e.g., 12A" required>
            </p>
            
            <p>
                <label>Class:</label><br>
                <select name="ticket_class" required>
                    <option value="Economy">Economy ($299.99)</option>
                    <option value="Business">Business ($899.99)</option>
                    <option value="First Class">First Class ($1,499.99)</option>
                </select>
            </p>
            
            <p>
                <label>Payment Method:</label><br>
                <select name="payment_method" required>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Debit Card">Debit Card</option>
                    <option value="PayPal">PayPal</option>
                </select>
            </p>
            
            <p>
                <button type="submit">Confirm Booking</button>
            </p>
        </form>
    <?php endif; ?>
    
</body>
</html>
