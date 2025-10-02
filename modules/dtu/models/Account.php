<?php

namespace models;
use exceptions\AccountAlreadyExists;
use exceptions\DatabaseNotInitiated;
use models\DataBase;

class Account {
  /**
   * @throws AccountAlreadyExists
   * @throws DatabaseNotInitiated
   */
  function registerAccount(
    string $username,
    string $email,
    string $password,
  ): void
  {
    DataBase::getInstance()->registerAccount(
      $username,
      $email,
      $password
    );
  }
}
