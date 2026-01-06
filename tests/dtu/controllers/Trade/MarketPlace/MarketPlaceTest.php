<?php

namespace Trade\MarketPlace;

use controllers\Trade\MarketPlace\MarketPlace;
use dtu\models\TradeDB;
use models\AccountDB;
use PHPUnit\Framework\TestCase;
//TODO: finish the tests
/**
 * @runTestsInSeparateProcesses
 */
class MarketPlaceTest extends TestCase
{
  private MarketPlace $marketPlace;
  private AccountDB $dbAccConn;
  private TradeDB $dbTradeConn;
  private string $testEmail01='test@testUser01.com';
  private string $testUsername01='testUser01';
  private string $testPassword01='password';
  private int $testOuid01=12;

  public function setUp(): void{
    // create a DeleteOffer instance
    $this->marketPlace = new MarketPlace();
    $this->dbAccConn = AccountDB::getInstance();
    $this->dbTradeConn = TradeDB::getInstance();

    // create a test account, that will have to be deleted in teardown()
    $this->dbAccConn->registerAccount($this->testUsername01,$this->testEmail01);
    // no need to hash password here
    $this->dbAccConn->updatePassword($this->testEmail01, $this->testPassword01);
    $this->dbAccConn->setRole($this->testEmail01,'student');

    // OFFER
    //  param of the offer :
    $_SESSION['email'] = $this->testEmail01;
    $title = 'UNIT_TEST_OFFER';
    $price = '100';
    $end_date = '2077-12-31';
    $description = 'UNIT_TEST_DESC';
    $this->dbTradeConn->insertOffre(
      $_SESSION['email'],
      $title,
      (float)$price,
      $description,
      $end_date
    );

    // get the ouid of the offers of the test user
    $offer = $this->dbAccConn->executeQuery(
      'SELECT ouid FROM offer WHERE owner =\'test@testUser01.com\';'
    );
    $this->testOuid01 = $offer[0]['ouid'];
  }

  public function testRedirectsToLoginIfNotLogged(): void {
    $_SESSION['logged-in'] = false;
    ob_start();
    $this->marketPlace->control();
    ob_get_clean();
    $header= xdebug_get_headers();
    $this->assertContains('Location: /user/login', $header);
  }

  public function testGetOffersNoSortNoSearch(): void {
    $_GET = [];
    $result = MarketPlace::getOffers();
    $this->assertIsString($result);
    $this->assertStringContainsString('<section class="offer-grid">', $result);
  }

  public function testDoesNotResolveIncorrectPath(){
    $this->assertFalse(MarketPlace::resolve('/offre/wrongpath', 'GET'));
  }

  public function testDoesNotResolveIncorrectMethod(){
    $this->assertFalse(MarketPlace::resolve('/marketplace', 'POST'));
  }

  public function testResolvesCorrectPathAndMethod(){
    $this->assertTrue(MarketPlace::resolve('/marketplace', 'GET'));
  }

  public function tearDown(): void{
    $this->dbTradeConn->deleteOffer($this->testOuid01);
    $this->dbAccConn->deleteUser($this->testEmail01);
  }
}
