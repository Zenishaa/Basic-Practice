<?php
include "../config/database.php";
include "../config/mail_helper.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if ($email == "") {
        $error = "Please enter your Gmail address.";
    } elseif (!isGmailAddress($email)) {
        $error = "Please enter a valid Gmail address.";
    } else {
        $sql = "SELECT id, name FROM users WHERE email = ? AND email_verified = 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            $resetToken = makeSecureToken();
            $resetExpires = date("Y-m-d H:i:s", strtotime("+30 minutes"));

            $updateSql = "UPDATE users SET password_reset_token = ?, password_reset_expires = ? WHERE id = ?";
            $updateStmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "ssi", $resetToken, $resetExpires, $user["id"]);
            mysqli_stmt_execute($updateStmt);

            $resetLink = getBaseUrl() . "/reset-password.php?token=" . $resetToken;
            $emailBody = "Hi " . htmlspecialchars($user["name"]) . ",<br><br>"
                . "Click this link to reset your password:<br>"
                . "<a href='" . $resetLink . "'>" . $resetLink . "</a><br><br>"
                . "This link will expire in 30 minutes.";

            $mailResult = sendSmtpEmail($email, $user["name"], "Reset your password", $emailBody);

            if ($mailResult !== true) {
                $error = $mailResult;
            }
        }

        if ($error == "") {
            $message = "If this verified Gmail exists, a password reset link has been sent.";
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
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php } ?>

            <?php if ($error != "") { ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php } ?>

            <form method="POST" action="">
                <label>Gmail Address</label>
                <input type="email" name="email" placeholder="Enter your registered Gmail">

                <button type="submit">Send Reset Link</button>
            </form>

            <div class="links">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
