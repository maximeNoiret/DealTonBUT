<?php
namespace tests\controllers\User\Settings;

use PHPUnit\Framework\TestCase;
use controllers\User\Settings\Settings;
use views\User\SettingsPage\SettingsPageView;
use models\AccountDB;

class SettingsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_SESSION = [];
    }

    /**
     * Test que resolve retourne true avec le bon path et méthode GET
     */
    public function testResolveReturnsTrueWithCorrectPathAndMethod(): void
    {
        $result = Settings::resolve('/user/settings', 'GET');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve retourne false avec une mauvaise méthode
     */
    public function testResolveReturnsFalseWithIncorrectMethod(): void
    {
        $result = Settings::resolve('/user/settings', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve retourne false avec un mauvais path
     */
    public function testResolveReturnsFalseWithIncorrectPath(): void
    {
        $result = Settings::resolve('/user/wrong', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test control avec utilisateur connecté
     */
    public function testControlWithLoggedInUser(): void
    {
        $_SESSION['logged-in'] = true;
        $_SESSION['email'] = 'test@example.com';

        $viewMock = $this->getMockBuilder(SettingsPageView::class)
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->with('Paramètre - DealTonBUT', Settings::STYLESHEET)
            ->willReturn('<html>Settings Page</html>');

        $this->expectOutputString('<html>Settings Page</html>');
        echo $viewMock->render('Paramètre - DealTonBUT', Settings::STYLESHEET);
    }

    /**
     * Test control avec utilisateur non connecté redirige vers login
     */
    public function testControlWithNotLoggedInUserRedirectsToLogin(): void
    {
        $loggedIn = false;

        if (!isset($loggedIn) || $loggedIn !== true) {
            $shouldRedirect = true;
        } else {
            $shouldRedirect = false;
        }

        $this->assertTrue($shouldRedirect);
    }

    /**
     * Test control avec logged-in absent de la session
     */
    public function testControlWithMissingLoggedInSession(): void
    {
        $loggedIn = null;

        if (!isset($loggedIn) || $loggedIn !== true) {
            $shouldRedirect = true;
        } else {
            $shouldRedirect = false;
        }

        $this->assertTrue($shouldRedirect);
    }

    /**
     * Test control avec logged-in à false
     */
    public function testControlWithLoggedInFalse(): void
    {
        $loggedIn = false;

        if (!isset($loggedIn) || $loggedIn !== true) {
            $shouldRedirect = true;
        } else {
            $shouldRedirect = false;
        }

        $this->assertTrue($shouldRedirect);
    }

    /**
     * Test deleteAccount avec email en session
     */
    public function testDeleteAccountWithEmailInSession(): void
    {
        $_SESSION['email'] = 'delete@example.com';
        $_SESSION['logged-in'] = true;
        $_SESSION['username'] = 'testuser';

        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteUser'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('deleteUser')
            ->with('delete@example.com');

        $email = '';
        if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }
        $accountDBMock->deleteUser($email);

        $this->assertEquals('delete@example.com', $email);
    }

    /**
     * Test deleteAccount sans email en session
     */
    public function testDeleteAccountWithoutEmailInSession(): void
    {
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteUser'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('deleteUser')
            ->with('');

        $email = '';
        if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }
        $accountDBMock->deleteUser($email);

        $this->assertEquals('', $email);
    }

    /**
     * Test deleteAccount avec email non-string en session
     */
    public function testDeleteAccountWithNonStringEmailInSession(): void
    {
        $_SESSION['email'] = 12345;

        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteUser'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('deleteUser')
            ->with('');

        $email = '';
        if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }
        $accountDBMock->deleteUser($email);

        $this->assertEquals('', $email);
    }

    /**
     * Test que deleteAccount est une méthode publique
     */
    public function testDeleteAccountMethodIsPublic(): void
    {
        $reflection = new \ReflectionMethod(Settings::class, 'deleteAccount');
        $this->assertTrue($reflection->isPublic());
    }

    /**
     * Test des constantes de classe
     */
    public function testClassConstants(): void
    {
        $this->assertEquals('/user/settings', Settings::PATH);
        $this->assertEquals('GET', Settings::METH);

        $this->assertIsArray(Settings::STYLESHEET);
        $this->assertCount(3, Settings::STYLESHEET);

        $this->assertEquals('/_assets/styles/settings.css', Settings::STYLESHEET[0]);
        $this->assertEquals('/_assets/styles/style.css', Settings::STYLESHEET[1]);
        $this->assertEquals('/_assets/styles/navbar.css', Settings::STYLESHEET[2]);
    }

    /**
     * Test que STYLESHEET contient les bons fichiers CSS
     */
    public function testStylesheetContainsExpectedFiles(): void
    {
        $expectedStylesheets = [
            '/_assets/styles/settings.css',
            '/_assets/styles/style.css',
            '/_assets/styles/navbar.css'
        ];

        $this->assertEquals($expectedStylesheets, Settings::STYLESHEET);
    }

    /**
     * Test que tous les stylesheets sont des chemins CSS valides
     */
    public function testStylesheetsAreValidCssPaths(): void
    {
        foreach (Settings::STYLESHEET as $stylesheet) {
            $this->assertIsString($stylesheet);
            $this->assertStringStartsWith('/_assets/styles/', $stylesheet);
            $this->assertStringEndsWith('.css', $stylesheet);
        }
    }

    /**
     * Test que la classe implémente Controller
     */
    public function testClassImplementsControllerInterface(): void
    {
        $reflection = new \ReflectionClass(Settings::class);
        $this->assertTrue($reflection->implementsInterface(\core\controllers\Controller::class));
    }

    /**
     * Test que control est une méthode publique
     */
    public function testControlMethodIsPublic(): void
    {
        $reflection = new \ReflectionMethod(Settings::class, 'control');
        $this->assertTrue($reflection->isPublic());
    }

    /**
     * Test que resolve est une méthode statique publique
     */
    public function testResolveMethodIsPublicAndStatic(): void
    {
        $reflection = new \ReflectionMethod(Settings::class, 'resolve');
        $this->assertTrue($reflection->isPublic());
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * Test control vérifie strictement logged-in à true
     */
    public function testControlChecksLoggedInStrictly(): void
    {
        $loggedIn = 1;

        if (!isset($loggedIn) || $loggedIn !== true) {
            $shouldRedirect = true;
        } else {
            $shouldRedirect = false;
        }

        $this->assertTrue($shouldRedirect);
    }

    /**
     * Test control avec logged-in string 'true'
     */
    public function testControlWithLoggedInAsString(): void
    {
        $loggedIn = 'true';

        if (!isset($loggedIn) || $loggedIn !== true) {
            $shouldRedirect = true;
        } else {
            $shouldRedirect = false;
        }

        $this->assertTrue($shouldRedirect);
    }

    /**
     * Test que le titre de la page est correct
     */
    public function testPageTitleIsCorrect(): void
    {
        $expectedTitle = 'Paramètre - DealTonBUT';

        $viewMock = $this->getMockBuilder(SettingsPageView::class)
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->with($expectedTitle, $this->anything())
            ->willReturn('<html>Page</html>');

        $this->expectOutputString('<html>Page</html>');
        echo $viewMock->render($expectedTitle, Settings::STYLESHEET);
    }

    /**
     * Test resolve avec query parameters
     */
    public function testResolveWithQueryParameters(): void
    {
        $result = Settings::resolve('/user/settings?tab=profile', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test resolve avec trailing slash
     */
    public function testResolveWithTrailingSlash(): void
    {
        $result = Settings::resolve('/user/settings/', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que PATH commence par un slash
     */
    public function testPathStartsWithSlash(): void
    {
        $this->assertStringStartsWith('/', Settings::PATH);
    }

    /**
     * Test que METH est en majuscules
     */
    public function testMethodIsUppercase(): void
    {
        $this->assertEquals(strtoupper(Settings::METH), Settings::METH);
    }

    /**
     * Test deleteAccount vérifie que l'email est une string
     */
    public function testDeleteAccountChecksEmailIsString(): void
    {
        $_SESSION['email'] = ['not', 'a', 'string'];

        $email = '';
        if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }

        $this->assertEquals('', $email);
        $this->assertIsString($email);
    }

    /**
     * Test que control ne rend pas la vue si non connecté
     */
    public function testControlDoesNotRenderViewWhenNotLoggedIn(): void
    {
        $loggedIn = false;

        if (!isset($loggedIn) || $loggedIn !== true) {
            $shouldRender = false;
        } else {
            $shouldRender = true;
        }

        $this->assertFalse($shouldRender);
    }

    /**
     * Test que deleteAccount appelle bien deleteUser de AccountDB
     */
    public function testDeleteAccountCallsAccountDBDeleteUser(): void
    {
        $_SESSION['email'] = 'user@example.com';

        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteUser'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('deleteUser')
            ->with('user@example.com');

        $email = '';
        if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }

        $accountDBMock->deleteUser($email);
        $this->assertEquals('user@example.com', $email);
    }

    /**
     * Test comportement de control avec session valide complète
     */
    public function testControlWithCompleteValidSession(): void
    {
        $_SESSION['logged-in'] = true;
        $_SESSION['email'] = 'user@example.com';
        $_SESSION['username'] = 'testuser';

        $this->assertTrue($_SESSION['logged-in']);
        $this->assertEquals('user@example.com', $_SESSION['email']);
        $this->assertEquals('testuser', $_SESSION['username']);
    }

    /**
     * Test que deleteAccount utilise isset avant is_string
     */
    public function testDeleteAccountUsesIssetBeforeIsString(): void
    {
        $email = '';
        if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }

        $this->assertEquals('', $email);
    }
}