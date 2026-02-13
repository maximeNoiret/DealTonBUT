<?php

namespace Forbidden;

use controllers\Forbidden\Env;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
  public Env $env;
  public function setUp(): void
  {
    $this->env = new Env();
  }

  public function testControlRendersForbiddenView()
  {
    ob_start();
    $this->env->control();

    // Get the output and clean the buffer
    $output = ob_get_clean();

    // Check if the output contains expected content from the Forbidden view
    $this->assertStringContainsString('<title>Forbidden - DealTonBUT</title>', $output);
  }

  public function testResolveCorrectPath()
  {
    $this->assertTrue(Env::resolve('/.env', 'GET'));
  }

  public function testDoesNotResolveIncorrectPath()
  {
    $this->assertFalse(Env::resolve('/wrongpath', 'GET'));
  }

  public function tearDown(): void
  {
    unset($this->env);
  }
}
