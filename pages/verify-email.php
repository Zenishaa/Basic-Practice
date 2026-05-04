<?php
include "../config/database.php";

$message = "";
$error = "";
$token = "";
$resendEmail = "";

if (isset($_GET["token"])) {
    $token = trim($_GET["token"]);
}

if ($token == "") {
    $error = "Invalid verification link.";
} else {
    $sql = "SELECT id FROM users WHERE email_verification_token = ? AND email_verification_expires > NOW()";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        $updateSql = "UPDATE users SET email_verified = 1, email_verification_token = NULL, email_verification_expires = NULL WHERE id = ?";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "i", $user["id"]);
        mysqli_stmt_execute($updateStmt);

        $message = "Email verified successfully. You can login now.";
    } else {
        $error = "This verification link is invalid or expired.";

        $expiredSql = "SELECT email FROM users WHERE email_verification_token = ?";
        $expiredStmt = mysqli_prepare($conn, $expiredSql);
        mysqli_stmt_bind_param($expiredStmt, "s", $token);
        mysqli_stmt_execute($expiredStmt);
        $expiredResult = mysqli_stmt_get_result($expiredStmt);

        if (mysqli_num_rows($expiredResult) == 1) {
            $expiredUser = mysqli_fetch_assoc($expiredResult);
            $resendEmail = $expiredUser["email"];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Email</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="page">
        <div class="box">
            <h1>Verify Email</h1>

            <?php if ($message != "") { ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php } ?>

            <?php if ($error != "") { ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php } ?>

            <?php if ($resendEmail != "") { ?>
                <div class="links">
                    <a href="resend-verification.php?email=<?php echo urlencode($resendEmail); ?>">Send a new verification email</a>
                </div>
            <?php } ?>

            <div class="links">
                <a href="login.php">Go to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
