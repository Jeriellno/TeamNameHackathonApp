<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendNotification($to, $subject, $body, $attachment_path = null) {
    $mail = new PHPMailer(true);

    try {
        // --- GMAIL SETTINGS ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'concerntrack1@gmail.com';    // PALITAN MO ITO
        $mail->Password   = 'phdtsaljztjtlbko';      // PALITAN MO NG 16-DIGIT APP PASSWORD
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // --- EMAIL CONTENT ---
        $mail->setFrom('concerntrack1@gmail.com', 'ConcernHub - Admin');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // --- ATTACHMENT LOGIC ---
        if ($attachment_path !== null && file_exists($attachment_path)) {
            $mail->addAttachment($attachment_path);
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the detailed error message to the server's error log for debugging
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>