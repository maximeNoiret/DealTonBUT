<?php

namespace User\AdminPanel;

use controllers\User\AdminPanel\AdminPanel;
use models\AccountDB;
use PHPUnit\Framework\TestCase;

class AdminPanelTest extends TestCase
{

  private AdminPanel $adminPanel;

  public function before(): void
  {
    $this->adminPanel = new AdminPanel();
  }

  /* ----------------------------------------------------------------------- */
  /* Resolve */
  /* ----------------------------------------------------------------------- */
  public function testResolveSuccess()
  {
    $this->assertTrue(AdminPanel::resolve('/admin', 'GET'));
  }

  public function testResolveFail()
  {
    $this->assertFalse(AdminPanel::resolve('/admin', 'METH'));
    $this->assertFalse(AdminPanel::resolve('/FailPath', 'POST'));
  }

  /* ----------------------------------------------------------------------- */
  /* genAdminDeleteOffer */
  /* ----------------------------------------------------------------------- */

  public function testGenAdminDeleteOfferContainsOfferId(): void
  {
    $adminPanel = new AdminPanel();
    $html = $adminPanel->genAdminDeleteOffer(42);

    $this->assertStringContainsString('42', $html);
  }

  public function testGenAdminDeleteOfferContainsDeleteEndpoint(): void
  {
    $adminPanel = new AdminPanel();
    $html = $adminPanel->genAdminDeleteOffer(1);

    $this->assertStringContainsString('/offre/delete', $html);
  }

  public function testGenAdminDeleteOffer_returnsAnchorTag(): void
  {
    $adminPanel = new AdminPanel();
    $html = $adminPanel->genAdminDeleteOffer(5);

    $this->assertStringStartsWith('<a', $html);
    $this->assertStringContainsString('</a>', $html);
  }

  public function testGenAdminDeleteOfferContainsDeleteClass(): void
  {
    $adminPanel = new AdminPanel();
    $html = $adminPanel->genAdminDeleteOffer(99);

    $this->assertStringContainsString('button-delete', $html);
  }

  public function testGenAdminDeleteOfferHrefContainsIdQueryParam(): void
  {
    $adminPanel = new AdminPanel();
    $html = $adminPanel->genAdminDeleteOffer(7);

    $this->assertMatchesRegularExpression('/href="[^"]*\?id=7"/', $html);
  }

  /* ----------------------------------------------------------------------- */
  /* getAllAccountHtml */
  /* ----------------------------------------------------------------------- */

  /**
   *@test
   *@covers AdminPanel::getAllAccountHtml
   */
  public function getAllAccountHtml_returnsEmptyStringWhenNoAccounts(): void
  {
    // Mock AccountDB singleton to return an empty array
    $mockAccountDB = $this->createMock(AccountDB::class);
    $mockAccountDB->method('getAllAccount')->willReturn([]);

    $this->mockAccountDBInstance($mockAccountDB);

    $adminPanel = new AdminPanel();
    $result = $adminPanel->getAllAccountHtml();

    $this->assertSame('', $result);
  }

  /**
   * @test
   * @covers AdminPanel::getAllAccountHtml
   */
  public function getAllAccountHtml_returnsHtmlForEachAccount(): void
  {
    $accounts = [
      ['email' => 'alice@example.com', 'role' => 'student', 'balance' => '100'],
      ['email' => 'bob@example.com',   'role' => 'teacher', 'balance' => '200'],
    ];

    $mockAccountDB = $this->createMock(AccountDB::class);
    $mockAccountDB->method('getAllAccount')->willReturn($accounts);

    $this->mockAccountDBInstance($mockAccountDB);

    $adminPanel = new AdminPanel();
    $result = $adminPanel->getAllAccountHtml();

    // Each account should produce some HTML containing its email
    $this->assertStringContainsString('alice@example.com', $result);
    $this->assertStringContainsString('bob@example.com', $result);
  }

  /**
   * @test
   * @covers AdminPanel::getAllAccountHtml
   */
  public function getAllAccountHtml_containsRoleInformation(): void
  {
    $accounts = [
      ['email' => 'charlie@example.com', 'role' => 'admin', 'balance' => '500'],
    ];

    $mockAccountDB = $this->createMock(AccountDB::class);
    $mockAccountDB->method('getAllAccount')->willReturn($accounts);

    $this->mockAccountDBInstance($mockAccountDB);

    $adminPanel = new AdminPanel();
    $result = $adminPanel->getAllAccountHtml();

    $this->assertStringContainsString('admin', $result);
  }

  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  /**
   * Replace the AccountDB singleton with the given mock for the duration of
   * the current test.
   */
  private function mockAccountDBInstance(AccountDB $mock): void
  {
    $reflection = new \ReflectionClass(AccountDB::class);
    $instanceProp = $reflection->getProperty('instance');
    $instanceProp->setAccessible(true);
    $instanceProp->setValue(null, $mock);

    // Restore after the test
    $this->addTeardownCallback(function () use ($instanceProp) {
      $instanceProp->setValue(null, null);
    });
  }

  /**
   * Helper to register teardown callbacks without needing setUp/tearDown.
   */
  private function addTeardownCallback(callable $cb): void
  {
    register_shutdown_function($cb);
  }
}