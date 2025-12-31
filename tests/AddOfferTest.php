<?php

use PHPUnit\Framework\TestCase;
use controllers\Trade\AddOffer\AddOffer;
use views\Trade\AddOffer\AddOfferView;

class AddOfferTest extends TestCase
{
  private AddOffer $addOfferController;

  public function setUp(): void{
    // create a AddOffer instance
    $this->addOfferController = new AddOffer();
  }

  // It seems that headers cannot be tested in the same process.
  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToLoginIfUserNotLoggedIn()
  {
    $url = '/user/login';
    $_SESSION = [];
    $this->addOfferController->control();

    $headers = xdebug_get_headers();
    if (sizeof($headers) > 0) {
      $this->assertContains('Location: ' . $url, $headers);
    } else {
      $this->fail('No headers were sent.');
    }
  }

  public function testRendersViewIfUserLoggedIn()
  {
    $_SESSION['logged-in'] = true;
    $expectedOutput = (new AddOfferView(''))->render("AddOffer - DealTonBUT", [
      '/_assets/styles/addOffer.css',
      '/_assets/styles/style.css',
      '/_assets/styles/navbar.css'
    ]);
    $this->addOfferController->control();

    $this->expectOutputString($expectedOutput);
  }

  public function testResolvesCorrectPathAndMethod()
  {
    $this->assertTrue(AddOffer::resolve('/offre', 'GET'));
  }

  public function testDoesNotResolveIncorrectPath()
  {
    $this->assertFalse(AddOffer::resolve('/wrong-path', 'GET'));
  }

  public function testDoesNotResolveIncorrectMethod()
  {
    $this->assertFalse(AddOffer::resolve('/offre', 'POST'));
  }
}
