<?php
session_start();
include "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($email == "" || $password == "") {
        $error = "Please enter email and password.";
    } else {
        $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];

                header("Location: home.php");
                exit();
            } else {
                $error = "Wrong email or password.";
            }
        } else {
            $error = "Wrong email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="page">
        <div class="box">
            <h1>Login</h1>

            <?php if ($error != "") { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>

            <form method="POST" action="">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email">

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password">

                <button type="submit">Login</button>
            </form>

            <div class="links">
                <a href="forgot.php">Forgot Password?</a><br>
                New user? <a href="signup.php">Create account</a>
            </div>
        </div>
    </div>
</body>
</html>
