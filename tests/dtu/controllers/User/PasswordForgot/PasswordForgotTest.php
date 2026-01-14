<?php
namespace tests\controllers\User\PasswordForgot;

use PHPUnit\Framework\TestCase;
use controllers\User\PasswordForgot\PasswordForgot;
use views\User\ForgotPassword\ForgotPasswordView;

class PasswordForgotTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test que resolve() retourne true avec les bons paramètres
     */
    public function testResolveReturnsTrueWithCorrectPathAndMethod(): void
    {
        $result = PasswordForgot::resolve('/user/forgot', 'GET');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve() retourne false avec un mauvais chemin
     */
    public function testResolveReturnsFalseWithIncorrectPath(): void
    {
        $result = PasswordForgot::resolve('/user/login', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve() retourne false avec une mauvaise méthode
     */
    public function testResolveReturnsFalseWithIncorrectMethod(): void
    {
        $result = PasswordForgot::resolve('/user/forgot', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve() retourne false avec chemin et méthode incorrects
     */
    public function testResolveReturnsFalseWithBothIncorrect(): void
    {
        $result = PasswordForgot::resolve('/user/reset', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve() est sensible à la casse du chemin
     */
    public function testResolveIsCaseSensitiveForPath(): void
    {
        $result = PasswordForgot::resolve('/User/Forgot', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve() est sensible à la casse de la méthode
     */
    public function testResolveIsCaseSensitiveForMethod(): void
    {
        $result = PasswordForgot::resolve('/user/forgot', 'get');
        $this->assertFalse($result);
    }

    /**
     * Test que control() génère du contenu HTML
     */
    public function testControlGeneratesOutput(): void
    {
        $controller = new PasswordForgot();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
        $this->assertIsString($output);
    }

    /**
     * Test que control() utilise ForgotPasswordView
     */
    public function testControlUsesForgotPasswordView(): void
    {
        $controller = new PasswordForgot();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertStringContainsString('Forgot Password - DealTonBUT', $output);
    }

    /**
     * Test que control() génère du HTML valide
     */
    public function testControlGeneratesValidHtml(): void
    {
        $controller = new PasswordForgot();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertStringContainsString('<!DOCTYPE html>', $output);
        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('</html>', $output);
    }

    /**
     * Test que control() inclut les stylesheets
     */
    public function testControlIncludesStylesheets(): void
    {
        $controller = new PasswordForgot();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertStringContainsString('loginSingnin.css', $output);
        $this->assertStringContainsString('style.css', $output);
        $this->assertStringContainsString('navbar.css', $output);
    }

    /**
     * Test des constantes de classe
     */
    public function testClassConstants(): void
    {
        $this->assertEquals('/user/forgot', PasswordForgot::PATH);
        $this->assertEquals('GET', PasswordForgot::METH);
        $this->assertIsArray(PasswordForgot::STYLESHEET);
        $this->assertCount(3, PasswordForgot::STYLESHEET);
    }

    /**
     * Test que les stylesheets sont correctement définis
     */
    public function testStylesheetsAreValid(): void
    {
        $stylesheets = PasswordForgot::STYLESHEET;

        $this->assertCount(3, $stylesheets);
        $this->assertContains('/_assets/styles/loginSingnin.css', $stylesheets);
        $this->assertContains('/_assets/styles/style.css', $stylesheets);
        $this->assertContains('/_assets/styles/navbar.css', $stylesheets);
    }

    /**
     * Test que les stylesheets sont dans le bon ordre
     */
    public function testStylesheetsOrder(): void
    {
        $stylesheets = PasswordForgot::STYLESHEET;

        $this->assertEquals('/_assets/styles/loginSingnin.css', $stylesheets[0]);
        $this->assertEquals('/_assets/styles/style.css', $stylesheets[1]);
        $this->assertEquals('/_assets/styles/navbar.css', $stylesheets[2]);
    }

    /**
     * Test que control() peut être appelé plusieurs fois sans erreur
     */
    public function testControlIsIdempotent(): void
    {
        $controller = new PasswordForgot();

        ob_start();
        $controller->control();
        $output1 = ob_get_clean();

        ob_start();
        $controller->control();
        $output2 = ob_get_clean();

        $this->assertNotEmpty($output1);
        $this->assertNotEmpty($output2);
        $this->assertEquals($output1, $output2);
    }

    /**
     * Test que resolve() avec des chemins similaires retourne false
     */
    public function testResolveWithSimilarPaths(): void
    {
        $this->assertFalse(PasswordForgot::resolve('/user/forgot/', 'GET'));
        $this->assertFalse(PasswordForgot::resolve('/user/forgot-password', 'GET'));
        $this->assertFalse(PasswordForgot::resolve('/user/forgot ', 'GET'));
        $this->assertFalse(PasswordForgot::resolve(' /user/forgot', 'GET'));
    }

    /**
     * Test que resolve() avec des méthodes HTTP variées retourne false
     */
    public function testResolveWithVariousHttpMethods(): void
    {
        $methods = ['POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'];

        foreach ($methods as $method) {
            $result = PasswordForgot::resolve('/user/forgot', $method);
            $this->assertFalse($result, "La méthode {$method} devrait retourner false");
        }
    }

    /**
     * Test que le contrôleur implémente l'interface Controller
     */
    public function testImplementsControllerInterface(): void
    {
        $controller = new PasswordForgot();
        $this->assertInstanceOf(\core\controllers\Controller::class, $controller);
    }

    /**
     * Test que control() n'affiche pas d'erreurs PHP
     */
    public function testControlDoesNotGeneratePhpErrors(): void
    {
        $controller = new PasswordForgot();

        $errorReporting = error_reporting();
        error_reporting(E_ALL);

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        error_reporting($errorReporting);

        $this->assertNotEmpty($output);
    }

    /**
     * Test que le titre de la page est correct
     */
    public function testPageTitleIsCorrect(): void
    {
        $controller = new PasswordForgot();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertStringContainsString('<title>Forgot Password - DealTonBUT</title>', $output);
    }
}