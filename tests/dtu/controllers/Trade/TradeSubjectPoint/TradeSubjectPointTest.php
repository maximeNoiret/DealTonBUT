<?php

namespace Trade\TradeSubjectPoint;


use controllers\Trade\TradeSubjectPoint\TradeSubjectPoint;
use dtu\models\TradeDB;
use models\AccountDB;
use PHPUnit\Framework\TestCase;

class TradeSubjectPointTest extends TestCase
{
  private TradeSubjectPoint $tradeSubjectPoint;
  private AccountDB $dbAccConn;
  private TradeDB $dbTradeConn;
  public function setUp(): void
  {
    $this->tradeSubjectPoint = new TradeSubjectPoint();

  }

  /**
   *@runInSeparateProcess
   */
  public function testRedirectsToLoginIfNotLogged(): void {
    // Simulate not logged-in user
    $_SESSION['logged-in'] = false;

    // Start output buffering to capture headers
    ob_start();
    $this->tradeSubjectPoint->control();
    ob_end_clean();

    // Check if the Location header is set to redirect to login
    $this->assertTrue(headers_sent() || in_array('Location: /user/login', xdebug_get_headers()));
  }

  //NOTE : there is no need to test resolve() with incorrect method as it accept both GET and POST
  public function testDoesNotResolveIncorrectPath(){
    $this->assertFalse(TradeSubjectPoint::resolve('/offre/wrongpath', 'GET'));
  }
  public function testResolvesCorrectPathAndMethod(){
    $this->assertTrue(TradeSubjectPoint::resolve('/trade/points', 'GET'));
  }

  public function tearDown(): void
  {
    unset($this->tradeSubjectPoint);
  }
}
