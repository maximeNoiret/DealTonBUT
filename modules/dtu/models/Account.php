<?php

namespace models;
use exceptions\AccountAlreadyExists;
use models\DataBase;

class Account {
  /**
   * @throws AccountAlreadyExists
   */
  function registerAccount(
    string $username,
    string $email,
    string $password,
  ): void
  {
    DataBase::openDataBase('guest', '');
    DataBase::registerAccount(
      $username,
      $email,
      $password
    );
  }
}
