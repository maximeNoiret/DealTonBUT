<?php

use PHPUnit\Framework\TestCase;
use views\Trade\MarketPlace\MarketPlaceView;
use controllers\Trade\MarketPlace\MarketPlace;

class MarketPlaceViewTest extends TestCase {

  public function returnsCorrectPath() {
    $view = new MarketPlaceView();
    $expectedPath = __DIR__ . DIRECTORY_SEPARATOR . 'MarketPlace.html';
    $this->assertEquals($expectedPath, $view->path());
  }

  public function returnsTemplateValuesWithOffers() {
    $mockOffers = ['offer1', 'offer2'];
    $this->mockStaticMethod(MarketPlace::class, 'getOffers', $mockOffers);

    $view = new MarketPlaceView();
    $values = $view->templateValues();

    $this->assertArrayHasKey('OFFERS', $values);
    $this->assertEquals($mockOffers, $values['OFFERS']);
  }

  public function returnsCorrectNavbarText() {
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