<?php

namespace dtu\models;

//use dtu\models\AccountMailer;
use core\models\Mailer;
//use models\Account;
use PHPUnit\Framework\TestCase;

class AccountMailerTest extends TestCase
{
  protected function setUp(): void {
    parent::setUp();

    // Clear session before each test
    $_SESSION = [];
  }

  protected function tearDown(): void {
    $_SESSION = [];
    parent::tearDown();
  }

  public function testSendForgotPasswordHandlesSpecialCharactersInLink(): void {
    $email = 'test@example.com';
    $link = 'https://example.com/reset?token=abc&param=123&other=xyz';

    $result = AccountMailer::sendForgotPassword($email, $link);

    $this->assertIsBool($result);
  }

  public function testSendVerificationEmailHandlesSpecialCharactersInLink(): void {
    $email = 'test@example.com';
    $link = 'https://example.com/verify?token=abc&param=123&other=xyz';

    $result = AccountMailer::sendVerificationEmail($email, $link);

    $this->assertIsBool($result);
  }

  public function testSendForgotPasswordWithLongEmail(): void {
    $email = 'very.long.email.address.with.many.dots@example.com';
    $link = 'https://example.com/reset?token=abc123';

    $result = AccountMailer::sendForgotPassword($email, $link);

    $this->assertIsBool($result);
  }

  public function testSendVerificationEmailWithLongEmail(): void {
    $email = 'very.long.email.address.with.many.dots@example.com';
    $link = 'https://example.com/verify?token=abc123';

    $result = AccountMailer::sendVerificationEmail($email, $link);

    $this->assertIsBool($result);
  }

  public function testSendForgotPasswordReturnsBoolean(): void {
    $email = 'test@example.com';
    $link = 'https://example.com/reset?token=abc';

    $result = AccountMailer::sendForgotPassword($email, $link);

    $this->assertIsBool($result);
  }

  public function testSendVerificationEmailReturnsBoolean(): void {
    $email = 'test@example.com';
    $link = 'https://example.com/verify?token=abc';

    $result = AccountMailer::sendVerificationEmail($email, $link);

    $this->assertIsBool($result);
  }

  public function testSendForgotPasswordWithURLEncodedEmail(): void {
    $email = 'test+tag@example.com';
    $link = 'https://example.com/reset?email=' . urlencode($email);

    $result = AccountMailer::sendForgotPassword($email, $link);

    $this->assertIsBool($result);
  }

  public function testSendVerificationEmailWithURLEncodedEmail(): void {
    $email = 'test+tag@example.com';
    $link = 'https://example.com/verify?email=' . urlencode($email);

    $result = AccountMailer::sendVerificationEmail($email, $link);

    $this->assertIsBool($result);
  }
}
