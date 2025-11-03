<?php

// TODO: Consider making this a singleton


namespace core\models;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


class Mailer {
    public static function getApiKey(): string {
        $file = fopen(__DIR__ . '/../../../.apkey', 'r') or die('File didn\'t open.');
        $apiKey = fgets($file);
        fclose($file);
        return rtrim($apiKey);
    }

    public static function sendMail(string $fromUsername, string $toAddress, string $toName, string $subject, string $body, bool $isHTML): bool {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // debug verbose output
            $mail->isSMTP();
            $mail->Host       = 'live.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'api';
            $mail->Password   = Mailer::getApiKey();
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;  // use 587 if `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom($fromUsername . '@dealtonbut.app', 'DealTonBUT');
            $mail->addAddress($toAddress, $toName);

            //Content
            $mail->isHTML($isHTML);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // TODO: REMOVE THIS, PURELY FOR DEBUGGING
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            exit;
        }
    }
}
