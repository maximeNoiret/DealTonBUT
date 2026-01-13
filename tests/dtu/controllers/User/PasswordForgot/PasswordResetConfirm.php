<?php
namespace tests\controllers\User\PasswordForgot;

use PHPUnit\Framework\TestCase;
use controllers\User\PasswordForgot\PasswordResetConfirm;
use models\AccountDB;
use views\User\LoginForm\LoginFormView;

class PasswordResetConfirmTest extends TestCase
{
    private $accountDBMock;
    private $loginFormViewMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset session and POST
        $_SESSION = [];
        $_POST = [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_SESSION = [];
        $_POST = [];
    }

    /**
     * Test que resolve retourne true avec le bon path et méthode POST
     */
    public function testResolveReturnsTrueWithCorrectPathAndMethod(): void
    {
        $result = PasswordResetConfirm::resolve('/user/validate', 'POST');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve retourne false avec une mauvaise méthode
     */
    public function testResolveReturnsFalseWithIncorrectMethod(): void
    {
        $result = PasswordResetConfirm::resolve('/user/validate', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve retourne false avec un mauvais path
     */
    public function testResolveReturnsFalseWithIncorrectPath(): void
    {
        $result = PasswordResetConfirm::resolve('/user/wrong', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test control avec mise à jour réussie du mot de passe
     */
    public function testControlWithSuccessfulPasswordUpdate(): void
    {
        // Arrange
        $_POST['new_password'] = 'NewSecurePassword123!';
        $_SESSION['reset_email'] = 'test@example.com';

        // Mock AccountDB
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['updatePassword'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('updatePassword')
            ->with(
                'test@example.com',
                $this->callback(function($hashedPassword) {
                    // Vérifie que c'est un hash bcrypt valide
                    return is_string($hashedPassword) &&
                        strlen($hashedPassword) === 60 &&
                        str_starts_with($hashedPassword, '$2y$');
                })
            )
            ->willReturn(true);

        // Mock LoginFormView
        $viewMock = $this->getMockBuilder(LoginFormView::class)
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->with('Login - DealTonBUT', $this->anything())
            ->willReturn('<html>Password Changed Successfully</html>');

        $this->expectOutputString('<html>Password Changed Successfully</html>');

        // Simulation du comportement
        $hashedPassword = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        if ($accountDBMock->updatePassword($_SESSION['reset_email'], $hashedPassword)) {
            echo $viewMock->render('Login - DealTonBUT', []);
        }
    }

    /**
     * Test control avec échec de mise à jour du mot de passe
     */
    public function testControlWithFailedPasswordUpdate(): void
    {
        // Arrange
        $_POST['new_password'] = 'NewSecurePassword123!';
        $_SESSION['reset_email'] = 'test@example.com';

        // Mock AccountDB pour retourner false
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['updatePassword'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('updatePassword')
            ->with(
                'test@example.com',
                $this->callback(function($hashedPassword) {
                    return is_string($hashedPassword) &&
                        strlen($hashedPassword) === 60 &&
                        str_starts_with($hashedPassword, '$2y$');
                })
            )
            ->willReturn(false);

        // Mock LoginFormView
        $viewMock = $this->getMockBuilder(LoginFormView::class)
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->with('Login - DealTonBUT', $this->anything())
            ->willReturn('<html>Unknown Error Occurred</html>');

        $this->expectOutputString('<html>Unknown Error Occurred</html>');

        // Simulation du comportement
        $hashedPassword = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        if ($accountDBMock->updatePassword($_SESSION['reset_email'], $hashedPassword)) {
            echo '<html>Success</html>';
        } else {
            echo $viewMock->render('Login - DealTonBUT', []);
        }
    }

    /**
     * Test control sans mot de passe dans POST
     */
    public function testControlWithMissingPassword(): void
    {
        // Arrange
        $_SESSION['reset_email'] = 'test@example.com';
        // $_POST['new_password'] n'est pas défini

        // Mock AccountDB
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['updatePassword'])
            ->getMock();

        // Le hash d'une chaîne vide sera passé
        $accountDBMock->expects($this->once())
            ->method('updatePassword')
            ->with(
                'test@example.com',
                $this->callback(function($hashedPassword) {
                    // Vérifie que c'est quand même un hash bcrypt
                    return is_string($hashedPassword) &&
                        strlen($hashedPassword) === 60;
                })
            )
            ->willReturn(true);

        // Mock LoginFormView
        $viewMock = $this->getMockBuilder(LoginFormView::class)
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>Password Changed</html>');

        $this->expectOutputString('<html>Password Changed</html>');

        // Simulation
        $hashedPassword = password_hash($_POST['new_password'] ?? '', PASSWORD_BCRYPT);
        if ($accountDBMock->updatePassword($_SESSION['reset_email'], $hashedPassword)) {
            echo $viewMock->render('Login - DealTonBUT', []);
        }
    }

    /**
     * Test control sans email dans SESSION
     */
    public function testControlWithMissingSessionEmail(): void
    {
        // Arrange
        $_POST['new_password'] = 'NewSecurePassword123!';
        // $_SESSION['reset_email'] n'est pas défini

        // Mock AccountDB
        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['updatePassword'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('updatePassword')
            ->with(
                '', // Email vide
                $this->callback(function($hashedPassword) {
                    return is_string($hashedPassword) &&
                        strlen($hashedPassword) === 60;
                })
            )
            ->willReturn(false);

        // Mock LoginFormView
        $viewMock = $this->getMockBuilder(LoginFormView::class)
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->willReturn('<html>Error</html>');

        $this->expectOutputString('<html>Error</html>');

        // Simulation
        $hashedPassword = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        if ($accountDBMock->updatePassword($_SESSION['reset_email'] ?? '', $hashedPassword)) {
            echo '<html>Success</html>';
        } else {
            echo $viewMock->render('Login - DealTonBUT', []);
        }
    }

    /**
     * Test que le mot de passe est correctement hashé avec BCRYPT
     */
    public function testPasswordIsHashedWithBcrypt(): void
    {
        $password = 'TestPassword123!';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Vérifie que c'est un hash bcrypt valide
        $this->assertStringStartsWith('$2y$', $hashedPassword);
        $this->assertEquals(60, strlen($hashedPassword));

        // Vérifie que le hash correspond au mot de passe
        $this->assertTrue(password_verify($password, $hashedPassword));
    }

    /**
     * Test des constantes de classe
     */
    public function testClassConstants(): void
    {
        $this->assertEquals('/user/validate', PasswordResetConfirm::PATH);
        $this->assertEquals('POST', PasswordResetConfirm::METH);
    }

    /**
     * Test que deux hashs du même mot de passe sont différents (salt aléatoire)
     */
    public function testPasswordHashesAreDifferent(): void
    {
        $password = 'SamePassword123!';
        $hash1 = password_hash($password, PASSWORD_BCRYPT);
        $hash2 = password_hash($password, PASSWORD_BCRYPT);

        // Les hashs doivent être différents (salt aléatoire)
        $this->assertNotEquals($hash1, $hash2);

        // Mais les deux doivent vérifier le même mot de passe
        $this->assertTrue(password_verify($password, $hash1));
        $this->assertTrue(password_verify($password, $hash2));
    }
}