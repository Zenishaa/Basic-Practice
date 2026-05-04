<?php
function isGmailAddress($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL)
        && strtolower(substr($email, -10)) == "@gmail.com";
}

function makeSecureToken()
{
    return bin2hex(random_bytes(32));
}

function getBaseUrl()
{
    $protocol = "http";

    if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off") {
        $protocol = "https";
    }

    $host = $_SERVER["HTTP_HOST"];
    $folder = rtrim(dirname($_SERVER["SCRIPT_NAME"]), "/\\");

    return $protocol . "://" . $host . $folder;
}

function sendSmtpEmail($toEmail, $toName, $subject, $htmlBody)
{
    $projectRoot = dirname(__DIR__);
    $autoloadFile = $projectRoot . "/vendor/autoload.php";
    $mailConfigFile = __DIR__ . "/mail.php";

    if (!file_exists($autoloadFile)) {
        return "PHPMailer is not installed. Run: composer require phpmailer/phpmailer";
    }

    if (!file_exists($mailConfigFile)) {
        return "SMTP config missing. Copy config/mail.example.php to config/mail.php and fill Gmail SMTP details.";
    }

    require_once $autoloadFile;
    require $mailConfigFile;

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtpPort;

        $mail->setFrom($smtpFromEmail, $smtpFromName);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = strip_tags(str_replace(["<br>", "<br/>", "<br />"], "\n", $htmlBody));

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Email could not be sent. Error: " . $mail->ErrorInfo;
    }
}
?>
