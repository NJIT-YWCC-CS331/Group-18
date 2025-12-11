<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] != "admin"){
    header("location: ../login.php");
    exit;
}

require_once "config.php";

$sql = "SELECT f.flight_number, f.airline_company, f.flight_duration,
        dep.airport_code as dep_airport, dep_city.city as dep_city,
        arr.airport_code as arr_airport, arr_city.city as arr_city,
        dep.departure_time, arr.arrival_time,
        ac.aircraft_model, ac.air_craft_capacity
        FROM FLIGHT f
        JOIN DEPARTS dep ON f.flight_number = dep.flight_number
        JOIN AIRPORT dep_city ON dep.airport_code = dep_city.airport_code
        JOIN ARRIVES arr ON f.flight_number = arr.flight_number
        JOIN AIRPORT arr_city ON arr.airport_code = arr_city.airport_code
        LEFT JOIN OPERATED_BY ob ON f.flight_number = ob.flight_number
        LEFT JOIN AIRCRAFT ac ON ob.aircraft_id = ac.aircraft_id
        ORDER BY dep.departure_time";

$result = mysqli_query($conn, $sql);
$flights = array();

while($row = mysqli_fetch_assoc($result)){
    $flights[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Flights</title>
</head>
<body>
    <h1>All Flights</h1>
    
    <p><a href="dashboard.php">← Back to Dashboard</a></p>
    
    <h2>All Scheduled Flights (<?php echo count($flights); ?>)</h2>
    
    <table border="1" cellpadding="10">
        <tr>
            <th>Flight Number</th>
            <th>Airline</th>
            <th>From</th>
            <th>To</th>
            <th>Departure</th>
            <th>Arrival</th>
            <th>Duration</th>
            <th>Aircraft</th>
            <th>Capacity</th>
        </tr>
        <?php foreach($flights as $flight): ?>
        <tr>
            <td><?php echo $flight['flight_number']; ?></td>
            <td><?php echo $flight['airline_company']; ?></td>
            <td><?php echo $flight['dep_city'] . " (" . $flight['dep_airport'] . ")"; ?></td>
            <td><?php echo $flight['arr_city'] . " (" . $flight['arr_airport'] . ")"; ?></td>
            <td><?php echo date('M d, Y H:i', strtotime($flight['departure_time'])); ?></td>
            <td><?php echo date('M d, Y H:i', strtotime($flight['arrival_time'])); ?></td>
            <td><?php echo $flight['flight_duration']; ?></td>
            <td><?php echo $flight['aircraft_model']; ?></td>
            <td><?php echo $flight['air_craft_capacity']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
</body>
</html>

