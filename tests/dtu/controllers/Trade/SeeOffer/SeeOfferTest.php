<?php

namespace Tests\Controllers\Trade\SeeOffer;

use controllers\Trade\SeeOffer\SeeOffer;
use PHPUnit\Framework\TestCase;

class SeeOfferTest extends TestCase
{
  protected function setUp(): void
  {
    $_SESSION = [];
    $_GET = [];
  }

  public function testIsOwnerOfOfferReturnsTrueIfUserIsOwner(): void
  {
    $_SESSION['email'] = 'owner@example.com';
    SeeOffer::$offer = ['owner' => 'owner@example.com', 'title' => 'Test Offer'];

    $seeOffer = $this->createPartialMock(SeeOffer::class, []);
    $this->assertTrue($seeOffer->isOwnerOfOffer());
  }

  public function testIsOwnerOfOfferReturnsFalseIfUserIsNotOwner(): void
  {
    $_SESSION['email'] = 'user@example.com';
    SeeOffer::$offer = ['owner' => 'owner@example.com', 'title' => 'Test Offer'];

    $seeOffer = $this->createPartialMock(SeeOffer::class, []);
    $this->assertFalse($seeOffer->isOwnerOfOffer());
  }

  public function testIsOwnerOfOfferReturnsFalseIfEmailNotInSession(): void
  {
    unset($_SESSION['email']);
    SeeOffer::$offer = ['owner' => 'owner@example.com', 'title' => 'Test Offer'];

    $seeOffer = $this->createPartialMock(SeeOffer::class, []);
    $this->assertFalse($seeOffer->isOwnerOfOffer());
  }

  public function testButtonOfferReturnsDeleteButtonForOwner(): void
  {
    $_SESSION['email'] = 'owner@example.com';
    SeeOffer::$offer = ['owner' => 'owner@example.com', 'title' => 'Test Offer'];
    SeeOffer::$id = 1;

    $seeOffer = $this->createPartialMock(SeeOffer::class, []);
    $this->assertStringContainsString('<a class="button-delete" href="/offre/delete?id=1">Delete</a>', $seeOffer->buttonOffer());
  }

  public function testButtonOfferReturnsBuyButtonForNonOwner(): void
  {
    $_SESSION['email'] = 'user@example.com';
    SeeOffer::$offer = ['owner' => 'owner@example.com', 'title' => 'Test Offer'];
    SeeOffer::$id = 1;

    $seeOffer = $this->createPartialMock(SeeOffer::class, []);
    $this->assertStringContainsString('<a class="button-buy" href="/offre/buy?id=1">Buy</a>', $seeOffer->buttonOffer());
  }

  public function testControlRedirectsToLoginIfUserNotLoggedIn(): void
  {
    $_SESSION['logged-in'] = false;

    $seeOffer = $this->getMockBuilder(SeeOffer::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['control'])
      ->getMock();

    $seeOffer->expects($this->once())
      ->method('control');

    $seeOffer->control();
  }

  public function testResolveReturnsTrueForMatchingPathAndMethod(): void
  {
    $this->assertTrue(SeeOffer::resolve('/offre/voir', 'GET'));
  }

  public function testResolveReturnsFalseForNonMatchingPath(): void
  {
    $this->assertFalse(SeeOffer::resolve('/offre/other', 'GET'));
  }

  public function testResolveReturnsFalseForNonMatchingMethod(): void
  {
    $this->assertFalse(SeeOffer::resolve('/offre/voir', 'POST'));
  }

  public function testResolveReturnsTrueIgnoresQueryParameters(): void
  {
    $this->assertTrue(SeeOffer::resolve('/offre/voir?id=123', 'GET'));
  }
}

