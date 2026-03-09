<?php

namespace core\models;

use PDO;
use exceptions\DatabaseNotInitiated;

class DataBase {
  protected PDO $dbConn;

  protected static $instance;

  /**
   * @description Private constructor to initialize the database connection.
   * @return void
   * @throws DatabaseNotInitiated
   */
  private function __construct() {
    if (file_exists(__DIR__ . '/../../../.env')) {
      $env = parse_ini_file(__DIR__ . '/../../../.env');
    } else {
      $env['DB_HOSTNAME'] = getenv('DB_HOSTNAME');
      $env['DB_NAME']     = getenv('DB_NAME');
      $env['DB_USER']     = getenv('DB_USERNAME');
      $env['DB_PASSWORD'] = getenv('DB_PASSWORD');
    }

    /**
     * @var array<string, string> $env
     */
    // if any of the env variables aren't set, throw
    if (!isset($env['DB_HOSTNAME']) ||
      !isset($env['DB_NAME']) ||
      !isset($env['DB_USER']) ||
      !isset($env['DB_PASSWORD'])) {
      throw new DatabaseNotInitiated();
    }
    $this->dbConn = new PDO(
      'mysql:host=' . $env['DB_HOSTNAME'] .
      ';dbname=' . $env['DB_NAME'] . ';charset=utf8mb4',
      $env['DB_USER'], $env['DB_PASSWORD'],
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  }

  /**
   * @description Retrieves the singleton instance of the AccountDB class.
   */
  public static function getInstance(): static {
    if (!isset(static::$instance)) {
      static::$instance = new static();
    }
    return static::$instance;
  }

  /**
   * @description Executes a raw SQL query.
   * @param string $queryString The SQL query to execute.
   * @return array<mixed> The result set as an associative array.
   */
  public function executeQuery(string $queryString): array {
    $query = $this->dbConn->prepare($queryString);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }
}