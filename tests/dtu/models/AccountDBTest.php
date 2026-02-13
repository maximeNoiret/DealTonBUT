<?php

namespace dtu\models;

use models\AccountDB;
use PHPUnit\Framework\TestCase;
use core\models\DataBase;
use PDO;
use PDOStatement;

class AccountDBTest extends TestCase
{
  private AccountDB $accountDb;
  private string $testEmail01='test@testUser01.com';
  private string $testUsername01='testUser01';
  private string $testPassword01='password';
  private string $testHahsedPassword01='not-hashed-password';
  private int $testOuid01=0;
  public function setUp(): void {
    $this->accountDb = AccountDB::getInstance();
    $this->testHahsedPassword01 = password_hash('password', PASSWORD_BCRYPT);
  }
  public function testRegistersAccountSuccessfully() {
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $testQuery = 'SELECT * FROM user_ WHERE email = \''.$this->testEmail01.'\';';
    $result = $this->accountDb->executeQuery($testQuery);
    $this->assertContains($this->testUsername01, $result[0]);
  }

  public function testSetRole() {
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $result = $this->accountDb->setRole($this->testEmail01, 'student');
    $this->assertTrue($result);

    $testQuery = 'SELECT role FROM user_ WHERE email = \''.$this->testEmail01.'\';';
    $queryResult = $this->accountDb->executeQuery($testQuery);
    $this->assertEquals('student', $queryResult[0]['role']);
  }

  public function testVerifiesAccountExists() {
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);

    $result = $this->accountDb->accountExists($this->testEmail01);
    $this->assertTrue($result);
  }

  public function testGetAccountWithValidCredentials() {
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $this->accountDb->updatePassword($this->testEmail01, $this->testHahsedPassword01);
    $result = $this->accountDb->getAccount($this->testEmail01, $this->testPassword01);
    $this->assertIsArray($result);
  }

  public function testFailsToGetAccountWithInvalidPassword() {
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $this->accountDb->updatePassword($this->testEmail01, $this->testHahsedPassword01);
    $result = $this->accountDb->getAccount($this->testEmail01, 'wrong-password');
    $this->assertFalse($result);
  }

  public function testFailGetBalanceBecauseOfWrongEmail(){
    $result = $this->accountDb->getBalance('wrong.email');
    $this->assertFalse($result);
  }

  public function testGetBalanceSuccessfully(){
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $result = $this->accountDb->getBalance($this->testEmail01);
    $this->assertEquals(0, $result);
  }

  public function testUpdatePasswordSuccessfully(){
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $this->accountDb->updatePassword($this->testEmail01, $this->testHahsedPassword01);
    $result = $this->accountDb->getAccount($this->testEmail01, $this->testPassword01);
    $this->assertIsArray($result);
  }

  public function testInsertTokenSuccessfully()
  {
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $this->accountDb->insertToken($this->testEmail01, 'unit_test_token_011');

    $testQuery = 'SELECT token FROM token WHERE email = \''.$this->testEmail01.'\';';
    $result = $this->accountDb->executeQuery($testQuery);
    $this->assertEquals('unit_test_token_011', $result[0]['token']);
  }

  public function testDeleteUserSuccessfully(){
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $this->accountDb->deleteUser($this->testEmail01);
    $result = $this->accountDb->accountExists($this->testEmail01);
    $this->assertFalse($result);
  }

  public function testUpdateBalanceSuccessfully(){
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $this->accountDb->updateBalance($this->testEmail01);
    $this->assertEquals(0, $_SESSION['balance']);
  }

  public function testIsAccountVerifiedUnsuccessfully()
  {
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $result = $this->accountDb->isAccountVerified($this->testEmail01);
    $this->assertFalse($result);
  }

  public function testIsAccountVerifiedSuccessfully(){
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $query = 'UPDATE user_ SET role = \'student\' WHERE email = \''.$this->testEmail01.'\';';
    $this->accountDb->executeQuery($query);
    $result = $this->accountDb->isAccountVerified($this->testEmail01);
    $this->assertTrue($result);
  }

  public function testGetEmailFromTokenSuccessfully()
  {
    $this->accountDb->registerAccount($this->testUsername01,$this->testEmail01);
    $this->accountDb->insertToken($this->testEmail01, 'unit_test_token_012');
    $result = $this->accountDb->getEmailFromToken('unit_test_token_012');
    $this->assertEquals($this->testEmail01, $result);
  }

  public function tearDown(): void {
   $this->accountDb->deleteUser($this->testEmail01);
  }
}
