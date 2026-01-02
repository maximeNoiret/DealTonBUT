<?php

namespace Trade\DeleteOffer;

use controllers\Trade\DeleteOffer\DeleteOffer;
use PHPUnit\Framework\TestCase;
use models\DataBase;

/**
 * @runTestsInSeparateProcesses
 */
class DeleteOfferTest extends TestCase
{

  private DeleteOffer $deleteOffer;
  private DataBase $dbConn;
  /*
 USER :
    Email: test@testUser01.com
    Username: testUser01
    password: password
 */
  private string $testEmail01='test@testUser01.com';
  private string $testUsername01='testUser01';
  private string $testPassword01='password';
  private int $testOuid01=12;

  public function setUp(): void{
    // create a DeleteOffer instance
    $this->deleteOffer = new DeleteOffer();
    // create a mock for DataBase.php
    // TODO: figure how to mock database
    /*$mockDb = $this->getMockBuilder(DataBase::class)
          ->disableOriginalConstructor()
          ->onlyMethods(['deleteOffer'])
          ->getMock();

        $mockDb->expects($this->once())
          ->method('deleteOffer')
          ->with(
            $this->equalTo(12)
          );*/
    $this->dbConn = DataBase::getInstance();

    // create a test account, that will have to be deleted in teardown()
    $this->dbConn->registerAccount($this->testUsername01,$this->testEmail01);
    // no need to hash password here
    $this->dbConn->updatePassword($this->testEmail01, $this->testPassword01);
    $this->dbConn->setRole($this->testEmail01,'student');

    // OFFER
    //  param of the offer :
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
    // delete the test offer and user from database
    $this->dbConn->deleteOffer($this->testOuid01);
    $this->dbConn->deleteUser($this->testEmail01);
  }
}
