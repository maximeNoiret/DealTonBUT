<?php

namespace Trade\SeeOffer;

use controllers\Trade\SeeOffer\SeeOffer;
use models\DataBase;
use PHPUnit\Framework\TestCase;

class SeeOfferTest extends TestCase
{

  private SeeOffer $seeOffer;
  private DataBase $dbConn;
  private string $testEmail01='test@testUser01.com';
  private string $testUsername01='testUser01';
  private string $testPassword01='password';
  private int $testOuid01=0;
  private array $offerDetails = [];
  public function setUp(): void
  {
    $seeOffer = new SeeOffer();
    // set up for database related unit tests
    $this->dbConn = DataBase::getInstance();

    // create a test account, that will have to be deleted in teardown()
    $this->dbConn->registerAccount($this->testUsername01,$this->testEmail01);
    // no need to hash password here
    $this->dbConn->updatePassword($this->testEmail01, $this->testPassword01);
    $this->dbConn->setRole($this->testEmail01,'student');

    //param of the offer :
    $_SESSION['email'] = $this->testEmail01;
    $title = 'UNIT_TEST_OFFER';
    $price = '100';
    $end_date = '2077-12-31';
    $description = 'UNIT_TEST_DESC';
    $this->dbConn->insertOffre(
      $_SESSION['email'],
      $title,
      (float)$price,
      $description,
      $end_date
    );

    // get the ouid of the offers of the test user
    $offer = $this->dbConn->executeQuery(
      'SELECT ouid FROM offer WHERE owner =\'test@testUser01.com\';'
    );
    $this->testOuid01 = $offer[0]['ouid'];
    $_GET['id'] = $this->testOuid01;
    $this->offerDetails = $this->dbConn->getOffer($_GET['id']);
  }
  public function testButtonOffer()
  {

  }

  public function test__construct()
  {

  }

  public function testControl()
  {

  }

  public function testOwnerOfOfferReturnFalse()
  {
    $_SESSION['email'] = '';
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
}
