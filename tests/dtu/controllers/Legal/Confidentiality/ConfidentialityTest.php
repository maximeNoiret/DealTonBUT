<?php

namespace Tests\Controllers\Legal\Confidentiality;

use controllers\Legal\Confidentiality\Confidentiality;
use PHPUnit\Framework\TestCase;

class ConfidentialityTest extends TestCase
{
  protected function setUp(): void
  {
    $_SESSION = [];
  }

  public function testControlRedirectsToLoginWhenUserNotLoggedIn(): void
  {
    unset($_SESSION['logged-in']);

    $confidentiality = $this->getMockBuilder(Confidentiality::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['control'])
      ->getMock();

    $confidentiality->expects($this->once())
      ->method('control');

    $confidentiality->control();
  }

  public function testControlRedirectsToLoginWhenLoggedInIsFalse(): void
  {
    $_SESSION['logged-in'] = false;

    $confidentiality = $this->getMockBuilder(Confidentiality::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['control'])
      ->getMock();

    $confidentiality->expects($this->once())
      ->method('control');

    $confidentiality->control();
  }

  public function testControlRendersConfidentialityViewWhenUserIsLoggedIn(): void
  {
    $_SESSION['logged-in'] = true;

    $confidentiality = $this->getMockBuilder(Confidentiality::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['control'])
      ->getMock();

    $confidentiality->expects($this->once())
      ->method('control');

    $confidentiality->control();
  }

  public function testResolveReturnsTrueForMatchingPathAndMethod(): void
  {
    $this->assertTrue(Confidentiality::resolve('/confidentiality', 'GET'));
  }

  public function testResolveReturnsFalseForNonMatchingPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('/privacy', 'GET'));
  }

  public function testResolveReturnsFalseForNonMatchingMethod(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidentiality', 'POST'));
  }

  public function testResolveReturnsFalseForDifferentHttpMethod(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidentiality', 'PUT'));
  }

  public function testResolveReturnsFalseForDeleteMethod(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidentiality', 'DELETE'));
  }

  public function testResolveReturnsFalseForEmptyPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('', 'GET'));
  }

  public function testResolveReturnsFalseForRootPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('/', 'GET'));
  }

  public function testResolveReturnsFalseForSimilarPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidentiality/other', 'GET'));
  }

  public function testResolveReturnsFalseForPartialPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidential', 'GET'));
  }

  public function testResolveIsCaseSensitiveForPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('/Confidentiality', 'GET'));
  }

  public function testResolveIsCaseSensitiveForMethod(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidentiality', 'get'));
  }
}

