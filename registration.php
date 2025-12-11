<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: index.php");
    exit;
}

include "includes/config.php";

$username = "";
$email = "";
$password = "";
$first_name = "";
$last_name = "";
$phone = "";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $phone = $_POST["phone"];

    
    if (empty($username) || empty($email) || empty($password) || empty($first_name) || empty($last_name)) {
        $error = "Please fill in all required fields.";
    } else {

        $checkUser = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($checkUser) > 0) {
            $error = "Username already taken.";
        } else {

            $checkEmail = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
            if (mysqli_num_rows($checkEmail) > 0) {
                $error = "Email already registered.";
            } else {

                $hashed = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, email, password, first_name, last_name, phone)
                        VALUES ('$username', '$email', '$hashed', '$first_name', '$last_name', '$phone')";

                if (mysqli_query($conn, $sql)) {
                    $success = "Registration successful! Redirecting to login...";
                    header("refresh:2;url=login.php");
                } else {
                    $error = "Something went wrong. Try again.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

<h2>Register</h2>

<?php
if (!empty($error)) {
    echo "<p style='color:red;'>$error</p>";
}

if (!empty($success)) {
    echo "<p style='color:green;'>$success</p>";
}
?>

<form method="post" action="register.php">

    <label>Username:</label><br>
    <input type="text" name="username" value="<?php echo $username; ?>"><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo $email; ?>"><br><br>

    <label>Password:</label><br>
    <input type="password" name="password"><br><br>

    <label>First Name:</label><br>
    <input type="text" name="first_name" value="<?php echo $first_name; ?>"><br><br>

    <label>Last Name:</label><br>
    <input type="text" name="last_name" value="<?php echo $last_name; ?>"><br><br>

    <label>Phone (optional):</label><br>
    <input type="text" name="phone" value="<?php echo $phone; ?>"><br><br>

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="login.php">Login here</a></p>

</body>
</html>


