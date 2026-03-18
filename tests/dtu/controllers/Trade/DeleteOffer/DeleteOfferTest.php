<?php

namespace Trade\DeleteOffer;

use controllers\Trade\DeleteOffer\DeleteOffer;
use dtu\models\TradeDB;
use PHPUnit\Framework\TestCase;
use models\AccountDB;

/**
 * @runTestsInSeparateProcesses
 */
class DeleteOfferTest extends TestCase
{

  private DeleteOffer $deleteOffer;
  private AccountDB $dbAccConn;
  private TradeDB $dbTradeConn;
  private string $testEmail01='test@testUser01.com';
  private string $testUsername01='testUser01';
  private string $testPassword01='password';
  private int $testOuid01=0;

  public function setUp(): void{
    // create a DeleteOffer instance
    $this->deleteOffer = new DeleteOffer();
    // set up for database related unit tests
    $this->dbAccConn = AccountDB::getInstance();
    $this->dbTradeConn = TradeDB::getInstance();

    // create a test account, that will have to be deleted in teardown()
    $this->dbAccConn->registerAccount($this->testUsername01,$this->testEmail01);
    // no need to hash password here
    $this->dbAccConn->updatePassword($this->testEmail01, $this->testPassword01);
    $this->dbAccConn->setRole($this->testEmail01,'student');

    // OFFER
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
    $offer = $this->dbTradeConn->executeQuery(
      'SELECT ouid FROM offer WHERE owner =\'test@testUser01.com\';'
    );
    // second version made by autocomplete, i don't use it because i don't really understand it
    // --Maxime: That version uses parameterized queries to customize the statement to allow dynamic testing values.
    /*'SELECT ouid FROM offer WHERE owner =? AND title = ? AND description =?',
      [$this->testEmail01, $title, $description]*/
    $this->testOuid01 = $offer[0]['ouid'];
  }

  public function testRedirectsToLoginIfNotLoggedIn()
  {
    $_SESSION['logged-in'] = null;

    $this->deleteOffer->control();
    $headers = xdebug_get_headers();

    $this->assertContains('Location: /user/login', $headers);
  }

  public function testRedirectsToMarketplaceIfIdNotSet()
  {
    $_SESSION['logged-in'] = true;
    $_GET['id'] = null;

    $this->deleteOffer->control();
    $headers = xdebug_get_headers();

    $this->assertContains('Location: /marketplace', $headers);
  }

  public function testRedirectsToMarketplaceIfOfferDoesNotExist()
  {
    $_SESSION['logged-in'] = true;
    $_SESSION['email'] = '';
    $_GET['id'] = null;

    $this->deleteOffer->control();
    $headers = xdebug_get_headers();

    $this->assertContains('Location: /marketplace', $headers);
  }

  public function testRedirectsToMarketplaceIfNotOfferOwner()
  {
    $_SESSION['logged-in'] = true;
    $_SESSION['email'] = 'email@thatDon\'exist.com';
    // the offer with id 2 is owned by another user
    $_GET['id'] = 2;


    $this->deleteOffer->control();
    $headers = xdebug_get_headers();

    $this->assertContains('Location: /marketplace', $headers);
  }

  public function testDeletesOfferIfOwnerAndRedirectsToMarketplace()
  {
    $_SESSION['logged-in'] = true;
    $_SESSION['email'] = $this->testEmail01;
    $_GET['id'] = $this->testOuid01;

    $this->deleteOffer->control();
    $headers = xdebug_get_headers();
    $this->assertContains('Location: /marketplace', $headers);
    }


  // // // // // // // // // // //

  public function testDoesNotResolveIncorrectPath(){
    $this->assertFalse(DeleteOffer::resolve('/offre/wrongpath', 'GET'));
  }

  public function testDoesNotResolveIncorrectMethod(){
    $this->assertFalse(DeleteOffer::resolve('/offre/delete', 'POST'));
  }

  public function testResolvesCorrectPathAndMethod(){
    $this->assertTrue(DeleteOffer::resolve('/offre/delete', 'GET'));
  }

  public function tearDown(): void
  {
    $this->dbTradeConn->deleteOffer($this->testOuid01);
    $this->dbAccConn->deleteUser($this->testEmail01);
  }
}
