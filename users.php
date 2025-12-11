<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] != "admin"){
    header("location: ../login.php");
    exit;
}

require_once "config.php";

$sql = "SELECT user_id, username, email, first_name, last_name, phone, role, created_at FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$users = array();

while($row = mysqli_fetch_assoc($result)){
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Users</title>
</head>
<body>
    <h1>All Users</h1>
    
    <p><a href="dashboard.php">← Back to Dashboard</a></p>
    
    <h2>Registered Users (<?php echo count($users); ?>)</h2>
    
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Registered</th>
        </tr>
        <?php foreach($users as $user): ?>
        <tr>
            <td><?php echo $user['user_id']; ?></td>
            <td><?php echo $user['username']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['first_name']; ?></td>
            <td><?php echo $user['last_name']; ?></td>
            <td><?php echo $user['phone']; ?></td>
            <td><?php echo ucfirst($user['role']); ?></td>
            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
</body>
</html>

