<?php

namespace dtu\models;

use dtu\models\TradeDB;
use models\AccountDB;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TradeDBTest extends TestCase
{
  private $mockPDO;
  private $tradeDB;
  private $mockAccountDB;

  protected function setUp(): void {
    parent::setUp();

    // Create mock PDO
    $this->mockPDO = $this->createMock(PDO::class);

    // Create mock AccountDB
    $this->mockAccountDB = $this->createMock(AccountDB::class);

    // Create TradeDB instance and inject mock PDO
    $this->tradeDB = $this->getMockBuilder(TradeDB::class)
      ->disableOriginalConstructor()
      ->onlyMethods([])
      ->getMock();

    // Use reflection to inject the mock PDO connection
    $reflection = new ReflectionClass($this->tradeDB);
    $property = $reflection->getProperty('dbConn');
    $property->setAccessible(true);
    $property->setValue($this->tradeDB, $this->mockPDO);
  }

  protected function tearDown(): void {
    // Reset singleton instances
    $reflection = new ReflectionClass(TradeDB::class);
    $instance = $reflection->getProperty('instance');
    $instance->setAccessible(true);
    $instance->setValue(null, null);

    $reflectionAccount = new ReflectionClass(AccountDB::class);
    $instanceAccount = $reflectionAccount->getProperty('instance');
    $instanceAccount->setAccessible(true);
    $instanceAccount->setValue(null, null);

    parent::tearDown();
  }

  public function testGetOffersWithNoParameters(): void {
    $expectedResult = [
      [
        'username' => 'john_doe',
        'title' => 'Math Tutoring',
        'description' => 'Help with calculus',
        'price' => 50.0,
        'deadline' => '2026-02-01 23:59:59'
      ]
    ];

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('execute');
    $mockStmt->expects($this->once())
      ->method('fetchAll')
      ->with(PDO::FETCH_ASSOC)
      ->willReturn($expectedResult);

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);

    $result = $this->tradeDB->getOffers('', '');

    $this->assertEquals($expectedResult, $result);
  }

  public function testGetOffersWithOrderBy(): void {
    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('execute');
    $mockStmt->expects($this->once())
      ->method('fetchAll')
      ->with(PDO::FETCH_ASSOC)
      ->willReturn([]);

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->with($this->stringContains('ORDER BY price ASC'))
      ->willReturn($mockStmt);

    $this->tradeDB->getOffers('price', 'ASC');
  }

  public function testGetOfferReturnsOfferDetails(): void {
    $ouid = 123;
    $expectedOffer = [
      'owner' => 'owner@example.com',
      'username' => 'owner_user',
      'title' => 'Physics Help',
      'description' => 'Quantum mechanics tutoring',
      'price' => 75.0,
      'deadline' => '2026-03-01 23:59:59'
    ];

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('bindValue')
      ->with('ouid', $ouid);
    $mockStmt->expects($this->once())
      ->method('execute');
    $mockStmt->expects($this->once())
      ->method('fetch')
      ->with(PDO::FETCH_ASSOC)
      ->willReturn($expectedOffer);

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);

    $result = $this->tradeDB->getOffer($ouid);

    $this->assertEquals($expectedOffer, $result);
  }

  public function testGetOfferReturnsFalseWhenNotFound(): void {
    $ouid = 999;

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('fetch')
      ->with(PDO::FETCH_ASSOC)
      ->willReturn(false);

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);

    $result = $this->tradeDB->getOffer($ouid);

    $this->assertFalse($result);
  }

  public function testBuyOfferSuccessful(): void {
    $email = 'buyer@example.com';
    $ouid = 123;
    $offerData = [
      'owner' => 'seller@example.com',
      'price' => 50.0,
      'deadline' => date('Y-m-d H:i:s', strtotime('+1 day'))
    ];

    // Mock AccountDB getInstance
    $mockAccountDB = $this->createMock(AccountDB::class);
    $mockAccountDB->expects($this->once())
      ->method('getBalance')
      ->with($email)
      ->willReturn(100);

    // Set up static mock for AccountDB::getInstance()
    $reflection = new ReflectionClass(AccountDB::class);
    $instance = $reflection->getProperty('instance');
    $instance->setAccessible(true);
    $instance->setValue(null, $mockAccountDB);

    $mockStmt1 = $this->createMock(PDOStatement::class);
    $mockStmt1->method('execute');
    $mockStmt1->method('fetch')->willReturn($offerData);

    $mockStmt2 = $this->createMock(PDOStatement::class);
    $mockStmt2->method('execute');

    $mockStmt3 = $this->createMock(PDOStatement::class);
    $mockStmt3->method('execute');

    $mockStmt4 = $this->createMock(PDOStatement::class);
    $mockStmt4->method('execute');

    $this->mockPDO->expects($this->once())
      ->method('beginTransaction');
    $this->mockPDO->expects($this->exactly(4))
      ->method('prepare')
      ->willReturnOnConsecutiveCalls($mockStmt1, $mockStmt2, $mockStmt3, $mockStmt4);
    $this->mockPDO->expects($this->once())
      ->method('commit');
    $this->mockPDO->expects($this->never())
      ->method('rollBack');

    $result = $this->tradeDB->buyOffer($email, $ouid);

    $this->assertTrue($result);
  }

  public function testBuyOfferFailsWhenOfferNotFound(): void {
    $email = 'buyer@example.com';
    $ouid = 999;

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->method('execute');
    $mockStmt->method('fetch')->willReturn(false);

    $this->mockPDO->expects($this->once())
      ->method('beginTransaction');
    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);
    $this->mockPDO->expects($this->once())
      ->method('rollBack');
    $this->mockPDO->expects($this->never())
      ->method('commit');

    $result = $this->tradeDB->buyOffer($email, $ouid);

    $this->assertFalse($result);
  }

  public function testBuyOfferFailsWhenBuyingOwnOffer(): void {
    $email = 'user@example.com';
    $ouid = 123;
    $offerData = [
      'owner' => 'user@example.com', // Same as buyer
      'price' => 50.0,
      'deadline' => date('Y-m-d H:i:s', strtotime('+1 day'))
    ];

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->method('execute');
    $mockStmt->method('fetch')->willReturn($offerData);

    $this->mockPDO->expects($this->once())
      ->method('beginTransaction');
    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);
    $this->mockPDO->expects($this->once())
      ->method('rollBack');

    $result = $this->tradeDB->buyOffer($email, $ouid);

    $this->assertFalse($result);
  }
  public function testBuyOfferRollsBackOnException(): void {
    $email = 'buyer@example.com';
    $ouid = 123;

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('execute')
      ->willThrowException(new \Exception('Database error'));

    $this->mockPDO->expects($this->once())
      ->method('beginTransaction');
    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);
    $this->mockPDO->expects($this->once())
      ->method('rollBack');
    $this->mockPDO->expects($this->never())
      ->method('commit');

    $result = $this->tradeDB->buyOffer($email, $ouid);

    $this->assertFalse($result);
  }

  public function testDeleteOfferExecutesCorrectly(): void {
    $ouid = 123;

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('bindValue')
      ->with('ouid', $ouid);
    $mockStmt->expects($this->once())
      ->method('execute');

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->with('DELETE FROM offer WHERE ouid = :ouid')
      ->willReturn($mockStmt);

    $this->tradeDB->deleteOffer($ouid);
  }

  public function testGetUserOffersReturnsArray(): void {
    $email = 'user@example.com';
    $expectedOffers = [
      [
        'ouid' => 1,
        'owner' => 'user@example.com',
        'username' => 'john_doe',
        'title' => 'Math Help',
        'description' => 'Calculus tutoring',
        'price' => 50.0,
        'deadline' => '2026-02-01 23:59:59'
      ]
    ];

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('bindValue')
      ->with('email', $email);
    $mockStmt->expects($this->once())
      ->method('execute');
    $mockStmt->expects($this->once())
      ->method('fetchAll')
      ->with(PDO::FETCH_ASSOC)
      ->willReturn($expectedOffers);

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);

    $result = $this->tradeDB->getUserOffers($email);

    $this->assertEquals($expectedOffers, $result);
  }

  public function testGetBoughtOffersReturnsArray(): void {
    $email = 'buyer@example.com';
    $expectedOffers = [
      [
        'ouid' => 1,
        'owner' => 'seller@example.com',
        'username' => 'seller_user',
        'title' => 'Physics Help',
        'description' => 'Quantum mechanics',
        'price' => 75.0,
        'deadline' => '2026-02-01 23:59:59'
      ]
    ];

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('bindValue')
      ->with('email', $email);
    $mockStmt->expects($this->once())
      ->method('execute');
    $mockStmt->expects($this->once())
      ->method('fetchAll')
      ->with(PDO::FETCH_ASSOC)
      ->willReturn($expectedOffers);

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);

    $result = $this->tradeDB->getBoughtOffers($email);

    $this->assertEquals($expectedOffers, $result);
  }

  public function testInsertOffreReturnsLastInsertId(): void {
    $userEmail = 'user@example.com';
    $title = 'Chemistry Help';
    $price = 60.0;
    $description = 'Organic chemistry tutoring';
    $deadline = '2026-03-01';
    $expectedId = 456;

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->exactly(6))
      ->method('bindValue');
    $mockStmt->expects($this->once())
      ->method('execute');

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);
    $this->mockPDO->expects($this->once())
      ->method('lastInsertId')
      ->willReturn((string) $expectedId);

    $result = $this->tradeDB->insertOffer($userEmail, $title, $price, $description, $deadline);

    $this->assertEquals($expectedId, $result);
  }

  public function testInsertTagCreatesTagAndAssociation(): void {
    $tagname = 'mathematics';
    $ouid = 123;

    $mockStmt1 = $this->createMock(PDOStatement::class);
    $mockStmt1->expects($this->once())
      ->method('bindValue')
      ->with('tagname', $tagname);
    $mockStmt1->expects($this->once())
      ->method('execute');

    $mockStmt2 = $this->createMock(PDOStatement::class);
    $mockStmt2->expects($this->exactly(2))
      ->method('bindValue')
      ->withConsecutive(
        ['ouid', $ouid],
        ['tagname', $tagname]
      );
    $mockStmt2->expects($this->once())
      ->method('execute');

    $this->mockPDO->expects($this->exactly(2))
      ->method('prepare')
      ->willReturnOnConsecutiveCalls($mockStmt1, $mockStmt2);

    $this->tradeDB->insertTag($tagname, $ouid);
  }

  public function testGetOffersWithSearchString(): void {
    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('execute');
    $mockStmt->expects($this->once())
      ->method('fetchAll')
      ->with(PDO::FETCH_ASSOC)
      ->willReturn([]);

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->with($this->stringContains('LIKE'))
      ->willReturn($mockStmt);

    $this->tradeDB->getOffers('search-string', '');
  }

  public function testGetUserOffersReturnsEmptyArrayWhenNoOffers(): void {
    $email = 'newuser@example.com';

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('fetchAll')
      ->with(PDO::FETCH_ASSOC)
      ->willReturn([]);

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);

    $result = $this->tradeDB->getUserOffers($email);

    $this->assertIsArray($result);
    $this->assertEmpty($result);
  }
}
