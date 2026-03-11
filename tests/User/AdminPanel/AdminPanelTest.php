<?php

namespace User\AdminPanel;

use controllers\User\AdminPanel\AdminPanel;
use PHPUnit\Framework\TestCase;

class AdminPanelTest extends TestCase
{

  private AdminPanel $adminPanel;

  public function before(): void
  {
    $this->adminPanel = new AdminPanel();
  }

  public function testGeneAdminDeleteOffer()
  {

  }

  public function testGetAllAccountHtml()
  {

  }

  public function testResolveSuccess()
  {
    $this->assertTrue(AdminPanel::resolve('/admin', 'GET'));
  }

  public function testResolveFail()
  {
    $this->assertFalse(AdminPanel::resolve('/admin', 'METH'));
    $this->assertFalse(AdminPanel::resolve('/FailPath', 'POST'));
  }

  public function testControl()
  {

  }

  public function testGenAdminOffersHtml()
  {

  }
}
