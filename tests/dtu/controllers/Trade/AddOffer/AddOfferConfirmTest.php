<?php
namespace Trade\AddOffer;

use controllers\Trade\AddOffer\AddOfferConfirm;
use dtu\models\TradeDB;
use models\AccountDB;
use PHPUnit\Framework\TestCase;

class AddOfferConfirmTest extends TestCase
{
  private AddOfferConfirm $addOfferConfirm;
  private AccountDB $dbAccConn;
  private TradeDB $dbTradeConn;
  private string $testEmail01='test@testUser01.com';
  private string $testUsername01='testUser01';
  private string $testPassword01='password';
  private int $testOuid01=0;

  public function setUp(): void{
    // set up for database related unit tests
    $this->dbAccConn = AccountDB::getInstance();
    $this->dbTradeConn = TradeDB::getInstance();
    // create a test account, that will have to be deleted in teardown()
    $this->dbAccConn->registerAccount($this->testUsername01,$this->testEmail01);
    // no need to hash password here
    $this->dbAccConn->updatePassword($this->testEmail01, $this->testPassword01);
    $this->dbAccConn->setRole($this->testEmail01,'student');
    // create a AddOfferConfirm instance
    $this->addOfferConfirm = new AddOfferConfirm();
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToFormIfFieldsAreEmpty() {
    $_POST = [
      'title' => null,
      'price' => null,
      'end_date' => null,
      'description' => null,
      'tag' => null
    ];

    // For some reason, php don't like when i use ob_end_clean().
    ob_start();
    $this->addOfferConfirm->control();
    $headers = xdebug_get_headers();

    $this->assertTrue(str_contains(ob_get_clean(), "Veuillez remplir tous les champs"));
    $this->assertContains('Location: /offre', $headers);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToFormIfPriceIsInvalid() {
    $_POST = [
      'title' => 'Test Offer',
      'price' => '-10',
      'end_date' => '2023-12-31',
      'description' => 'Test Description',
      'tag' => 'Test'
    ];

    //  the buffer to avoid output interference, caused by the two echo call in control()
    ob_start();
    $this->addOfferConfirm->control();
    ob_end_clean();

    $headers = xdebug_get_headers();
    $this->assertContains('Location: /offre', $headers);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToMarketplaceOnValidInput() {
    $_SESSION['email'] = $this->testEmail01;
    $_POST = [
      'title' => 'UNIT_TEST_OFFER',
      'price' => '100',
      'end_date' => '2077-12-31',
      'description' => 'UNIT_TEST_DESC',
    ];


//    AccountDB::setInstance($mockDb);

    ob_start();
    $this->addOfferConfirm->control();
    ob_end_clean();

    $headers = xdebug_get_headers();
    $this->assertContains('Location: /marketplace', $headers);
  }

  public function testResolvesCorrectPathAndMethod()
  {
    $this->assertTrue(AddOfferConfirm::resolve('/offre/confirm', 'POST'));
  }

  public function testDoesNotResolveIncorrectPath()
  {
    $this->assertFalse(AddOfferConfirm::resolve('/wrong-path', 'POST'));
  }

  public function testDoesNotResolveIncorrectMethod()
  {
    $this->assertFalse(AddOfferConfirm::resolve('/offre/confirm', 'GET'));
  }

  public function tearDown(): void{
/*    // get the ouid of the offers of the test user
    $offer = $this->dbAccConn->executeQuery(
      'SELECT ouid FROM offer WHERE owner =\'test@testUser01.com\';'
    );
    $this->testOuid01 = $offer[0]['ouid'];*/
    $this->dbTradeConn->deleteOffer($this->testOuid01);
    $this->dbAccConn->deleteUser($this->testEmail01);
  }

}
