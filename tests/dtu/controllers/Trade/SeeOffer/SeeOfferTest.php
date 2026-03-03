<?php

namespace Trade\SeeOffer;

use controllers\Trade\SeeOffer\SeeOffer;
use dtu\models\TradeDB;
use dtu\views\Trade\SeeOffer\SeeOfferView;
use models\AccountDB;
use PHPUnit\Framework\TestCase;

class SeeOfferTest extends TestCase
{

  private SeeOffer $seeOffer;
  private AccountDB $dbAccConn;
  private TradeDB $dbTradeConn;
  private string $testEmail01='test@testUser01.com';
  private string $testUsername01='testUser01';
  private string $testPassword01='password';
  private int $testOuid01=0;
  private array $offerDetails = [];
  public function setUp(): void
  {
    // set up for database related unit tests
    $this->dbAccConn = AccountDB::getInstance();
    $this->dbTradeConn = TradeDB::getInstance();

    // create a test account, that will have to be deleted in teardown()
    $this->dbAccConn->registerAccount($this->testUsername01,$this->testEmail01);
    // no need to hash password here
    $this->dbAccConn->updatePassword($this->testEmail01, $this->testPassword01);
    $this->dbAccConn->setRole($this->testEmail01,'student');

    //param of the offer :
    $_SESSION['email'] = $this->testEmail01;
    $title = 'UNIT_TEST_OFFER';
    $price = '100';
    $end_date = '2077-12-31';
    $description = 'UNIT_TEST_DESC';
    $this->dbTradeConn->insertOffer(
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
    $_GET['id'] = $this->testOuid01;
    $this->offerDetails = $this->dbTradeConn->getOffer($_GET['id']);

    $this->seeOffer = new SeeOffer();
  }

  public function testOwnerOfOfferReturnFalse()
  {
    $_SESSION['email'] = '';
    $this->assertFalse($this->seeOffer->isOwnerOfOffer());
  }

  public function testOwnerOfOfferReturnTrue()
  {
    $_SESSION['email'] = $this->testEmail01;
    $this->assertTrue($this->seeOffer->isOwnerOfOffer());
  }

  public function testButtonOfferDelete()
  {
    $_SESSION['email'] = $this->testEmail01;
    $expected = '<a class="button-delete" href="/offre/delete?id=' . $this->seeOffer::$id . '">Delete</a>';
    $this->assertEquals($expected, $this->seeOffer->buttonOffer());
  }

  public function testButtonOfferBuy()
  {
    $_SESSION['email'] = '';
    $expected = '<a class="button-buy" href="/offre/buy?id=' . $this->seeOffer::$id . '">Buy</a>';
    $this->assertEquals($expected, $this->seeOffer->buttonOffer());
  }

  /**
   * @runInSeparateProcess
   */
  public function testControlNotLogged()
  {
    $_SESSION['email'] = null;
    $this->seeOffer->control();
    $headers = xdebug_get_headers();
    $this->assertContains('Location: /user/login', $headers);
  }

  // note : i don't know how to test the creation of a new class
  public function testControlLogged(){
    $_SESSION['email'] = $this->testEmail01;
    $_SESSION['logged-in'] = true;
    // no redirection should happen
    ob_start();
    $this->seeOffer->control();
    ob_end_clean();
    $headers = xdebug_get_headers();
    $this->assertNotContains('Location: /user/login', $headers);
  }


  public function testDoesNotResolveIncorrectPath(){
    $this->assertFalse(SeeOffer::resolve('/offre/wrongpath', 'GET'));
  }

  public function testDoesNotResolveIncorrectMethod(){
    $this->assertFalse(SeeOffer::resolve('/offre/voir', 'POST'));
  }

  public function testResolvesCorrectPathAndMethod(){
    $this->assertTrue(SeeOffer::resolve('/offre/voir', 'GET'));
  }

  public function tearDown(): void
  {
    $this->dbTradeConn->deleteOffer($this->testOuid01);
    $this->dbAccConn->deleteUser($this->testEmail01);
  }
}
