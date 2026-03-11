<?php

namespace dtu\models;

use core\models\DataBase;
use PDO;

class SubjectDB extends DataBase {

  protected static $instance;

  /**
   * @description Retrieves all subjects for a user.
   * @param string $email The email address of the user.
   * @return array
   */
  public function getSubject(string $email): array {
    $query = $this->dbConn->prepare('SELECT subject_name FROM points WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * @description ensures that the subject exists, then associates it with the user.
   * @param string $email The email address of the user.
   * @param string $subject_name The name of the subject.
   * @param float $points The new points value.
   * @return void
   */
  public function insertSubjectSafe(string $email, string $subject_name, float $points): void {
    $query1 = $this->dbConn->prepare('INSERT IGNORE INTO subject (subject_name) VALUES (:name)');
    $query1->bindValue('name', $subject_name);
    $query1->execute();

    $query2 = $this->dbConn->prepare('INSERT IGNORE INTO points (email, subject_name, points) VALUES (:email, :name, :points)');
    $query2->bindValue('email', $email);
    $query2->bindValue('name', $subject_name);
    $query2->bindValue('points', $points);
    $query2->execute();
  }

  /**
   * @description Updates the points for a specific subject of a user.
   * @param string $email The email address of the user.
   * @param float $points The new points value.
   * @param string $subject_name The name of the subject.
   * @return void
   */
  public function setPoints(string $email, float $points, string $subject_name): void {
    $query = $this->dbConn->prepare('UPDATE points SET points = :points WHERE email = :email AND subject_name = :subject_name');
    $query->bindValue('email', $email);
    $query->bindValue('points', $points);
    $query->bindValue('subject_name', $subject_name);
    $query->execute();
  }

  /**
   * @description Retrieves the points for a specific subject of a user.
   * @param string $email The email address of the user.
   * @param string $subject_name The name of the subject.
   * @return float The points for the specified subject.
   */
  public function getPoints(string $email, string $subject_name): float {
    $query = $this->dbConn->prepare('SELECT points FROM points WHERE email = :email AND subject_name = :subject_name');
    $query->bindValue('email', $email);
    $query->bindValue('subject_name', $subject_name);
    $query->execute();
    return (float) $query->fetchColumn();
  }



  /**
   * @description Transfers points between two subjects for a user.
   * @param string $email The email address of the user.
   * @param float $points The number of points to transfer.
   * @param string $from_subject The subject from which points are deducted.
   * @param string $to_subject The subject to which points are added.
   * @return void
   * @throws \Exception
   */
  public function transferPoints(string $email, float $points, string $from_subject, string $to_subject): void {
    try {
      $this->dbConn->beginTransaction();

      $query1 = $this->dbConn->prepare(
        'UPDATE points SET points = points - :points WHERE email = :email AND subject_name = :subject_name'
      );
      // Enlevage de points
      $query1->bindValue('email', $email);
      $query1->bindValue('points', $points);
      $query1->bindValue('subject_name', $from_subject);
      $query1->execute();

      $query2 = $this->dbConn->prepare(
        'UPDATE points SET points = points + :points WHERE email = :email AND subject_name = :subject_name'
      );

      // Ajout de points
      $query2->bindValue('email', $email);
      $query2->bindValue('points', $points);
      $query2->bindValue('subject_name', $to_subject);
      $query2->execute();

      $this->dbConn->commit();
    } catch (\Exception $e) {
      $this->dbConn->rollBack();
      throw $e;
    }
  }
}