<?php

// TODO: Consider making this a singleton


namespace core\models;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


class Mailer {
  /**
   * @return string : Returns the API key stored in the ..apkey file.
   */
  // NOTE: Make sure to get the ..apkey file, it is not in the repository
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
  public static function sendMail(string $fromUsername,
                                  string $toAddress,
                                  string $toName,
                                  string $subject,
                                  string $body,
                                  string $altBody,
                                  bool   $isHTML): bool {
    $mail = new PHPMailer(true);

    try {
      //Server settings
      //$mail->SMTPDebug = SMTP::DEBUG_SERVER;  // debug verbose output
      $mail->isSMTP();
      $mail->Host = 'live.smtp.mailtrap.io';
      $mail->SMTPAuth = true;
      $mail->Username = 'api';
      $mail->Password = Mailer::getApiKey();
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port = 465;  // use 587 if `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
      $mail->CharSet = 'UTF-8';

      //Recipients
      $mail->setFrom($fromUsername . '@dealtonbut.app', 'DealTonBUT');
      $mail->addAddress($toAddress, $toName);

      //Content
      $mail->isHTML($isHTML);
      $mail->Subject = $subject;
      $mail->Body = $body;
      $mail->AltBody = $altBody;

      $mail->send();
      return true;
    } catch (Exception $e) {
      // TODO: REMOVE THIS, PURELY FOR DEBUGGING
      echo "ChatController could not be sent. Mailer Error: {$mail->ErrorInfo}";
      exit;
    }
  }
}
