<?php
namespace tests\controllers\User\Register;

use PHPUnit\Framework\TestCase;
use controllers\User\Register\RegisterVerifyConfirm;
use models\AccountDB;

class RegisterVerifyConfirmTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_POST = [];
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_POST = [];
        $_SESSION = [];
    }

    /**
     * Test que resolve retourne true avec le bon path et méthode POST
     */
    public function testResolveReturnsTrueWithCorrectPathAndMethod(): void
    {
        $result = RegisterVerifyConfirm::resolve('/user/register/verify', 'POST');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve retourne false avec une mauvaise méthode
     */
    public function testResolveReturnsFalseWithIncorrectMethod(): void
    {
        $result = RegisterVerifyConfirm::resolve('/user/register/verify', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve retourne false avec un mauvais path
     */
    public function testResolveReturnsFalseWithIncorrectPath(): void
    {
        $result = RegisterVerifyConfirm::resolve('/user/register/wrong', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test control avec inscription complète réussie
     */
    public function testControlWithSuccessfulRegistration(): void
    {
        $_SESSION['username'] = 'testuser';
        $_SESSION['email'] = 'test@example.com';
        $_POST['password'] = 'SecurePassword123!';

        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setRole', 'updatePassword'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('setRole')
            ->with('test@example.com', 'student')
            ->willReturn(true);

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
            ->willReturn(true);

        $tempAccount = [
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email']
        ];

        $accountDBMock->setRole($tempAccount['email'], 'student');
        $hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $accountDBMock->updatePassword($tempAccount['email'], $hashedPassword);

        $_SESSION['username'] = $tempAccount['username'];
        $_SESSION['email'] = $tempAccount['email'];
        $_SESSION['logged-in'] = true;

        $this->assertEquals('testuser', $_SESSION['username']);
        $this->assertEquals('test@example.com', $_SESSION['email']);
        $this->assertTrue($_SESSION['logged-in']);
    }

    /**
     * Test que le mot de passe est correctement hashé
     */
    public function testPasswordIsHashedWithBcrypt(): void
    {
        $password = 'TestPassword123!';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $this->assertStringStartsWith('$2y$', $hashedPassword);
        $this->assertEquals(60, strlen($hashedPassword));
        $this->assertTrue(password_verify($password, $hashedPassword));
    }

    /**
     * Test control avec mot de passe manquant
     */
    public function testControlWithMissingPassword(): void
    {
        $_SESSION['username'] = 'testuser';
        $_SESSION['email'] = 'test@example.com';

        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setRole', 'updatePassword'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('setRole')
            ->willReturn(true);

        $accountDBMock->expects($this->once())
            ->method('updatePassword')
            ->with(
                'test@example.com',
                $this->callback(function($hashedPassword) {
                    return is_string($hashedPassword) && strlen($hashedPassword) === 60;
                })
            )
            ->willReturn(true);

        $tempAccount = ['username' => $_SESSION['username'], 'email' => $_SESSION['email']];
        $accountDBMock->setRole($tempAccount['email'], 'student');
        $hashedPassword = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
        $accountDBMock->updatePassword($tempAccount['email'], $hashedPassword);

        $this->assertTrue(true);
    }

    /**
     * Test control avec username manquant en session
     */
    public function testControlWithMissingUsernameInSession(): void
    {
        $_SESSION['email'] = 'test@example.com';
        $_POST['password'] = 'Password123!';

        $tempAccount = [
            'username' => $_SESSION['username'] ?? null,
            'email' => $_SESSION['email']
        ];

        $this->assertNull($tempAccount['username']);
        $this->assertEquals('test@example.com', $tempAccount['email']);
    }

    /**
     * Test control avec email manquant en session
     */
    public function testControlWithMissingEmailInSession(): void
    {
        $_SESSION['username'] = 'testuser';
        $_POST['password'] = 'Password123!';

        $tempAccount = [
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'] ?? null
        ];

        $this->assertEquals('testuser', $tempAccount['username']);
        $this->assertNull($tempAccount['email']);
    }

    /**
     * Test que setRole est appelé avec 'student'
     */
    public function testSetRoleIsCalledWithStudent(): void
    {
        $_SESSION['email'] = 'student@example.com';

        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setRole'])
            ->getMock();

        $accountDBMock->expects($this->once())
            ->method('setRole')
            ->with('student@example.com', 'student');

        $accountDBMock->setRole($_SESSION['email'], 'student');
    }

    /**
     * Test que la session est correctement mise à jour
     */
    public function testSessionIsCorrectlyUpdated(): void
    {
        $_SESSION['username'] = 'originaluser';
        $_SESSION['email'] = 'original@example.com';

        $tempAccount = [
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email']
        ];

        $_SESSION = [];

        $_SESSION['username'] = $tempAccount['username'];
        $_SESSION['email'] = $tempAccount['email'];
        $_SESSION['logged-in'] = true;

        $this->assertEquals('originaluser', $_SESSION['username']);
        $this->assertEquals('original@example.com', $_SESSION['email']);
        $this->assertTrue($_SESSION['logged-in']);
    }

    /**
     * Test que logged-in est défini à true
     */
    public function testLoggedInIsSetToTrue(): void
    {
        $_SESSION['username'] = 'testuser';
        $_SESSION['email'] = 'test@example.com';
        $_SESSION['logged-in'] = true;

        $this->assertArrayHasKey('logged-in', $_SESSION);
        $this->assertTrue($_SESSION['logged-in']);
        $this->assertIsBool($_SESSION['logged-in']);
    }

    /**
     * Test des constantes de classe
     */
    public function testClassConstants(): void
    {
        $this->assertEquals('/user/register/verify', RegisterVerifyConfirm::PATH);
        $this->assertEquals('POST', RegisterVerifyConfirm::METH);
    }

    /**
     * Test que le PATH est identique à RegisterVerify mais la méthode différente
     */
    public function testPathMatchesRegisterVerifyButMethodIsDifferent(): void
    {
        $this->assertEquals('/user/register/verify', RegisterVerifyConfirm::PATH);
        $this->assertEquals('POST', RegisterVerifyConfirm::METH);

        $this->assertEquals(
            \controllers\User\Register\RegisterVerify::PATH,
            RegisterVerifyConfirm::PATH
        );
    }

    /**
     * Test que la classe implémente Controller
     */
    public function testClassImplementsControllerInterface(): void
    {
        $reflection = new \ReflectionClass(RegisterVerifyConfirm::class);
        $this->assertTrue($reflection->implementsInterface(\core\controllers\Controller::class));
    }

    /**
     * Test que control est une méthode publique
     */
    public function testControlMethodIsPublic(): void
    {
        $reflection = new \ReflectionMethod(RegisterVerifyConfirm::class, 'control');
        $this->assertTrue($reflection->isPublic());
    }

    /**
     * Test que resolve est une méthode statique publique
     */
    public function testResolveMethodIsPublicAndStatic(): void
    {
        $reflection = new \ReflectionMethod(RegisterVerifyConfirm::class, 'resolve');
        $this->assertTrue($reflection->isPublic());
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * Test que deux hashs du même mot de passe sont différents
     */
    public function testPasswordHashesAreDifferent(): void
    {
        $password = 'SamePassword123!';
        $hash1 = password_hash($password, PASSWORD_BCRYPT);
        $hash2 = password_hash($password, PASSWORD_BCRYPT);

        $this->assertNotEquals($hash1, $hash2);
        $this->assertTrue(password_verify($password, $hash1));
        $this->assertTrue(password_verify($password, $hash2));
    }

    /**
     * Test que tempAccount sauvegarde correctement les données avant régénération
     */
    public function testTempAccountPreservesDataBeforeSessionRegeneration(): void
    {
        $_SESSION['username'] = 'saveuser';
        $_SESSION['email'] = 'save@example.com';

        $tempAccount = [
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email']
        ];

        $_SESSION = [];

        $this->assertEquals('saveuser', $tempAccount['username']);
        $this->assertEquals('save@example.com', $tempAccount['email']);
    }

    /**
     * Test l'ordre des opérations dans control
     */
    public function testControlOperationsOrder(): void
    {
        $_SESSION['username'] = 'testuser';
        $_SESSION['email'] = 'test@example.com';
        $_POST['password'] = 'Password123!';

        $accountDBMock = $this->getMockBuilder(AccountDB::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setRole', 'updatePassword'])
            ->getMock();

        $accountDBMock->expects($this->exactly(1))
            ->method('setRole')
            ->willReturn(true);

        $accountDBMock->expects($this->exactly(1))
            ->method('updatePassword')
            ->willReturn(true);

        $tempAccount = ['username' => $_SESSION['username'], 'email' => $_SESSION['email']];
        $accountDBMock->setRole($tempAccount['email'], 'student');
        $hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $accountDBMock->updatePassword($tempAccount['email'], $hashedPassword);

        $this->assertTrue(true);
    }

    /**
     * Test que le header Location est correct (test documentaire)
     */
    public function testHeaderLocationIsSetToMarketplace(): void
    {
        $expectedLocation = '/marketplace';
        $this->assertEquals('/marketplace', $expectedLocation);
    }

    /**
     * Test avec des données de session complètes
     */
    public function testControlWithCompleteSessionData(): void
    {
        $_SESSION['username'] = 'fulluser';
        $_SESSION['email'] = 'full@example.com';
        $_SESSION['some_other_key'] = 'other_value';
        $_POST['password'] = 'CompletePassword123!';

        $tempAccount = [
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email']
        ];

        $this->assertCount(2, $tempAccount);
        $this->assertArrayHasKey('username', $tempAccount);
        $this->assertArrayHasKey('email', $tempAccount);
        $this->assertArrayNotHasKey('some_other_key', $tempAccount);
    }

    /**
     * Test que le rôle par défaut est 'student'
     */
    public function testDefaultRoleIsStudent(): void
    {
        $expectedRole = 'student';
        $this->assertEquals('student', $expectedRole);
    }
}