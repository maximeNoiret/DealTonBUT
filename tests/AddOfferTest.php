<?php

use PHPUnit\Framework\TestCase;
use controllers\Trade\AddOffer\AddOffer;
use views\Trade\AddOffer\AddOfferView;
use core\controllers\Controller;

class AddOfferTest extends TestCase
{

  private AddOffer $addOfferController;

  public function setUp(): void{
    $this->addOfferController = new AddOffer();
  }

  public function testRedirectsToLoginIfUserNotLoggedIn()
  {
    $_SESSION = [];
    $this->expectOutputString('');
    $this->expectExceptionMessage('Location: /user/login');

//    (new AddOffer())->control();
    $this->addOfferController->control();
  }

  public function testRendersViewIfUserLoggedIn()
  {
    $_SESSION['logged-in'] = true;
    $expectedOutput = (new AddOfferView(''))->render("AddOffer - DealTonBUT", [
      '/_assets/styles/addOffer.css',
      '/_assets/styles/style.css',
      '/_assets/styles/navbar.css'
    ]);

    $this->expectOutputString($expectedOutput);

    (new AddOffer())->control();
  }

  public function testResolvesCorrectPathAndMethod()
  {
    $this->assertTrue(AddOffer::resolve('/offre', 'GET'));
  }

  public function testDoesNotResolveIncorrectPath()
  {
    $this->assertFalse(AddOffer::resolve('/wrong-path', 'GET'));
  }

  public function doesNotResolveIncorrectMethod()
  {
    $this->assertFalse(AddOffer::resolve('/offre', 'POST'));
  }
}
