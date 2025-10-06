<?php

namespace models;
use exceptions\AccountAlreadyExists;
use exceptions\DatabaseNotInitiated;
use models\DataBase;

class Account {
  /**
   * @throws AccountAlreadyExists
   */
  function registerAccount(
    string $username,
    string $email,
    string $password,
  ): void {
    DataBase::getInstance()->registerAccount(
      $username,
      $email,
      $password
    );
  }

  static function forgotPassword(string $email): string {
    // check if account exists at all
    if(!DataBase::getInstance()->accountExists($email)) {
      return 'message';
    }
    // check if account already requested password reset with alive ttl
    if (DataBase::getInstance()->alreadyForgotPassword($email)) {
      return 'already_sent';
    }


  }
}
