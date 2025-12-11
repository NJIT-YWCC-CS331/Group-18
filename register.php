<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

require_once "config.php";

$username = "";
$email = "";
$first_name = "";
$last_name = "";
$phone = "";
$error = "";
$success = "";


if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $phone = $_POST["phone"];
    
    $sql = "SELECT user_id FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if(mysqli_stmt_num_rows($stmt) > 0){
        $error = "Username already taken.";
    } else {
        mysqli_stmt_close($stmt);
        
        $sql = "SELECT user_id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) > 0){
            $error = "Email already registered.";
        } else {
            mysqli_stmt_close($stmt);
            
            $sql = "INSERT INTO users (username, email, password, first_name, last_name, phone) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
        
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            mysqli_stmt_bind_param($stmt, "ssssss", $username, $email, $hashed_password, $first_name, $last_name, $phone);
            
            if(mysqli_stmt_execute($stmt)){
                $success = "Registration successful! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $error = "Something went wrong. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Register</h1>
    
    <p><a href="index.php">← Back to Home</a></p>
    
    <?php if($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <?php if($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    
    <form method="post" action="">
        <p>
            <label>Username:</label><br>
            <input type="text" name="username" value="<?php echo $username; ?>" required>
        </p>
        
        <p>
            <label>Email:</label><br>
            <input type="email" name="email" value="<?php echo $email; ?>" required>
        </p>
        
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" required>
        </p>
        
        <p>
            <label>First Name:</label><br>
            <input type="text" name="first_name" value="<?php echo $first_name; ?>" required>
        </p>
        
        <p>
            <label>Last Name:</label><br>
            <input type="text" name="last_name" value="<?php echo $last_name; ?>" required>
        </p>
        
        <p>
            <label>Phone:</label><br>
            <input type="text" name="phone" value="<?php echo $phone; ?>">
        </p>
        
        <p>
            <button type="submit">Register</button>
        </p>
    </form>
    
    <p>Already have an account? <a href="login.php">Login here</a></p>
    
</body>
</html>

