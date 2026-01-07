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

    public function resolvesCorrectPathAndMethod()
    {
        $this->assertTrue(TermsOfUse::resolve('/termsofuse', 'GET'));
    }

    public function resolvesReturnsFalseForIncorrectPath()
    {
        $this->assertFalse(TermsOfUse::resolve('/different', 'GET'));
    }

    public function resolvesReturnsFalseForIncorrectMethod()
    {
        $this->assertFalse(TermsOfUse::resolve('/termsofuse', 'POST'));
    }

    public function resolvesReturnsFalseForBothIncorrectPathAndMethod()
    {
        $this->assertFalse(TermsOfUse::resolve('/different', 'POST'));
    }

    public function redirectsToLoginWhenNotLoggedIn()
    {
        $_SESSION['logged-in'] = false;

        $controller = new TermsOfUse();

        ob_start();
        $this->expectOutputString('');
        $controller->control();
        ob_end_clean();

        $this->assertArrayHasKey('Location', $this->getHeadersArray());
    }

    public function redirectsToLoginWhenSessionNotSet()
    {
        $controller = new TermsOfUse();

        ob_start();
        $this->expectOutputString('');
        $controller->control();
        ob_end_clean();

        $this->assertArrayHasKey('Location', $this->getHeadersArray());
    }

    public function rendersViewWhenUserIsLoggedIn()
    {
        $_SESSION['logged-in'] = true;

        $mockView = $this->createMock(TermsOfUseView::class);
        $mockView->method('render')->willReturn('<html>Terms of Use</html>');

        $controller = new TermsOfUse();

        ob_start();
        $this->expectOutputString('<html>Terms of Use</html>');
        $controller->control();
        ob_end_clean();
    }

    public function usesCorrectStylesheets()
    {
        $expectedStylesheets = [
            '/_assets/styles/style.css',
            '/_assets/styles/navbar.css',
            '/_assets/styles/loginSingnin.css'
        ];

        $this->assertEquals($expectedStylesheets, TermsOfUse::STYLESHEET);
    }

    public function pathConstantIsCorrect()
    {
        $this->assertEquals('/termsofuse', TermsOfUse::PATH);
    }

    public function methodConstantIsCorrect()
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

