<?php
include "../config/database.php";

$message = "";
$error = "";
$token = "";
$showForm = false;

if (isset($_GET["token"])) {
    $token = trim($_GET["token"]);
}

if ($token == "") {
    $error = "Invalid reset link.";
} else {
    $sql = "SELECT id FROM users WHERE password_reset_token = ? AND password_reset_expires > NOW()";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $showForm = true;
    } else {
        $error = "This reset link is invalid or expired.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $showForm) {
    $newPassword = trim($_POST["new_password"]);
    $confirmPassword = trim($_POST["confirm_password"]);

    if ($newPassword == "" || $confirmPassword == "") {
        $error = "Please fill all fields.";
    } elseif ($newPassword != $confirmPassword) {
        $error = "Password and confirm password do not match.";
    } elseif (strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateSql = "UPDATE users SET password = ?, password_reset_token = NULL, password_reset_expires = NULL WHERE id = ?";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "si", $hashedPassword, $user["id"]);
        mysqli_stmt_execute($updateStmt);

        $showForm = false;
        $message = "Password changed successfully. You can login now.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="page">
        <div class="box">
            <h1>Reset Password</h1>

            <?php if ($message != "") { ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php } ?>

            <?php if ($error != "") { ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php } ?>

            <?php if ($showForm) { ?>
                <form method="POST" action="">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="Enter new password">

                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Enter password again">

                    <button type="submit">Change Password</button>
                </form>
            <?php } ?>

            <div class="links">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
