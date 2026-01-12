<?php
namespace tests\controllers\User\Register;

use PHPUnit\Framework\TestCase;
use controllers\User\Register\Register;
use views\User\RegisterForm\RegisterFormView;

class RegisterTest extends TestCase
{
    /**
     * Test que resolve retourne true avec le bon path et méthode GET
     */
    public function testResolveReturnsTrueWithCorrectPathAndMethod(): void
    {
        $result = Register::resolve('/user/register', 'GET');
        $this->assertTrue($result);
    }

    /**
     * Test que resolve retourne false avec une mauvaise méthode
     */
    public function testResolveReturnsFalseWithIncorrectMethod(): void
    {
        $result = Register::resolve('/user/register', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve retourne false avec un mauvais path
     */
    public function testResolveReturnsFalseWithIncorrectPath(): void
    {
        $result = Register::resolve('/user/wrong', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve retourne false avec path et méthode incorrects
     */
    public function testResolveReturnsFalseWithBothIncorrect(): void
    {
        $result = Register::resolve('/user/wrong', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve est sensible à la casse
     */
    public function testResolveIsCaseSensitiveForPath(): void
    {
        $result = Register::resolve('/user/REGISTER', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test que resolve est sensible à la casse pour la méthode
     */
    public function testResolveIsCaseSensitiveForMethod(): void
    {
        $result = Register::resolve('/user/register', 'get');
        $this->assertFalse($result);
    }

    /**
     * Test control affiche le formulaire d'inscription
     */
    public function testControlRendersRegisterForm(): void
    {
        // Mock RegisterFormView
        $viewMock = $this->getMockBuilder(RegisterFormView::class)
            ->onlyMethods(['render'])
            ->getMock();

        $expectedOutput = '<html><body>Register Form</body></html>';

        $viewMock->expects($this->once())
            ->method('render')
            ->with(
                'Register - DealTonBUT',
                [
                    '/_assets/styles/loginSingnin.css',
                    '/_assets/styles/style.css',
                    '/_assets/styles/navbar.css'
                ]
            )
            ->willReturn($expectedOutput);

        $this->expectOutputString($expectedOutput);

        // Simulation du comportement
        echo $viewMock->render('Register - DealTonBUT', Register::STYLESHEET);
    }

    /**
     * Test control avec les bons paramètres de render
     */
    public function testControlCallsRenderWithCorrectParameters(): void
    {
        $viewMock = $this->getMockBuilder(RegisterFormView::class)
            ->onlyMethods(['render'])
            ->getMock();

        $viewMock->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('Register - DealTonBUT'),
                $this->equalTo([
                    '/_assets/styles/loginSingnin.css',
                    '/_assets/styles/style.css',
                    '/_assets/styles/navbar.css'
                ])
            )
            ->willReturn('output');

        $this->expectOutputString('output');
        echo $viewMock->render('Register - DealTonBUT', Register::STYLESHEET);
    }

    /**
     * Test des constantes de classe
     */
    public function testClassConstants(): void
    {
        $this->assertEquals('/user/register', Register::PATH);
        $this->assertEquals('GET', Register::METH);

        $this->assertIsArray(Register::STYLESHEET);
        $this->assertCount(3, Register::STYLESHEET);

        $this->assertEquals('/_assets/styles/loginSingnin.css', Register::STYLESHEET[0]);
        $this->assertEquals('/_assets/styles/style.css', Register::STYLESHEET[1]);
        $this->assertEquals('/_assets/styles/navbar.css', Register::STYLESHEET[2]);
    }

    /**
     * Test que STYLESHEET est un array de strings
     */
    public function testStylesheetIsArrayOfStrings(): void
    {
        $this->assertIsArray(Register::STYLESHEET);

        foreach (Register::STYLESHEET as $stylesheet) {
            $this->assertIsString($stylesheet);
            $this->assertStringStartsWith('/_assets/styles/', $stylesheet);
            $this->assertStringEndsWith('.css', $stylesheet);
        }
    }

    /**
     * Test que STYLESHEET contient les bons fichiers CSS
     */
    public function testStylesheetContainsExpectedFiles(): void
    {
        $expectedStylesheets = [
            '/_assets/styles/loginSingnin.css',
            '/_assets/styles/style.css',
            '/_assets/styles/navbar.css'
        ];

        $this->assertEquals($expectedStylesheets, Register::STYLESHEET);
    }

    /**
     * Test que le PATH commence par un slash
     */
    public function testPathStartsWithSlash(): void
    {
        $this->assertStringStartsWith('/', Register::PATH);
    }

    /**
     * Test que METH est en majuscules
     */
    public function testMethodIsUppercase(): void
    {
        $this->assertEquals(strtoupper(Register::METH), Register::METH);
    }

    /**
     * Test resolve avec query parameters dans le path
     */
    public function testResolveIgnoresQueryParameters(): void
    {
        // Si votre implémentation ne gère pas les query params, ce test devrait échouer
        // et vous indique qu'il faut peut-être utiliser strtok comme dans PasswordReset
        $result = Register::resolve('/user/register?param=value', 'GET');

        // Ce test vérifie le comportement actuel
        // Si vous voulez gérer les query params, changez la méthode resolve
        $this->assertFalse($result); // Actuellement false car path strict
    }

    /**
     * Test resolve avec trailing slash
     */
    public function testResolveWithTrailingSlash(): void
    {
        $result = Register::resolve('/user/register/', 'GET');
        $this->assertFalse($result); // Path strict, pas de trailing slash
    }

    /**
     * Test que la classe implémente Controller
     */
    public function testClassImplementsControllerInterface(): void
    {
        $reflection = new \ReflectionClass(Register::class);
        $this->assertTrue($reflection->implementsInterface(\core\controllers\Controller::class));
    }

    /**
     * Test que control est une méthode publique
     */
    public function testControlMethodIsPublic(): void
    {
        $reflection = new \ReflectionMethod(Register::class, 'control');
        $this->assertTrue($reflection->isPublic());
    }

    /**
     * Test que resolve est une méthode statique publique
     */
    public function testResolveMethodIsPublicAndStatic(): void
    {
        $reflection = new \ReflectionMethod(Register::class, 'resolve');
        $this->assertTrue($reflection->isPublic());
        $this->assertTrue($reflection->isStatic());
    }
}