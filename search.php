<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

$origin = "";
$destination = "";
$date = "";
$airline = "";
$results = array();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $origin = $_POST["origin"];
    $destination = $_POST["destination"];
    $date = $_POST["date"];
    $airline = $_POST["airline"];
    
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
            JOIN OPERATED_BY ob ON f.flight_number = ob.flight_number
            JOIN AIRCRAFT ac ON ob.aircraft_id = ac.aircraft_id
            WHERE 1=1";
    
    $params = array();
    $types = "";
    
    if(!empty($origin)){
        $sql .= " AND dep_city.city LIKE ?";
        $params[] = "%" . $origin . "%";
        $types .= "s";
    }
    
    if(!empty($destination)){
        $sql .= " AND arr_city.city LIKE ?";
        $params[] = "%" . $destination . "%";
        $types .= "s";
    }
    
    if(!empty($date)){
        $sql .= " AND DATE(dep.departure_time) = ?";
        $params[] = $date;
        $types .= "s";
    }
    
    if(!empty($airline)){
        $sql .= " AND f.airline_company LIKE ?";
        $params[] = "%" . $airline . "%";
        $types .= "s";
    }
    
    $sql .= " ORDER BY dep.departure_time";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        if(!empty($params)){
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            while($row = mysqli_fetch_assoc($result)){
                $results[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Flights</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Search Flights</h1>
    
    <p><a href="index.php">← Back to Home</a></p>
    
    <h2>Find Your Flight</h2>
    <p><em>Fill in any fields you want to search by. Leave blank to see all options.</em></p>
    
    <form method="post" action="">
        <p>
            <label>From (City):</label><br>
            <input type="text" name="origin" value="<?php echo $origin; ?>" placeholder="e.g., New York">
        </p>
        
        <p>
            <label>To (City):</label><br>
            <input type="text" name="destination" value="<?php echo $destination; ?>" placeholder="e.g., Los Angeles">
        </p>
        
        <p>
            <label>Date:</label><br>
            <input type="date" name="date" value="<?php echo $date; ?>">
        </p>
        
        <p>
            <label>Airline:</label><br>
            <input type="text" name="airline" value="<?php echo $airline; ?>" placeholder="e.g., American Airlines">
        </p>
        
        <p>
            <button type="submit">Search Flights</button>
        </p>
    </form>
    
    <?php if($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <?php if(!empty($results)): ?>
            <h2>Available Flights (<?php echo count($results); ?> found)</h2>
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
                    <th>Action</th>
                </tr>
                <?php foreach($results as $flight): ?>
                <tr>
                    <td><?php echo $flight['flight_number']; ?></td>
                    <td><?php echo $flight['airline_company']; ?></td>
                    <td><?php echo $flight['dep_city'] . " (" . $flight['dep_airport'] . ")"; ?></td>
                    <td><?php echo $flight['arr_city'] . " (" . $flight['arr_airport'] . ")"; ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($flight['departure_time'])); ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($flight['arrival_time'])); ?></td>
                    <td><?php echo $flight['flight_duration']; ?></td>
                    <td><?php echo $flight['aircraft_model']; ?></td>
                    <td>
                        <a href="booking.php?flight=<?php echo $flight['flight_number']; ?>">Book Now</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No flights found. Try different search criteria.</p>
        <?php endif; ?>
    <?php endif; ?>
    
</body>
</html>
