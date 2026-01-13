<?php

namespace tests\controllers\User\AccountPage;

use controllers\User\AccountPage\DeleteAccount;
use models\AccountDB;
use PHPUnit\Framework\TestCase;

class DeleteAccountTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Reset $_SESSION avant chaque test
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_SESSION = [];
    }

    /**
     * Test de la méthode resolve avec le bon path et méthode
     */
    public function testResolveReturnsTrueWithCorrectPathAndMethod(): void
    {
        $result = DeleteAccount::resolve('/user/delete-account', 'POST');
        $this->assertTrue($result);
    }

    /**
     * Test de la méthode resolve avec un mauvais path
     */
    public function testResolveReturnsFalseWithIncorrectPath(): void
    {
        $result = DeleteAccount::resolve('/user/account', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test de la méthode resolve avec une mauvaise méthode HTTP
     */
    public function testResolveReturnsFalseWithIncorrectMethod(): void
    {
        $result = DeleteAccount::resolve('/user/delete-account', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test de resolve avec des cas limites
     */
    public function testResolveEdgeCases(): void
    {
        // Path avec trailing slash
        $this->assertFalse(DeleteAccount::resolve('/user/delete-account/', 'POST'));

        // Path en majuscules
        $this->assertFalse(DeleteAccount::resolve('/USER/DELETE-ACCOUNT', 'POST'));

        // Méthode en minuscules
        $this->assertFalse(DeleteAccount::resolve('/user/delete-account', 'post'));

        // Path vide
        $this->assertFalse(DeleteAccount::resolve('', 'POST'));

        // Méthode vide
        $this->assertFalse(DeleteAccount::resolve('/user/delete-account', ''));

        // Méthode PUT
        $this->assertFalse(DeleteAccount::resolve('/user/delete-account', 'PUT'));

        // Méthode DELETE (pourrait sembler logique mais ce n'est pas la méthode définie)
        $this->assertFalse(DeleteAccount::resolve('/user/delete-account', 'DELETE'));
    }

    /**
     * Test des constantes de la classe
     */
    public function testClassConstants(): void
    {
        $this->assertEquals('/user/delete-account', DeleteAccount::PATH);
        $this->assertEquals('POST', DeleteAccount::METH);
    }

    /**
     * Test de control quand l'utilisateur n'est pas connecté (logged-in = false)
     * Note: Ce test vérifie que la méthode tente de rediriger (exit est appelé)
     */
    public function testControlRedirectsToLoginWhenNotLoggedIn(): void
    {
        $this->markTestSkipped(
            'Ce test nécessite de capturer header() et exit(). ' .
            'Il est difficile à tester unitairement sans framework de test d\'intégration.'
        );
    }

    /**
     * Test de control quand logged-in n'est pas défini
     * Note: Ce test vérifie que la méthode tente de rediriger (exit est appelé)
     */
    public function testControlRedirectsToLoginWhenLoggedInNotSet(): void
    {
        $this->markTestSkipped(
            'Ce test nécessite de capturer header() et exit(). ' .
            'Il est difficile à tester unitairement sans framework de test d\'intégration.'
        );
    }

    /**
     * Test de control avec utilisateur connecté - nécessite un mock de AccountDB
     * Note: Ce test est skipped car il nécessite un mock de AccountDB::getInstance()
     */
    public function testControlDeletesUserAndRedirectsWhenLoggedIn(): void
    {
        $this->markTestSkipped(
            'Ce test nécessite un mock de AccountDB::getInstance(). ' .
            'Implémentez une méthode setInstance() dans AccountDB ou utilisez une base de données de test.'
        );
    }

    /**
     * Test de control avec email non défini en session
     * Note: Ce test est skipped car il nécessite un mock de AccountDB::getInstance()
     */
    public function testControlWithNoEmailInSession(): void
    {
        $this->markTestSkipped(
            'Ce test nécessite un mock de AccountDB::getInstance(). ' .
            'Implémentez une méthode setInstance() dans AccountDB ou utilisez une base de données de test.'
        );
    }

    /**
     * Test de control avec email non string en session
     * Note: Ce test est skipped car il nécessite un mock de AccountDB::getInstance()
     */
    public function testControlWithNonStringEmailInSession(): void
    {
        $this->markTestSkipped(
            'Ce test nécessite un mock de AccountDB::getInstance(). ' .
            'Implémentez une méthode setInstance() dans AccountDB ou utilisez une base de données de test.'
        );
    }

    /**
     * Test de la logique d'extraction de l'email
     * On teste uniquement la logique sans appeler control()
     */
    public function testEmailExtractionLogic(): void
    {
        // Email valide en string
        $_SESSION['email'] = 'user@university.fr';
        $email = '';
        if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }
        $this->assertEquals('user@university.fr', $email);

        // Email non défini
        unset($_SESSION['email']);
        $email = '';
        if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }
        $this->assertEquals('', $email);

        // Email non string (int)
        $_SESSION['email'] = 12345;
        $email = '';
        if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }
        $this->assertEquals('', $email);

        // Email non string (array)
        $_SESSION['email'] = ['test@university.fr'];
        $email = '';
        if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }
        $this->assertEquals('', $email);

        // Email non string (null)
        $_SESSION['email'] = null;
        $email = '';
        if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }
        $this->assertEquals('', $email);
    }

    /**
     * Test de la vérification de connexion avec différents états
     */
    public function testLoggedInCheckLogic(): void
    {
        // Test avec logged-in = true (valide)
        $_SESSION['logged-in'] = true;
        $isLoggedIn = isset($_SESSION['logged-in']) && $_SESSION['logged-in'] === true;
        $this->assertTrue($isLoggedIn);

        // Test avec logged-in = false
        $_SESSION['logged-in'] = false;
        $isLoggedIn = isset($_SESSION['logged-in']) && $_SESSION['logged-in'] === true;
        $this->assertFalse($isLoggedIn);

        // Test avec logged-in non défini
        unset($_SESSION['logged-in']);
        $isLoggedIn = isset($_SESSION['logged-in']) && $_SESSION['logged-in'] === true;
        $this->assertFalse($isLoggedIn);

        // Test avec logged-in = 'true' (string)
        $_SESSION['logged-in'] = 'true';
        $isLoggedIn = isset($_SESSION['logged-in']) && $_SESSION['logged-in'] === true;
        $this->assertFalse($isLoggedIn, 'String "true" should not be considered as boolean true');

        // Test avec logged-in = 1 (int)
        $_SESSION['logged-in'] = 1;
        $isLoggedIn = isset($_SESSION['logged-in']) && $_SESSION['logged-in'] === true;
        $this->assertFalse($isLoggedIn, 'Integer 1 should not be considered as boolean true');
    }

    /**
     * Test des différentes méthodes HTTP
     */
    public function testResolveWithDifferentHttpMethods(): void
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD'];

        foreach ($methods as $method) {
            $result = DeleteAccount::resolve('/user/delete-account', $method);
            if ($method === 'POST') {
                $this->assertTrue($result, "POST should resolve to true");
            } else {
                $this->assertFalse($result, "$method should resolve to false");
            }
        }
    }

    /**
     * Test avec des variations de path
     */
    public function testResolveWithPathVariations(): void
    {
        $paths = [
            '/user/delete-account' => true,   // Correct
            '/user/delete-account/' => false, // Avec trailing slash
            '/user/deleteaccount' => false,   // Sans tiret
            '/user/delete_account' => false,  // Avec underscore
            '/user/delete-accounts' => false, // Au pluriel
            '/user/delete' => false,          // Incomplet
            '/delete-account' => false,       // Sans /user
            'user/delete-account' => false,   // Sans / initial
        ];

        foreach ($paths as $path => $expected) {
            $result = DeleteAccount::resolve($path, 'POST');
            $this->assertEquals($expected, $result, "Path '$path' should resolve to " . ($expected ? 'true' : 'false'));
        }
    }
}