<?php

namespace Trade\SeeOtherAccount;

use controllers\Trade\SeeOtherAccount\SeeOtherAccount;
use models\AccountDB;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class SeeOtherAccountTest extends TestCase
{
  private SeeOtherAccount $seeOtherAccount;
  private string $testEmail01;
  function setUp(): void
  {
    $this->seeOtherAccount = new SeeOtherAccount();
    AccountDB::getInstance()->registerAccount('testUser01', 'testUser01@example.com');
    $this->testEmail01 = 'testUser01@example.com';
  }

  function testResolve(): void
  {
    $this->assertTrue(SeeOtherAccount::resolve('/account/see', 'GET'));
    $this->assertFalse(SeeOtherAccount::resolve('/account/see', 'POST'));
    $this->assertTrue(SeeOtherAccount::resolve('/account/see?email=test', 'GET'));
    $this->assertFalse(SeeOtherAccount::resolve('/account/see?email=test', 'POST'));
  }

  public function testControlNotLogged()
  {
    $_SESSION['logged-in'] = null;
    ob_start();
    $this->seeOtherAccount->control();
    ob_end_clean();
    $headers = xdebug_get_headers();
    $this->assertContains('Location: /user/login', $headers);
  }

  function testControlLoggedAndSeeOtherAccountPage()
  {
    $_SESSION['logged-in'] = true;
    $_GET['email']= $this->testEmail01;
    $_SESSION['email'] = 'email2@test.com';
    ob_start();
    $this->seeOtherAccount->control();
    $content = ob_get_clean();
    $this->assertStringContainsString('<title>Account - DealTonBUT</title>', $content);
  }

  function testControlLoggedAndSeeOwnAccountPage()
  {
    $_SESSION['logged-in'] = true;
    $_GET['email']= $this->testEmail01;
    $_SESSION['email'] = $this->testEmail01;
    ob_start();
    $this->seeOtherAccount->control();
    $content = ob_get_clean();
    $this->assertStringContainsString('<title>Account - DealTonBUT</title>', $content);
  }

  function tearDown(): void
  {
    AccountDB::getInstance()->deleteUser('testUser01@example');
  }

}
