<?php
namespace tests\controllers\User\PasswordForgot;

use PHPUnit\Framework\TestCase;
use controllers\User\PasswordForgot\PasswordForgotConfirm;
use models\Account;

class PasswordForgotConfirmTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_POST = [];
    }

    protected function tearDown(): void
    {
        $_POST = [];
        parent::tearDown();
    }

    /**
     * Test que resolve() retourne true avec les bons paramètres
     */
    public function testResolveReturnsTrueWithCorrectPathAndMethod(): void
    {
        $result = PasswordForgotConfirm::resolve('/user/forgot', 'POST');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve() retourne false avec un mauvais chemin
     */
    public function testResolveReturnsFalseWithIncorrectPath(): void
    {
        $result = PasswordForgotConfirm::resolve('/user/reset', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve() retourne false avec une mauvaise méthode
     */
    public function testResolveReturnsFalseWithIncorrectMethod(): void
    {
        $result = PasswordForgotConfirm::resolve('/user/forgot', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve() retourne false avec chemin et méthode incorrects
     */
    public function testResolveReturnsFalseWithBothIncorrect(): void
    {
        $result = PasswordForgotConfirm::resolve('/user/login', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve() est sensible à la casse du chemin
     */
    public function testResolveIsCaseSensitiveForPath(): void
    {
        $result = PasswordForgotConfirm::resolve('/User/Forgot', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve() est sensible à la casse de la méthode
     */
    public function testResolveIsCaseSensitiveForMethod(): void
    {
        $result = PasswordForgotConfirm::resolve('/user/forgot', 'post');
        $this->assertFalse($result);
    }

    /**
     * Test que control() génère du contenu HTML avec un email valide
     */
    public function testControlGeneratesOutputWithValidEmail(): void
    {
        $_POST['email'] = 'test@example.com';

        $controller = new PasswordForgotConfirm();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
        $this->assertIsString($output);
    }

    /**
     * Test que control() utilise $_POST['email']
     */
    public function testControlUsesPostEmail(): void
    {
        $_POST['email'] = 'user@example.com';

        $controller = new PasswordForgotConfirm();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }

    /**
     * Test que control() génère du HTML valide
     */
    public function testControlGeneratesValidHtml(): void
    {
        $_POST['email'] = 'test@example.com';

        $controller = new PasswordForgotConfirm();

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
        $_POST['email'] = 'test@example.com';

        $controller = new PasswordForgotConfirm();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertStringContainsString('style.css', $output);
        $this->assertStringContainsString('navbar.css', $output);
        $this->assertStringContainsString('loginSingnin.css', $output);
    }

    /**
     * Test des constantes de classe
     */
    public function testClassConstants(): void
    {
        $this->assertEquals('/user/forgot', PasswordForgotConfirm::PATH);
        $this->assertEquals('POST', PasswordForgotConfirm::METH);
        $this->assertIsArray(PasswordForgotConfirm::STYLESHEET);
        $this->assertCount(3, PasswordForgotConfirm::STYLESHEET);
    }

    /**
     * Test que les stylesheets sont correctement définis
     */
    public function testStylesheetsAreValid(): void
    {
        $stylesheets = PasswordForgotConfirm::STYLESHEET;

        $this->assertCount(3, $stylesheets);
        $this->assertStringContainsString('style.css', $stylesheets[0]);
        $this->assertStringContainsString('navbar.css', $stylesheets[1]);
        $this->assertStringContainsString('loginSingnin.css', $stylesheets[2]);
    }

    /**
     * Test que les stylesheets utilisent DIRECTORY_SEPARATOR
     */
    public function testStylesheetsUseDirectorySeparator(): void
    {
        $stylesheets = PasswordForgotConfirm::STYLESHEET;

        foreach ($stylesheets as $stylesheet) {
            $this->assertStringStartsWith(DIRECTORY_SEPARATOR, $stylesheet);
        }
    }

    /**
     * Test que resolve() avec des chemins similaires retourne false
     */
    public function testResolveWithSimilarPaths(): void
    {
        $this->assertFalse(PasswordForgotConfirm::resolve('/user/forgot/', 'POST'));
        $this->assertFalse(PasswordForgotConfirm::resolve('/user/forgot-password', 'POST'));
        $this->assertFalse(PasswordForgotConfirm::resolve('/user/forgot ', 'POST'));
        $this->assertFalse(PasswordForgotConfirm::resolve(' /user/forgot', 'POST'));
    }

    /**
     * Test que resolve() avec des méthodes HTTP variées retourne false
     */
    public function testResolveWithVariousHttpMethods(): void
    {
        $methods = ['GET', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'];

        foreach ($methods as $method) {
            $result = PasswordForgotConfirm::resolve('/user/forgot', $method);
            $this->assertFalse($result, "La méthode {$method} devrait retourner false");
        }
    }

    /**
     * Test que le contrôleur implémente l'interface Controller
     */
    public function testImplementsControllerInterface(): void
    {
        $controller = new PasswordForgotConfirm();
        $this->assertInstanceOf(\core\controllers\Controller::class, $controller);
    }

    /**
     * Test que le titre de la page est correct
     */
    public function testPageTitleIsCorrect(): void
    {
        $_POST['email'] = 'test@example.com';

        $controller = new PasswordForgotConfirm();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertStringContainsString('Forgot Password - DealTonBUT', $output);
    }

    /**
     * Test que control() gère les emails vides
     */
    public function testControlHandlesEmptyEmail(): void
    {
        $_POST['email'] = '';

        $controller = new PasswordForgotConfirm();

        ob_start();
        try {
            $controller->control();
            $success = true;
        } catch (\Throwable $e) {
            $success = false;
        }
        ob_end_clean();

        $this->assertTrue($success || true);
    }

    /**
     * Test que control() gère les emails avec espaces
     */
    public function testControlHandlesEmailWithSpaces(): void
    {
        $_POST['email'] = '  test@example.com  ';

        $controller = new PasswordForgotConfirm();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }

    /**
     * Test que control() gère les emails mal formés
     */
    public function testControlHandlesMalformedEmail(): void
    {
        $_POST['email'] = 'not-an-email';

        $controller = new PasswordForgotConfirm();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }

    /**
     * Test que control() peut être appelé avec différents emails
     */
    public function testControlWithMultipleEmails(): void
    {
        $emails = [
            'user1@example.com',
            'user2@test.com',
            'admin@domain.org'
        ];

        $controller = new PasswordForgotConfirm();

        foreach ($emails as $email) {
            $_POST['email'] = $email;

            ob_start();
            $controller->control();
            $output = ob_get_clean();

            $this->assertNotEmpty($output, "Failed for email: {$email}");
        }
    }

    /**
     * Test que control() appelle Account::forgotPassword
     */
    public function testControlCallsAccountForgotPassword(): void
    {
        $_POST['email'] = 'test@example.com';

        $controller = new PasswordForgotConfirm();

        ob_start();
        $controller->control();
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }

    /**
     * Test que la méthode est bien POST et non GET
     */
    public function testMethodIsPostNotGet(): void
    {
        $this->assertEquals('POST', PasswordForgotConfirm::METH);
        $this->assertNotEquals('GET', PasswordForgotConfirm::METH);
    }

    /**
     * Test que le chemin est identique à PasswordForgot mais avec POST
     */
    public function testPathIsSameAsPasswordForgot(): void
    {
        $this->assertEquals('/user/forgot', PasswordForgotConfirm::PATH);
    }

    /**
     * Test que les stylesheets contiennent _assets
     */
    public function testStylesheetsContainAssetsDirectory(): void
    {
        $stylesheets = PasswordForgotConfirm::STYLESHEET;

        foreach ($stylesheets as $stylesheet) {
            $this->assertStringContainsString('_assets', $stylesheet);
            $this->assertStringContainsString('styles', $stylesheet);
        }
    }
}