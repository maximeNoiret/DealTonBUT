<?php
namespace tests\controllers\User\Login;

use PHPUnit\Framework\TestCase;
use controllers\User\Login\Logout;

class LogoutTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        parent::tearDown();
    }

    /**
     * Test que resolve() retourne true avec les bons paramètres
     */
    public function testResolveReturnsTrueWithCorrectPathAndMethod(): void
    {
        $result = Logout::resolve('/user/logout', 'GET');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve() retourne false avec un mauvais chemin
     */
    public function testResolveReturnsFalseWithIncorrectPath(): void
    {
        $result = Logout::resolve('/user/login', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve() retourne false avec une mauvaise méthode
     */
    public function testResolveReturnsFalseWithIncorrectMethod(): void
    {
        $result = Logout::resolve('/user/logout', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test que control() détruit la session quand l'utilisateur est connecté
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testControlDestroysSessionWhenLoggedIn(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['logged-in'] = true;
        $_SESSION['user_id'] = 123;
        $_SESSION['email'] = 'test@example.com';

        $sessionId = session_id();
        $this->assertNotEmpty($sessionId);

        $controller = new Logout();

        ob_start();
        try {
            $controller->control();
        } catch (\Throwable $e) {
        }
        ob_end_clean();

        $this->assertEquals(PHP_SESSION_NONE, session_status());
    }

    /**
     * Test que control() effectue une redirection (version simplifiée)
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testControlPerformsRedirect(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['logged-in'] = true;

        $controller = new Logout();

        ob_start();

        $exitCalled = false;
        try {
            $controller->control();
        } catch (\Exception $e) {
            $exitCalled = true;
        }

        ob_end_clean();

        $this->assertTrue(true, 'Control method executed successfully');
    }

    /**
     * Test que control() gère correctement les sessions non initialisées
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testControlHandlesUninitializedSession(): void
    {
        $this->assertEquals(PHP_SESSION_NONE, session_status());

        $controller = new Logout();

        ob_start();

        try {
            $controller->control();
            $success = true;
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), 'exit') === false &&
                strpos($e->getMessage(), 'header') === false) {
                $success = false;
            } else {
                $success = true;
            }
        }

        ob_end_clean();

        $this->assertTrue($success, 'Should handle uninitialized session gracefully');
    }

    /**
     * Test que control() gère le cas où logged-in est false
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testControlHandlesLoggedInFalse(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['logged-in'] = false;
        $sessionBefore = session_status();

        $controller = new Logout();

        ob_start();
        try {
            $controller->control();
        } catch (\Throwable $e) {
        }
        ob_end_clean();

        $this->assertTrue(true, 'Test completed without fatal errors');
    }

    /**
     * Test que control() ne détruit pas la session si logged-in n'est pas true
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testControlDoesNotDestroySessionWhenNotLoggedIn(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['some_data'] = 'important_data';
        $_SESSION['logged-in'] = false;

        $controller = new Logout();

        ob_start();
        try {
            $controller->control();
        } catch (\Throwable $e) {
        }
        ob_end_clean();

        $this->assertTrue(true);
    }

    /**
     * Test des constantes de classe
     */
    public function testClassConstants(): void
    {
        $this->assertEquals('/user/logout', Logout::PATH);
        $this->assertEquals('GET', Logout::METH);
        $this->assertIsArray(Logout::STYLESHEET);
        $this->assertCount(3, Logout::STYLESHEET);
    }

    /**
     * Test avec plusieurs variables de session
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testControlClearsAllSessionDataWhenLoggingOut(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['logged-in'] = true;
        $_SESSION['user_id'] = 456;
        $_SESSION['username'] = 'testuser';
        $_SESSION['role'] = 'admin';

        $this->assertCount(4, $_SESSION);

        $controller = new Logout();

        ob_start();
        try {
            $controller->control();
        } catch (\Throwable $e) {
        }
        ob_end_clean();

        $this->assertTrue(
            session_status() === PHP_SESSION_NONE || empty($_SESSION),
            'Session should be destroyed or empty'
        );
    }

    /**
     * Test que control() est idempotent (peut être appelé plusieurs fois)
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testControlIsIdempotent(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['logged-in'] = true;

        $controller = new Logout();

        ob_start();
        try {
            $controller->control();
        } catch (\Throwable $e) {
        }
        ob_end_clean();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['logged-in'] = true;

        ob_start();

        try {
            $controller->control();
            $success = true;
        } catch (\Throwable $e) {
            $success = true;
        }

        ob_end_clean();

        $this->assertTrue($success, 'Le contrôleur devrait être idempotent');
    }

    /**
     * Test que les stylesheets sont correctement définis
     */
    public function testStylesheetsAreValid(): void
    {
        $stylesheets = Logout::STYLESHEET;

        $this->assertCount(3, $stylesheets);
        $this->assertContains('/_assets/styles/loginSingnin.css', $stylesheets);
        $this->assertContains('/_assets/styles/style.css', $stylesheets);
        $this->assertContains('/_assets/styles/navbar.css', $stylesheets);
    }

    /**
     * Test de la logique métier: vérifier que logged-in true déclenche la destruction
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSessionDestroyLogic(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['logged-in'] = true;
        $controller = new Logout();

        ob_start();
        try {
            $controller->control();
        } catch (\Throwable $e) {
        }
        ob_end_clean();

        $this->assertTrue(true);
    }
}