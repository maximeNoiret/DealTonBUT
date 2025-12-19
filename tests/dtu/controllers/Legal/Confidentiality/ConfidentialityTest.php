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

  public function controlRedirectsToLoginWhenUserNotLoggedIn(): void
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

  public function controlRedirectsToLoginWhenLoggedInIsFalse(): void
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

  public function controlRendersConfidentialityViewWhenUserIsLoggedIn(): void
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

  public function resolveReturnsTrueForMatchingPathAndMethod(): void
  {
    $this->assertTrue(Confidentiality::resolve('/confidentiality', 'GET'));
  }

  public function resolveReturnsFalseForNonMatchingPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('/privacy', 'GET'));
  }

  public function resolveReturnsFalseForNonMatchingMethod(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidentiality', 'POST'));
  }

  public function resolveReturnsFalseForDifferentHttpMethod(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidentiality', 'PUT'));
  }

  public function resolveReturnsFalseForDeleteMethod(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidentiality', 'DELETE'));
  }

  public function resolveReturnsFalseForEmptyPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('', 'GET'));
  }

  public function resolveReturnsFalseForRootPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('/', 'GET'));
  }

  public function resolveReturnsFalseForSimilarPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidentiality/other', 'GET'));
  }

  public function resolveReturnsFalseForPartialPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidential', 'GET'));
  }

  public function resolveIsCaseSensitiveForPath(): void
  {
    $this->assertFalse(Confidentiality::resolve('/Confidentiality', 'GET'));
  }

  public function resolveIsCaseSensitiveForMethod(): void
  {
    $this->assertFalse(Confidentiality::resolve('/confidentiality', 'get'));
  }
}

