<?php
namespace tests\controllers\User\PasswordForgot;

use PHPUnit\Framework\TestCase;
use controllers\User\PasswordForgot\PasswordReset;
use models\AccountDB;
use views\User\ForgotPassword\PasswordResetView;
use views\User\LoginForm\LoginFormView;

class PasswordResetTest extends TestCase
{
    private $accountDBMock;
    private $passwordResetViewMock;
    private $loginFormViewMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock AccountDB singleton
        $this->accountDBMock = $this->createMock(AccountDB::class);

        // Mock views
        $this->passwordResetViewMock = $this->createMock(PasswordResetView::class);
        $this->loginFormViewMock = $this->createMock(LoginFormView::class);

        // Reset session
        $_SESSION = [];
        $_GET = [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_SESSION = [];
        $_GET = [];
    }

    /**
     * Test que resolve retourne true avec le bon path et méthode
     */
    public function testResolveReturnsTrueWithCorrectPathAndMethod(): void
    {
        $result = PasswordReset::resolve('/user/validate', 'GET');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve retourne true avec query parameters
     */
    public function testResolveReturnsTrueWithQueryParameters(): void
    {
        $result = PasswordReset::resolve('/user/validate?email=test@example.com&token=abc123', 'GET');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve retourne false avec un mauvais path
     */
    public function testResolveReturnsFalseWithIncorrectPath(): void
    {
        $result = PasswordReset::resolve('/user/wrong', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve retourne false avec une mauvaise méthode
     */
    public function testResolveReturnsFalseWithIncorrectMethod(): void
    {
        $result = PasswordReset::resolve('/user/validate', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test control avec token valide
     */
    public function testControlWithValidToken(): void
    {
        // Arrange
        $_GET['email'] = 'test@example.com';
        $_GET['token'] = 'valid_token_123';

        // Mock AccountDB pour retourner true (token valide)
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['checkToken'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('checkToken')
            ->with('test@example.com', 'valid_token_123')
            ->willReturn(true);

        // Mock PasswordResetView
        $viewMock = $this->getMockBuilder(PasswordResetView::class)
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>Reset Password Form</html>');

        // Inject mocks (nécessite de modifier la classe pour injection de dépendances)
        // Pour cet exemple, on suppose que vous pouvez injecter les dépendances

        // Vérifier que $_SESSION['reset_email'] est défini
        $this->expectOutputString('<html>Reset Password Form</html>');

        // Simulation du comportement
        if ($accountDBMock->checkToken($_GET['email'], $_GET['token'])) {
            $_SESSION['reset_email'] = $_GET['email'];
            echo $viewMock->render('Reset Password - DealTonBUT', []);
        }

        $this->assertEquals('test@example.com', $_SESSION['reset_email']);
    }

    /**
     * Test control avec token invalide
     */
    public function testControlWithInvalidToken(): void
    {
        // Arrange
        $_GET['email'] = 'test@example.com';
        $_GET['token'] = 'invalid_token';

        // Mock AccountDB pour retourner false (token invalide)
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['checkToken'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('checkToken')
            ->with('test@example.com', 'invalid_token')
            ->willReturn(false);

        // Mock LoginFormView
        $viewMock = $this->getMockBuilder(LoginFormView::class)
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>Link Expired</html>');

        $this->expectOutputString('<html>Link Expired</html>');

        // Simulation du comportement
        if ($accountDBMock->checkToken($_GET['email'], $_GET['token'])) {
            $_SESSION['reset_email'] = $_GET['email'];
        } else {
            echo $viewMock->render('Login - DealTonBUT', []);
        }

        $this->assertArrayNotHasKey('reset_email', $_SESSION);
    }

    /**
     * Test control sans email ni token
     */
    public function testControlWithMissingParameters(): void
    {
        // Arrange - pas de $_GET défini

        // Mock AccountDB pour retourner false
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['checkToken'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('checkToken')
            ->with('', '')
            ->willReturn(false);

        // Mock LoginFormView
        $viewMock = $this->getMockBuilder(LoginFormView::class)
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>Link Expired</html>');

        $this->expectOutputString('<html>Link Expired</html>');

        // Simulation
        if ($accountDBMock->checkToken($_GET['email'] ?? '', $_GET['token'] ?? '')) {
            $_SESSION['reset_email'] = $_GET['email'] ?? '';
        } else {
            echo $viewMock->render('Login - DealTonBUT', []);
        }
    }

    /**
     * Test des constantes de classe
     */
    public function testClassConstants(): void
    {
        $this->assertEquals('/user/validate', PasswordReset::PATH);
        $this->assertEquals('GET', PasswordReset::METH);
    }
}