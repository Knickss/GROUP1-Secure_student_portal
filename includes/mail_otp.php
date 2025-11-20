<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../phpmailer/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/SMTP.php';
require_once __DIR__ . '/../phpmailer/Exception.php';

function send_otp_email(string $toEmail, string $toName, string $otp): bool {
    $mail = new PHPMailer(true);

    try {
        // SMTP settings (Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'escolinkcentra@gmail.com';     // change this
        $mail->Password   = 'xmbv orhk pjoe uyqh';       // change this
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender
        $mail->setFrom('escolinkcentra@gmail.com', 'Escolink Centra'); // change if you want
        $mail->addAddress($toEmail, $toName);

        // Email Body
        $mail->isHTML(true);
        $mail->Subject = 'Your Escolink Centra Login Code';
        $mail->Body    = "
            <p>Hello <strong>{$toName}</strong>,</p>
            <p>Your one-time login code is:</p>
            <h2>{$otp}</h2>
            <p>This code expires in <strong>5 minutes</strong>.</p>
        ";
        $mail->AltBody = "Your one-time login code is: {$otp}";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('OTP email error: ' . $mail->ErrorInfo);
        return false;
    }
}
