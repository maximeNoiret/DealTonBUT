<?php

namespace models;
use models\DataBase;

class Account {
  function registerAccount(
    string $username,
    string $email,
    string $password,
  ) {
    DataBase::openDataBase('guest', '');
    DataBase::registerAccount(
      $username,
      $email,
      $password
    );
  }
}
