<?php

namespace models;

class Mailer {
  private $from;
  private $fromName;


  public funtion __construct(string $from, string $fromName = '') {
    $this->from = $from;
    $this->fromName = $fromName;
  }

  public function send(
    const string $to,
    const string $subject,
    const string $message,
    const bool $isHtml = true): void {    
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

    // user mail() to actually send the mail.
    return mail($to, $subject, $message, implode('\r\n', $headers));
  }

  public function sendPasswordReset($to, $resetLink) {
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
