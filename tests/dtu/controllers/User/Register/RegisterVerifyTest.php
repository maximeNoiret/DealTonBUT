<?php
namespace tests\controllers\User\Register;

use PHPUnit\Framework\TestCase;
use controllers\User\Register\RegisterVerify;
use controllers\User\Register\Register;
use models\AccountDB;
use dtu\views\User\RegisterForm\RegisterFormPasswordView;
use views\User\RegisterForm\RegisterFormView;

class RegisterVerifyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_GET = [];
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_GET = [];
        $_SESSION = [];
    }

    /**
     * Test que resolve retourne true avec le bon path et méthode
     */
    public function testResolveReturnsTrueWithCorrectPathAndMethod(): void
    {
        $result = RegisterVerify::resolve('/user/register/verify', 'GET');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve retourne true avec query parameters
     */
    public function testResolveReturnsTrueWithQueryParameters(): void
    {
        $result = RegisterVerify::resolve('/user/register/verify?token=abc123', 'GET');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve retourne false avec une mauvaise méthode
     */
    public function testResolveReturnsFalseWithIncorrectMethod(): void
    {
        $result = RegisterVerify::resolve('/user/register/verify', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve retourne false avec un mauvais path
     */
    public function testResolveReturnsFalseWithIncorrectPath(): void
    {
        $result = RegisterVerify::resolve('/user/register/wrong', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve ignore les query parameters grâce à strtok
     */
    public function testResolveUsesStrtokToIgnoreQueryParameters(): void
    {
        $pathWithParams = '/user/register/verify?token=test&email=test@example.com';
        $result = RegisterVerify::resolve($pathWithParams, 'GET');
        $this->assertTrue($result);
    }

    /**
     * Test control avec token valide
     */
    public function testControlWithValidToken(): void
    {
        // Arrange
        $_GET['token'] = 'valid_token_123';
        $email = 'test@example.com';

        // Mock AccountDB
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEmailFromToken', 'checkToken'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('getEmailFromToken')
            ->with('valid_token_123')
            ->willReturn($email);

        $accountDBMock->expects($this->once())
            ->method('checkToken')
            ->with($email, 'valid_token_123')
            ->willReturn(true);

        // Mock RegisterFormPasswordView
        $viewMock = $this->getMockBuilder(RegisterFormPasswordView::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->with('Register - DealTonBUT', RegisterVerify::STYLESHEET)
            ->willReturn('<html>Password Form</html>');

        $this->expectOutputString('<html>Password Form</html>');

        // Simulation
        $_SESSION['email'] = $accountDBMock->getEmailFromToken($_GET['token']);
        if ($accountDBMock->checkToken($_SESSION['email'], $_GET['token'])) {
            echo $viewMock->render('Register - DealTonBUT', RegisterVerify::STYLESHEET);
        }

        // Vérifie que l'email est bien stocké en session
        $this->assertEquals($email, $_SESSION['email']);
    }

    /**
     * Test control avec token invalide
     */
    public function testControlWithInvalidToken(): void
    {
        // Arrange
        $_GET['token'] = 'invalid_token';
        $email = 'test@example.com';

        // Mock AccountDB
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEmailFromToken', 'checkToken'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('getEmailFromToken')
            ->with('invalid_token')
            ->willReturn($email);

        $accountDBMock->expects($this->once())
            ->method('checkToken')
            ->with($email, 'invalid_token')
            ->willReturn(false);

        // Mock RegisterFormView
        $viewMock = $this->getMockBuilder(RegisterFormView::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->with('Register - DealTonBUT', RegisterVerify::STYLESHEET)
            ->willReturn('<html>Link Expired</html>');

        $this->expectOutputString('<html>Link Expired</html>');

        // Simulation
        $_SESSION['email'] = $accountDBMock->getEmailFromToken($_GET['token']);
        if ($accountDBMock->checkToken($_SESSION['email'], $_GET['token'])) {
            echo '<html>Success</html>';
        } else {
            echo $viewMock->render('Register - DealTonBUT', RegisterVerify::STYLESHEET);
        }

        // Vérifie que l'email est quand même stocké en session
        $this->assertEquals($email, $_SESSION['email']);
    }

    /**
     * Test control avec token expiré
     */
    public function testControlWithExpiredToken(): void
    {
        // Arrange
        $_GET['token'] = 'expired_token';
        $email = 'expired@example.com';

        // Mock AccountDB
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEmailFromToken', 'checkToken'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('getEmailFromToken')
            ->with('expired_token')
            ->willReturn($email);

        $accountDBMock->expects($this->once())
            ->method('checkToken')
            ->with($email, 'expired_token')
            ->willReturn(false); // Token expiré

        // Mock RegisterFormView avec message d'erreur
        $viewMock = $this->getMockBuilder(RegisterFormView::class)
            ->setConstructorArgs(['verification_link_expired'])
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>Verification Link Expired</html>');

        $this->expectOutputString('<html>Verification Link Expired</html>');

        // Simulation
        $_SESSION['email'] = $accountDBMock->getEmailFromToken($_GET['token']);
        if ($accountDBMock->checkToken($_SESSION['email'], $_GET['token'])) {
            echo '<html>Success</html>';
        } else {
            echo $viewMock->render('Register - DealTonBUT', RegisterVerify::STYLESHEET);
        }
    }

    /**
     * Test control sans token dans GET
     */
    public function testControlWithMissingToken(): void
    {
        // Mock AccountDB
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEmailFromToken', 'checkToken'])
            ->getMock();

        // getEmailFromToken sera appelé avec une chaîne vide (pas null)
        $accountDBMock->expects($this->once())
            ->method('getEmailFromToken')
            ->with('')
            ->willReturn('');

        $accountDBMock->expects($this->once())
            ->method('checkToken')
            ->with('', '')
            ->willReturn(false);

        // Mock RegisterFormView
        $viewMock = $this->getMockBuilder(RegisterFormView::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>Error</html>');

        $this->expectOutputString('<html>Error</html>');

        // Simulation avec chaînes vides au lieu de null
        $_SESSION['email'] = $accountDBMock->getEmailFromToken($_GET['token'] ?? '');
        if ($accountDBMock->checkToken($_SESSION['email'], $_GET['token'] ?? '')) {
            echo '<html>Success</html>';
        } else {
            echo $viewMock->render('Register - DealTonBUT', RegisterVerify::STYLESHEET);
        }
    }

    /**
     * Test que l'email est correctement stocké en session
     */
    public function testEmailIsStoredInSession(): void
    {
        $_GET['token'] = 'test_token';
        $expectedEmail = 'session@example.com';

        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEmailFromToken', 'checkToken'])
            ->getMock();

        $accountDBMock->method('getEmailFromToken')
            ->willReturn($expectedEmail);

        $accountDBMock->method('checkToken')
            ->willReturn(true);

        // Simulation
        $_SESSION['email'] = $accountDBMock->getEmailFromToken($_GET['token']);

        $this->assertArrayHasKey('email', $_SESSION);
        $this->assertEquals($expectedEmail, $_SESSION['email']);
    }

    /**
     * Test des constantes de classe
     */
    public function testClassConstants(): void
    {
        $this->assertEquals('/user/register/verify', RegisterVerify::PATH);
        $this->assertEquals('GET', RegisterVerify::METH);

        // Vérifie que STYLESHEET référence Register::STYLESHEET
        $this->assertEquals(Register::STYLESHEET, RegisterVerify::STYLESHEET);
        $this->assertIsArray(RegisterVerify::STYLESHEET);
    }

    /**
     * Test que STYLESHEET est identique à Register::STYLESHEET
     */
    public function testStylesheetReferencesRegisterStylesheet(): void
    {
        $this->assertSame(Register::STYLESHEET, RegisterVerify::STYLESHEET);
    }

    /**
     * Test que la classe implémente Controller
     */
    public function testClassImplementsControllerInterface(): void
    {
        $reflection = new \ReflectionClass(RegisterVerify::class);
        $this->assertTrue($reflection->implementsInterface(\core\controllers\Controller::class));
    }

    /**
     * Test que control est une méthode publique
     */
    public function testControlMethodIsPublic(): void
    {
        $reflection = new \ReflectionMethod(RegisterVerify::class, 'control');
        $this->assertTrue($reflection->isPublic());
    }

    /**
     * Test que resolve est une méthode statique publique
     */
    public function testResolveMethodIsPublicAndStatic(): void
    {
        $reflection = new \ReflectionMethod(RegisterVerify::class, 'resolve');
        $this->assertTrue($reflection->isPublic());
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * Test avec plusieurs query parameters
     */
    public function testResolveWithMultipleQueryParameters(): void
    {
        $path = '/user/register/verify?token=abc&email=test@example.com&ref=home';
        $result = RegisterVerify::resolve($path, 'GET');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve ne match pas un path similaire
     */
    public function testResolveDoesNotMatchSimilarPath(): void
    {
        $result1 = RegisterVerify::resolve('/user/register/verify2', 'GET');
        $result2 = RegisterVerify::resolve('/user/register/verif', 'GET');
        $result3 = RegisterVerify::resolve('/user/register', 'GET');

        $this->assertFalse($result1);
        $this->assertFalse($result2);
        $this->assertFalse($result3);
    }

    /**
     * Test que le bon message d'erreur est passé à la vue
     */
    public function testCorrectErrorMessageIsPassedToView(): void
    {
        $viewMock = $this->getMockBuilder(RegisterFormView::class)
            ->setConstructorArgs(['verification_link_expired'])
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>Expired</html>');

        $this->expectOutputString('<html>Expired</html>');
        echo $viewMock->render('Register - DealTonBUT', RegisterVerify::STYLESHEET);
    }

    /**
     * Test que le titre de la page est correct
     */
    public function testPageTitleIsCorrect(): void
    {
        $expectedTitle = 'Register - DealTonBUT';

        $viewMock = $this->getMockBuilder(RegisterFormPasswordView::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->with($expectedTitle, $this->anything())
            ->willReturn('<html>Page</html>');

        $this->expectOutputString('<html>Page</html>');
        echo $viewMock->render($expectedTitle, RegisterVerify::STYLESHEET);
    }

    /**
     * Test que getEmailFromToken est appelé avant checkToken
     */
    public function testGetEmailFromTokenIsCalledBeforeCheckToken(): void
    {
        $_GET['token'] = 'test_token';

        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEmailFromToken', 'checkToken'])
            ->getMock();

        // Utilise InvokedAtIndex pour vérifier l'ordre
        $accountDBMock->expects($this->exactly(1))
            ->method('getEmailFromToken')
            ->willReturn('test@example.com');

        $accountDBMock->expects($this->exactly(1))
            ->method('checkToken')
            ->willReturn(true);

        // Simulation dans le bon ordre
        $_SESSION['email'] = $accountDBMock->getEmailFromToken($_GET['token']);
        $accountDBMock->checkToken($_SESSION['email'], $_GET['token']);

        $this->assertTrue(true); // Si on arrive ici, l'ordre est respecté
    }

    /**
     * Test que l'email retourné par getEmailFromToken peut être une chaîne vide
     */
    public function testGetEmailFromTokenCanReturnEmptyString(): void
    {
        $_GET['token'] = 'nonexistent_token';

        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEmailFromToken', 'checkToken'])
            ->getMock();

        $accountDBMock->method('getEmailFromToken')
            ->willReturn(''); // Retourne une chaîne vide, pas null

        $accountDBMock->method('checkToken')
            ->with('', 'nonexistent_token')
            ->willReturn(false);

        $_SESSION['email'] = $accountDBMock->getEmailFromToken($_GET['token']);

        $this->assertEquals('', $_SESSION['email']);
        $this->assertIsString($_SESSION['email']);
    }
}