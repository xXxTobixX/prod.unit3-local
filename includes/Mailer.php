<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

class Mailer {
    public static function sendOTP($email, $otp) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'localproductexportdevelopment@gmail.com'; // Replace with your Gmail
            $mail->Password   = 'qckp ssut rllj eynb';    // Replace with your App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('localproductexportdevelopment@gmail.com', 'Local Product & Export Development');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Verification Code';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #2563eb;'>LGU 3: Local Product & Export Development Verification</h2>
                    <p>Hello,</p>
                    <p>Your one-time password (OTP) for logging in is:</p>
                    <div style='font-size: 32px; font-weight: bold; color: #2563eb; letter-spacing: 5px; margin: 20px 0;'>$otp</div>
                    <p>This code will expire in 10 minutes.</p>
                    <p>If you did not request this code, please ignore this email.</p>
                </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    public static function sendPasswordReset($email, $otp) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'localproductexportdevelopment@gmail.com'; 
            $mail->Password   = 'qckp ssut rllj eynb';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('localproductexportdevelopment@gmail.com', 'Local Product & Export Development');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #2563eb;'>Password Reset Request</h2>
                    <p>Hello,</p>
                    <p>We received a request to reset your password. Use the code below to proceed:</p>
                    <div style='font-size: 32px; font-weight: bold; color: #dc2626; letter-spacing: 5px; margin: 20px 0;'>$otp</div>
                    <p>This code will expire in 10 minutes.</p>
                    <p>If you did not request this, please ignore this email and your password will remain unchanged.</p>
                </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
