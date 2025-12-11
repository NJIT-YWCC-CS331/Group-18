<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

require_once "config.php";

$username = "";
$password = "";
$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    $sql = "SELECT user_id, username, password, role, first_name, last_name, email FROM users WHERE username = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) == 1){
            mysqli_stmt_bind_result($stmt, $user_id, $username, $hashed_password, $role, $first_name, $last_name, $email);
            mysqli_stmt_fetch($stmt);
            
            if(password_verify($password, $hashed_password)){
   
                $_SESSION["loggedin"] = true;
                $_SESSION["user_id"] = $user_id;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = $role;
                $_SESSION["first_name"] = $first_name;
                $_SESSION["last_name"] = $last_name;
                $_SESSION["email"] = $email;
              
                if($role == "admin"){
                    header("location: admin/dashboard.php");
                } else {
                    header("location: index.php");
                }
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Login</h1>
    
    <p><a href="index.php">← Back to Home</a></p>
    
    <?php if($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <form method="post" action="">
        <p>
            <label>Username:</label><br>
            <input type="text" name="username" value="<?php echo $username; ?>" required>
        </p>
        
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" required>
        </p>
        
        <p>
            <button type="submit">Login</button>
        </p>
    </form>
    
    <p>Don't have an account? <a href="register.php">Register here</a></p>
    
</body>
</html>

