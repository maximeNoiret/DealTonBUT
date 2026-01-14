<?php

namespace tests\dtu\controllers\Legal\TermsOfUse;

use controllers\Legal\TermsOfUse\TermsOfUse;
use views\Legal\TermsOfUse\TermsOfUseView;
use PHPUnit\Framework\TestCase;


class TermsOfUseTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testResolvesCorrectPathAndMethod()
    {
        $this->assertTrue(TermsOfUse::resolve('/termsofuse', 'GET'));
    }

    public function testResolvesReturnsFalseForIncorrectPath()
    {
        $this->assertFalse(TermsOfUse::resolve('/different', 'GET'));
    }

    public function testResolvesReturnsFalseForIncorrectMethod()
    {
        $this->assertFalse(TermsOfUse::resolve('/termsofuse', 'POST'));
    }

    public function testResolvesReturnsFalseForBothIncorrectPathAndMethod()
    {
        $this->assertFalse(TermsOfUse::resolve('/different', 'POST'));
    }

  /**
   * @runInSeparateProcess
   */
    public function testRedirectsToLoginWhenNotLoggedIn()
    {
        $_SESSION['logged-in'] = false;

        $controller = new TermsOfUse();

        ob_start();
        //$this->expectOutputString('');
        $controller->control();
        $headers =xdebug_get_headers();
        ob_end_clean();
        $this->assertContains('Location: /user/login', $headers);
       // $this->assertArrayHasKey('Location', $this->getHeadersArray());
    }

  /**
   * @runInSeparateProcess
   */
    public function testRedirectsToLoginWhenSessionNotSet()
    {
        $controller = new TermsOfUse();

        ob_start();
        //$this->expectOutputString('');
        $controller->control();
        $headers = xdebug_get_headers();
        ob_end_clean();
        $this->assertContains('Location: /user/login', $headers);

/*        $this->assertArrayHasKey('Location', $this->getHeadersArray());*/
    }

    public function testRendersViewWhenUserIsLoggedIn()
    {
        $_SESSION['logged-in'] = true;

        $controller = new TermsOfUse();
        ob_start();
        $controller->control();
        $output = ob_get_clean();
        $this->assertStringContainsString('<title>Condition d\'utilisation - DealTonBUT</title>', $output);
    }

    public function testUsesCorrectStylesheets()
    {
        $expectedStylesheets = [
            '/_assets/styles/style.css',
            '/_assets/styles/navbar.css',
            '/_assets/styles/loginSingnin.css'
        ];

        $this->assertEquals($expectedStylesheets, TermsOfUse::STYLESHEET);
    }

    public function testPathConstantIsCorrect()
    {
        $this->assertEquals('/termsofuse', TermsOfUse::PATH);
    }

    public function testMethodConstantIsCorrect()
    {
        $this->assertEquals('GET', TermsOfUse::METH);
    }

    private function getHeadersArray(): array
    {
        $headers = [];
        foreach (headers_list() as $header) {
            $parts = explode(':', $header, 2);
            if (count($parts) === 2) {
                $headers[trim($parts[0])] = trim($parts[1]);
            }
        }
        return $headers;
    }
}

