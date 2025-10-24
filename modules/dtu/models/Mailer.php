<?php

namespace models;

class Mailer {
  private string $from;
  private string $fromName;


  /**
   * @description Constructs a Mailer object with specified sender details.
   * @param string $from The email address from which the email will be sent.
   * @param string $fromName Optional name to display as the sender.
   */
  public function __construct(string $from, string $fromName = '') {
    $this->from = $from;
    $this->fromName = $fromName;
  }

  /**
   * @description Sends an email with the specified parameters.
   * @param string $to The recipient's email address.
   * @param string $subject The subject of the email.
   * @param string $message The body of the email.
   * @param bool $isHtml Indicates whether the email content is HTML or plain text. Default is true (HTML).
   * @return bool Returns true if the email was sent successfully, false otherwise.
   */
  public function send(
     string $to,
     string $subject,
     string $message,
     bool $isHtml = true): bool {    
    $headers = [];
    
    // 'From' header object
    if ($this->fromName) {
      $headers[] = 'From: ' . $this->fromName . ' <' . $this->from . '>';
    } else {
      $headers[] = 'From: ' . $this->from;
    }

    // 'Reply-To' header object
    $headers[] = 'Reply-To: ' . $this->from;

    // 'Content-Type' header object
    if ($isHtml) {
      $headers[] = 'MIME-Version: 1.0';  // why 1.0?
      $headers[] = 'Content-Type: text/html; charset=UTF-8';
    } else {  // assume plain text email
      $headers[] = 'Content-Type: text/plan; charset=UTF-8';
    }

    // Additional header for PHP version and deliverability
    $headers[] = "X-Mailer: PHP/" . phpversion();

    // use mail() to actually send the mail.
    return mail($to, $subject, $message, implode('\n', $headers));
  }

    /**
     * @description Sends a password reset email to the specified recipient.
     * @param string $to The recipient's email address.
     * @param string $resetLink The password reset link to be included in the email.
     * @return bool Returns true if the email was sent successfully, false otherwise.
     */

  public function sendPasswordReset(string $to, string $resetLink): bool {
    $subject = "Password Reset Request";
        
    $message = "
      <html>
      <body>
        <h2>Password Reset Request</h2>
        <p>You requested a password reset. Click the link below to reset your password:</p>
        <p><a href='{$resetLink}'>Reset Password</a></p>
        <p>Or copy and paste this link into your browser:</p>
        <p>{$resetLink}</p>
        <p>This link will expire in 10 minutes.</p>
        <p>If you didn't request this, please ignore this email.</p>
      </body>
      </html>
      ";
      return $this->send($to, $subject, $message);
    }
}
