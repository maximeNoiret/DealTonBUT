<?php

namespace tests\controllers\User\AccountPage;

use controllers\Trade\SeeOtherAccount\SeeOtherAccount;
use controllers\User\AccountPage\Account;
use dtu\models\TradeDB;
use models\AccountDB;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AccountTest extends TestCase
{
   private string $testEmail01;
   private string $testEmail02;

   private TradeDB $tradeDB;
   private int $testOuid01;
    protected function setUp(): void
    {
        parent::setUp();

        // Reset $_SESSION avant chaque test
        $_SESSION = [];
        $this->tradeDB = TradeDB::getInstance();
        $this->accDB = AccountDB::getInstance();
      //$this->seeOtherAccount = new SeeOtherAccount();
        // create a user
        AccountDB::getInstance()->registerAccount('testUser01', 'testUser01@example.com');
        AccountDB::getInstance()->registerAccount('testUser02', 'testUser02@exmanple.com');
        $this->testEmail01 = 'testUser01@example.com';
        $this->testEmail02 = 'testUser02@exmanple.com';
        TradeDB::getInstance()->insertOffre($this->testEmail02, 'Test Offer', 0.00, 10, '2030-12-31');

        $offer = $this->tradeDB->executeQuery(
          'SELECT ouid FROM offer WHERE owner =\''.$this->testEmail02.'\';'
        );
        $this->testOuid01 = $offer[0]['ouid'];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_SESSION = [];
        // //
        AccountDB::getInstance()->deleteUser($this->testEmail01);
        //TradeDB::getInstance()->deleteOffer($this->testOuid01);
        AccountDB::getInstance()->deleteUser($this->testEmail02);
    }

    /**
     * Test de la méthode resolve avec le bon path et méthode
     */
    public function testResolveReturnsTrueWithCorrectPathAndMethod(): void
    {
        $result = Account::resolve('/user/account', 'GET');
        $this->assertTrue($result);
    }

    /**
     * Test de la méthode resolve avec un mauvais path
     */
    public function testResolveReturnsFalseWithIncorrectPath(): void
    {
        $result = Account::resolve('/user/profile', 'GET');
        $this->assertFalse($result);
    }

    /**
     * Test de la méthode resolve avec une mauvaise méthode HTTP
     */
    public function testResolveReturnsFalseWithIncorrectMethod(): void
    {
        $result = Account::resolve('/user/account', 'POST');
        $this->assertFalse($result);
    }

    /**
     * Test de getName avec un email valide
     */
    public function testGetNameReturnsFormattedName(): void
    {
        $_SESSION['email'] = 'jean.dupont@university.fr';

        $result = \models\Account::getName();

        $this->assertEquals('Jean Dupont', $result);
    }

    /**
     * Test de getName avec un email sans point
     */
    public function testGetNameWithEmailWithoutDot(): void
    {
        $_SESSION['email'] = 'admin@university.fr';

        $result = \models\Account::getName();

        $this->assertEquals('Admin', $result);
    }

    /**
     * Test de getName avec plusieurs points dans l'email
     */
    public function testGetNameWithMultipleDots(): void
    {
        $_SESSION['email'] = 'marie.claire.martin@university.fr';

        $result = \models\Account::getName();

        $this->assertEquals('Marie Claire Martin', $result);
    }

    /**
     * Test de getName quand l'email n'est pas défini
     */
    public function testGetNameWithNoEmail(): void
    {
        // $_SESSION['email'] n'est pas défini

        $result = \models\Account::getName();

        $this->assertEquals('', $result);
    }

    /**
     * Test de getName quand l'email n'est pas une string
     */
    public function testGetNameWithNonStringEmail(): void
    {
        $_SESSION['email'] = 12345; // Non string

        $result = \models\Account::getName();

        $this->assertEquals('', $result);
    }

    /**
     * Test de getUserOffers avec des offres existantes
     * Note: Ce test nécessite une base de données de test ou un mock global
     */
    public function testGetUserOffersReturnsOffersGridWithMockData(): void
    {
        $this->markTestSkipped(
            'Ce test nécessite un mock de TradeDB::getInstance(). ' .
            'Implémentez une méthode setInstance() dans TradeDB ou utilisez une base de données de test.'
        );
    }

    /**
     * Test de getUserOffers sans offres
     * Note: Ce test nécessite une base de données de test ou un mock global
     */
    public function testGetUserOffersReturnsNoOffersMessage(): void
    {
        $this->markTestSkipped(
            'Ce test nécessite un mock de TradeDB::getInstance(). ' .
            'Implémentez une méthode setInstance() dans TradeDB ou utilisez une base de données de test.'
        );
    }

    /**
     * Test de getUserOffers sans email en session
     * Note: Ce test nécessite une base de données de test ou un mock global
     */
    public function testGetUserOffersWithNoSessionEmail(): void
    {
        $this->markTestSkipped(
            'Ce test nécessite un mock de TradeDB::getInstance(). ' .
            'Implémentez une méthode setInstance() dans TradeDB ou utilisez une base de données de test.'
        );
    }

    /**
     * Test de getUserBoughtOffers avec des offres achetées
     * Note: Ce test nécessite une base de données de test ou un mock global
     */
    public function testGetUserBoughtOffersReturnsOffersGrid(): void
    {
        $this->markTestSkipped(
            'Ce test nécessite un mock de TradeDB::getInstance(). ' .
            'Implémentez une méthode setInstance() dans TradeDB ou utilisez une base de données de test.'
        );
    }

    /**
     * Test de getUserBoughtOffers sans offres achetées
     * Note: Ce test nécessite une base de données de test ou un mock global
     */
    public function testGetUserBoughtOffersReturnsNoOffersMessage(): void
    {
        $this->markTestSkipped(
            'Ce test nécessite un mock de TradeDB::getInstance(). ' .
            'Implémentez une méthode setInstance() dans TradeDB ou utilisez une base de données de test.'
        );
    }

    /**
     * Test de control quand l'utilisateur n'est pas connecté
     */
    public function testControlShowsLoginFormWhenNotLoggedIn(): void
    {
        // L'utilisateur n'est pas connecté
        $_SESSION['logged-in'] = false;

        $account = new Account();

        ob_start();
        $account->control();
        $output = ob_get_clean();

        // Vérifie que la sortie contient du contenu (LoginFormView->render())
        $this->assertNotEmpty($output);
    }

    /**
     * Test de control quand logged-in n'est pas défini
     */
    public function testControlShowsLoginFormWhenLoggedInNotSet(): void
    {
        // $_SESSION['logged-in'] n'est pas défini

        $account = new Account();

        ob_start();
        $account->control();
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }

    /**
     * Test de control quand l'utilisateur est connecté
     */
    public function testControlShowsAccountPageWhenLoggedIn(): void
    {
        $_SESSION['logged-in'] = true;
        $_SESSION['email'] = 'user@university.fr';
        $_SESSION['username'] = 'testuser';
        $_SESSION['balance'] = 100;

        $account = new Account();

        ob_start();
        $account->control();
        $output = ob_get_clean();

        // Vérifie que la sortie contient du contenu (AccountPageView->render())
        $this->assertNotEmpty($output);
    }

    /**
     * Test des constantes de la classe
     */
    public function testClassConstants(): void
    {
        $this->assertEquals('/user/account', Account::PATH);
        $this->assertEquals('GET', Account::METH);

        $expectedStylesheets = [
            '/_assets/styles/Account.css',
            '/_assets/styles/style.css',
            '/_assets/styles/navbar.css',
            '/_assets/styles/offer.css'
        ];

        $this->assertEquals($expectedStylesheets, Account::STYLESHEET);
    }

    /**
     * Test de la logique interne de getName avec différents formats d'email
     */
    public function testGetNameLogic(): void
    {
        // Test avec email complet
        $_SESSION['email'] = 'pierre.paul.jacques@university.fr';
        $this->assertEquals('Pierre Paul Jacques', \models\Account::getName());

        // Test avec un seul mot
        $_SESSION['email'] = 'admin@university.fr';
        $this->assertEquals('Admin', \models\Account::getName());

        // Test avec chiffres
        $_SESSION['email'] = 'user123@university.fr';
        $this->assertEquals('User123', \models\Account::getName());

        // Test avec tirets (ne sont pas remplacés)
        $_SESSION['email'] = 'jean-pierre@university.fr';
        $this->assertEquals('Jean-pierre', \models\Account::getName());
    }

    /**
     * Test de getName avec des cas limites
     */
    public function testGetNameEdgeCases(): void
    {
        // Email vide
        $_SESSION['email'] = '';
        $this->assertEquals('', \models\Account::getName());

        // Email sans @
        $_SESSION['email'] = 'invalidemail';
        $result = \models\Account::getName();
        $this->assertEquals('Invalidemail', $result);

        // Email avec @ à la fin
        $_SESSION['email'] = 'test@';
        $this->assertEquals('Test', \models\Account::getName());
    }

    /**
     * Test de resolve avec des cas limites
     */
    public function testResolveEdgeCases(): void
    {
        // Path avec trailing slash
        $this->assertFalse(Account::resolve('/user/account/', 'GET'));

        // Path en majuscules
        $this->assertFalse(Account::resolve('/USER/ACCOUNT', 'GET'));

        // Méthode en minuscules
        $this->assertFalse(Account::resolve('/user/account', 'get'));

        // Path vide
        $this->assertFalse(Account::resolve('', 'GET'));

        // Méthode vide
        $this->assertFalse(Account::resolve('/user/account', ''));
    }

    /**
     * Test de control avec différents états de session
     */
    public function testControlWithVariousSessionStates(): void
    {
        // Test avec logged-in = true mais en string
        $_SESSION['logged-in'] = 'true';
        $account = new Account();
        ob_start();
        $account->control();
        $output = ob_get_clean();
        // Devrait afficher le login car 'true' (string) !== true (boolean)
        $this->assertNotEmpty($output);

        // Reset
        $_SESSION = [];

        // Test avec logged-in = 1 (int)
        $_SESSION['logged-in'] = 1;
        $account = new Account();
        ob_start();
        $account->control();
        $output = ob_get_clean();
        // Devrait afficher le login car 1 !== true (strict comparison)
        $this->assertNotEmpty($output);
    }

    function testGetOfferByOtherUserSuccessful()
    {
      $content = Account::getOfferByOtherUser($this->testEmail02);
      //$this->assertStringContainsString('<h1 class="description-text">There are no offers!</h1>', $content);
      $this->assertStringNotContainsString('<h1 class="description-text">There are no offers!</h1>', $content);
    }
    function testGetOfferByOtherUserWithNoOffers()
    {
      $content = Account::getOfferByOtherUser($this->testEmail01);
      $this->assertStringContainsString('<h1 class="description-text">There are no offers!</h1>', $content);
    }

    function testGetOfferBoughtByOtherUserWithNoOffers()
    {
      $content = Account::getOfferBoughtByOtherUser($this->testEmail01);
      $this->assertStringContainsString('<h1 class="description-text">There are no offers!</h1>', $content);
    }
    //TODO : to the unit test for getOfferBoughtByOtherUser() with the case where the user has bought offer
}