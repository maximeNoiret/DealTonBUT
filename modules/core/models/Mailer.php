<?php

// TODO: Consider making this a singleton


namespace core\models;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


class Mailer {
  /**
   * @return string : Returns the API key stored in the .apkey file.
   */
    public static function getApiKey(): string {
        $file = fopen(__DIR__ . '/../../../.apkey', 'r') or die('File didn\'t open.');
        $apiKey = fgets($file);
        fclose($file);
        return rtrim($apiKey);
    }

  /**
   * @param string $fromUsername : Username part of the from address.
   * @param string $toAddress : Recipient email address.
   * @param string $toName : Recipient name.
   * @param string $subject : Email subject.
   * @param string $body : Email body.
   * @param string $altBody : Alternative email body for non-HTML email clients.
   * @param bool $isHTML : Indicates if the email body is in HTML format.
   * @return bool : Returns true if mail sent successfully, false otherwise.
   */
    public static function sendMail(string $fromUsername, string $toAddress, string $toName, string $subject, string $body, string $altBody, bool $isHTML): bool {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;  // debug verbose output
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
            $mail->AltBody = $altBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // TODO: REMOVE THIS, PURELY FOR DEBUGGING
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            exit;
        }
    }

    /**
     * @param $email : Email of the user to send reset link.
     * @param $link  : Reset link.
     */
    public static function sendForgotPassword(string $email, string $link): bool {
        $body = '<h1>DealTonBUT - Mot de passe oublié</h1>
<p>Vous recevez ce mail suite à une requête de réinitialisation de mot de passe sur DealTonBUT.</p>
<p>Veuillez cliquer <a href="' . $link . '">ICI</a> afin de réinitialiser votre mot de passe.</p><br>
<p>Si vous ne pouvez pas cliquer sur le lien, copiez collez celui-ci:<p>
<p style="color: #0077FF">' . $link . '</p><br><br>
<p>Si vous n\'avez pas fait cette requête, vous pouvez ignorer ce mail.</p><br>
<h5>DealTonBUT - On n\'a pas encore de slogan.</h5>';
        $altBody = 'DealTonBUT - Mot de passe oublié\n\n' .
            'Vous recevez ce mail suite à une requête de réinitialisation de mot de passe sur DealTonBUT.\n' .
            'Veuillez utiliser ce lien:\n ' . $link . '\n\n' .
            'Si vous n\'avez pas fait cette requête, vous pouvez ignorer ce mail.\n\n' .
            'DealTonBUT - On n\'a pas encore de slogan.';
        $name = 'REPLACE ME WITH FUNCTION CALL';  // TODO: make a function to get name from email and use it here

        return self::sendMail('no-reply', $email, $name, 'Forgot Password', $body, $altBody, true);
    }
}
