<?php
include "../config/database.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $newPassword = trim($_POST["new_password"]);
    $confirmPassword = trim($_POST["confirm_password"]);

    if ($email == "" || $newPassword == "" || $confirmPassword == "") {
        $error = "Please fill all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($newPassword != $confirmPassword) {
        $error = "Password and confirm password do not match.";
    } elseif (strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $checkStmt = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) == 1) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $updateSql = "UPDATE users SET password = ? WHERE email = ?";
            $updateStmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "ss", $hashedPassword, $email);
            mysqli_stmt_execute($updateStmt);

            $message = "Password changed successfully. You can login now.";
        } else {
            $error = "Email not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="page">
        <div class="box">
            <h1>Forgot Password</h1>

            <?php if ($message != "") { ?>
                <div class="message"><?php echo $message; ?></div>
            <?php } ?>

            <?php if ($error != "") { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>

            <form method="POST" action="">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your registered email">

                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Enter new password">

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Enter password again">

                <button type="submit">Reset Password</button>
            </form>

            <div class="links">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
