<?php

use PHPUnit\Framework\TestCase;
use views\Trade\MarketPlace\MarketPlaceView;
use controllers\Trade\MarketPlace\MarketPlace;

class SeeOfferTest extends TestCase {

  public function testReturnsCorrectPath() {
    $view = new MarketPlaceView();
    $expectedPath = __DIR__ . DIRECTORY_SEPARATOR . 'MarketPlace.html';
    $this->assertEquals($expectedPath, $view->path());
  }

  public function TestReturnsTemplateValuesWithOffers() {
    $mockOffers = ['offer1', 'offer2'];
    $this->mockStaticMethod(MarketPlace::class, 'getOffers', $mockOffers);

    $view = new MarketPlaceView();
    $values = $view->templateValues();

    $this->assertArrayHasKey('OFFERS', $values);
    $this->assertEquals($mockOffers, $values['OFFERS']);
  }

  public function testReturnsCorrectNavbarText() {
    $view = new MarketPlaceView();
    $this->assertEquals('Place De Marché', $view->navbarText());
  }

  private function mockStaticMethod($class, $method, $returnValue) {
    $mock = $this->getMockBuilder($class)
      ->disableOriginalConstructor()
      ->onlyMethods([$method])
      ->getMock();

    $mock->expects($this->once())
      ->method($method)
      ->willReturn($returnValue);

    return $mock;
  }
}