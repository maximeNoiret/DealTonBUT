<?php
namespace views\Trade\SeeOtherAccount;

use core\views\AbstractView;
use dtu\models\TradeDB;
use controllers\User\AccountPage\Account;
use models\AccountDB;
use views\Trade\Offer\Offer;

class SeeOtherAccountView extends AbstractView {

  /**
   * @description Method that give the path to the corresponding .html
   * @return string the path to the .html file associated to this view
   */
  function path(): string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . 'SeeOtherAccountTemplate.html';
  }

  function templateValues(): array
  {
    // update the var in $_SESSION that contain the balance value to now hold the balance of the observed user
    AccountDB::getInstance()->updateBalance($_GET['email'] ?? '');
    return [
      'USERNAME' =>AccountDB::getInstance()->getUserUsername($_GET['email'] ?? ''),
      'EMAIL' => $_GET['email'] ?? '',
      'USEROFFERS' => Account::getOfferByOtherUser($_GET['email'] ?? ''),
      'USERBALANCE' => $_SESSION['balance'] ?? 0,
      'USERBOUGHTOFFERS' => Account::getOfferBoughtByOtherUser($_GET['email'] ?? ''),
      'NAME' => \models\Account::getName($_GET['email'] ?? ''),
    ];
  }

  function navbarText(): string
  {
    return 'Compte de ' . ($_GET['email'] ?? '');
  }

}