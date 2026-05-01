<?php
include "../config/database.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirm_password"]);

    if ($name == "" || $email == "" || $phone == "" || $password == "" || $confirmPassword == "") {
        $error = "Please fill all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($password != $confirmPassword) {
        $error = "Password and confirm password do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $checkStmt = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            $error = "This email is already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertSql = "INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)";
            $insertStmt = mysqli_prepare($conn, $insertSql);
            mysqli_stmt_bind_param($insertStmt, "ssss", $name, $email, $phone, $hashedPassword);
            mysqli_stmt_execute($insertStmt);

            $message = "Signup successful. You can login now.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="page">
        <div class="box">
            <h1>Signup</h1>

            <?php if ($message != "") { ?>
                <div class="message"><?php echo $message; ?></div>
            <?php } ?>

            <?php if ($error != "") { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>

            <form method="POST" action="">
                <label>Name</label>
                <input type="text" name="name" placeholder="Enter your name">

                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email">

                <label>Phone</label>
                <input type="text" name="phone" placeholder="Enter your phone number">

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password">

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Enter password again">

                <button type="submit">Create Account</button>
            </form>

            <div class="links">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</body>
</html>
