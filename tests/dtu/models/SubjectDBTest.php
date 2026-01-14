<?php

namespace dtu\models;

use dtu\models\SubjectDB;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SubjectDBTest extends TestCase {

  private $mockPDO;
  private $subjectDB;

  protected function setUp(): void {
    parent::setUp();

    // Create mock PDO
    $this->mockPDO = $this->createMock(PDO::class);

    // Create SubjectDB instance and inject mock PDO
    $this->subjectDB = $this->getMockBuilder(SubjectDB::class)
      ->disableOriginalConstructor()
      ->onlyMethods([])
      ->getMock();

    // Use reflection to inject the mock PDO connection
    $reflection = new ReflectionClass($this->subjectDB);
    $property = $reflection->getProperty('dbConn');
    $property->setAccessible(true);
    $property->setValue($this->subjectDB, $this->mockPDO);
  }

  protected function tearDown(): void {
    // Reset singleton instance
    $reflection = new ReflectionClass(SubjectDB::class);
    $instance = $reflection->getProperty('instance');
    $instance->setAccessible(true);
    $instance->setValue(null, null);

    parent::tearDown();
  }

  public function testGetSubjectReturnsArray(): void {
    $email = 'test@example.com';
    $expectedResult = [
      ['subject_name' => 'Mathematics'],
      ['subject_name' => 'Physics']
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
      ->willReturn($expectedResult);

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->with('SELECT subject_name FROM points WHERE email = :email')
      ->willReturn($mockStmt);

    $result = $this->subjectDB->getSubject($email);

    $this->assertEquals($expectedResult, $result);
  }

  public function testGetSubjectReturnsEmptyArrayWhenNoSubjects(): void {
    $email = 'nosubjects@example.com';

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('fetchAll')
      ->with(PDO::FETCH_ASSOC)
      ->willReturn([]);

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);

    $result = $this->subjectDB->getSubject($email);

    $this->assertIsArray($result);
    $this->assertEmpty($result);
  }

  public function testInsertSubjectSafeCreatesSubjectAndAssociation(): void {
    $email = 'test@example.com';
    $subjectName = 'Chemistry';
    $points = 85.5;

    $mockStmt1 = $this->createMock(PDOStatement::class);
    $mockStmt1->expects($this->once())
      ->method('bindValue')
      ->with('name', $subjectName);
    $mockStmt1->expects($this->once())
      ->method('execute');

    $mockStmt2 = $this->createMock(PDOStatement::class);
    $mockStmt2->expects($this->exactly(3))
      ->method('bindValue')
      ->withConsecutive(
        ['email', $email],
        ['name', $subjectName],
        ['points', $points]
      );
    $mockStmt2->expects($this->once())
      ->method('execute');

    $this->mockPDO->expects($this->exactly(2))
      ->method('prepare')
      ->willReturnOnConsecutiveCalls($mockStmt1, $mockStmt2);

    $this->subjectDB->insertSubjectSafe($email, $subjectName, $points);
  }

  public function testSetPointsUpdatesCorrectly(): void {
    $email = 'test@example.com';
    $points = 92.0;
    $subjectName = 'Biology';

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->exactly(3))
      ->method('bindValue')
      ->withConsecutive(
        ['email', $email],
        ['points', $points],
        ['subject_name', $subjectName]
      );
    $mockStmt->expects($this->once())
      ->method('execute');

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->with('UPDATE points SET points = :points WHERE email = :email AND subject_name = :subject_name')
      ->willReturn($mockStmt);

    $this->subjectDB->setPoints($email, $points, $subjectName);
  }

  public function testGetPointsReturnsFloat(): void {
    $email = 'test@example.com';
    $subjectName = 'Mathematics';
    $expectedPoints = 87.5;

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->exactly(2))
      ->method('bindValue')
      ->withConsecutive(
        ['email', $email],
        ['subject_name', $subjectName]
      );
    $mockStmt->expects($this->once())
      ->method('execute');
    $mockStmt->expects($this->once())
      ->method('fetchColumn')
      ->willReturn($expectedPoints);

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->with('SELECT points FROM points WHERE email = :email AND subject_name = :subject_name')
      ->willReturn($mockStmt);

    $result = $this->subjectDB->getPoints($email, $subjectName);

    $this->assertIsFloat($result);
    $this->assertEquals($expectedPoints, $result);
  }

  public function testGetPointsConvertsStringToFloat(): void {
    $email = 'test@example.com';
    $subjectName = 'Physics';

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('fetchColumn')
      ->willReturn('95.5');

    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);

    $result = $this->subjectDB->getPoints($email, $subjectName);

    $this->assertIsFloat($result);
    $this->assertEquals(95.5, $result);
  }

  public function testTransferPointsCommitsSuccessfully(): void {
    $email = 'test@example.com';
    $points = 10.0;
    $fromSubject = 'Mathematics';
    $toSubject = 'Physics';

    $mockStmt1 = $this->createMock(PDOStatement::class);
    $mockStmt1->expects($this->exactly(3))
      ->method('bindValue')
      ->withConsecutive(
        ['email', $email],
        ['points', $points],
        ['subject_name', $fromSubject]
      );
    $mockStmt1->expects($this->once())
      ->method('execute');

    $mockStmt2 = $this->createMock(PDOStatement::class);
    $mockStmt2->expects($this->exactly(3))
      ->method('bindValue')
      ->withConsecutive(
        ['email', $email],
        ['points', $points],
        ['subject_name', $toSubject]
      );
    $mockStmt2->expects($this->once())
      ->method('execute');

    $this->mockPDO->expects($this->once())
      ->method('beginTransaction');
    $this->mockPDO->expects($this->exactly(2))
      ->method('prepare')
      ->willReturnOnConsecutiveCalls($mockStmt1, $mockStmt2);
    $this->mockPDO->expects($this->once())
      ->method('commit');
    $this->mockPDO->expects($this->never())
      ->method('rollBack');

    $this->subjectDB->transferPoints($email, $points, $fromSubject, $toSubject);
  }

  public function testTransferPointsRollsBackOnException(): void {
    $email = 'test@example.com';
    $points = 10.0;
    $fromSubject = 'Mathematics';
    $toSubject = 'Physics';

    $mockStmt1 = $this->createMock(PDOStatement::class);
    $mockStmt1->expects($this->once())
      ->method('execute')
      ->willThrowException(new \Exception('Database error'));

    $this->mockPDO->expects($this->once())
      ->method('beginTransaction');
    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt1);
    $this->mockPDO->expects($this->once())
      ->method('rollBack');
    $this->mockPDO->expects($this->never())
      ->method('commit');

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Database error');

    $this->subjectDB->transferPoints($email, $points, $fromSubject, $toSubject);
  }

  public function testTransferPointsWithZeroPoints(): void {
    $email = 'test@example.com';
    $points = 0.0;
    $fromSubject = 'Mathematics';
    $toSubject = 'Physics';

    $mockStmt1 = $this->createMock(PDOStatement::class);
    $mockStmt2 = $this->createMock(PDOStatement::class);

    $this->mockPDO->expects($this->once())
      ->method('beginTransaction');
    $this->mockPDO->expects($this->exactly(2))
      ->method('prepare')
      ->willReturnOnConsecutiveCalls($mockStmt1, $mockStmt2);
    $this->mockPDO->expects($this->once())
      ->method('commit');

    $this->subjectDB->transferPoints($email, $points, $fromSubject, $toSubject);
  }

  public function testTransferPointsWithNegativePointsThrowsException(): void {
    $email = 'test@example.com';
    $points = -5.0;
    $fromSubject = 'Mathematics';
    $toSubject = 'Physics';

    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->expects($this->once())
      ->method('execute')
      ->willThrowException(new \Exception('Negative points'));

    $this->mockPDO->expects($this->once())
      ->method('beginTransaction');
    $this->mockPDO->expects($this->once())
      ->method('prepare')
      ->willReturn($mockStmt);
    $this->mockPDO->expects($this->once())
      ->method('rollBack');

    $this->expectException(\Exception::class);

    $this->subjectDB->transferPoints($email, $points, $fromSubject, $toSubject);
  }
}
