<?php
include "../config/database.php";
include "../config/mail_helper.php";

$message = "";
$error = "";
$email = "";
date_default_timezone_set('Asia/Kolkata');
if (isset($_GET["email"])) {
    $email = trim($_GET["email"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if ($email == "") {
        $error = "Please enter your Gmail address.";
    } elseif (!isGmailAddress($email)) {
        $error = "Please enter a valid Gmail address.";
    } else {
        $sql = "SELECT id, name, email_verified FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            if ($user["email_verified"] == 1) {
                $message = "This Gmail address is already verified. You can login now.";
            } else {
                $verificationToken = makeSecureToken();

                $updateSql = "UPDATE users SET email_verification_token = ?, email_verification_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?";
                $updateStmt = mysqli_prepare($conn, $updateSql);
                mysqli_stmt_bind_param($updateStmt, "si", $verificationToken, $user["id"]);
                mysqli_stmt_execute($updateStmt);

                $verifyLink = getBaseUrl() . "/verify-email.php?token=" . $verificationToken;
                $emailBody = "Hi " . htmlspecialchars($user["name"]) . ",<br><br>"
                    . "Please verify your email address by clicking this link:<br>"
                    . "<a href='" . $verifyLink . "'>" . $verifyLink . "</a><br><br>"
                    . "This link will expire in 1 hour.";

                $mailResult = sendSmtpEmail($email, $user["name"], "Verify your email", $emailBody);

                if ($mailResult === true) {
                    $message = "Verification email sent. Please check your Gmail inbox.";
                } else {
                    $error = $mailResult;
                }
            }
        } else {
            $message = "If this Gmail is registered, a verification email has been sent.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resend Verification</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="page">
        <div class="box">
            <h1>Verify Gmail</h1>

            <?php if ($message != "") { ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php } ?>

            <?php if ($error != "") { ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php } ?>

            <form method="POST" action="">
                <label>Gmail Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your Gmail">

                <button type="submit">Send Verification Email</button>
            </form>

            <div class="links">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
