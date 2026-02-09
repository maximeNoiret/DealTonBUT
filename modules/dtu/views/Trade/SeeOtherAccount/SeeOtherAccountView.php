<?php
namespace views\Trade\SeeOtherAccount;

use core\views\AbstractView;

class SeeOtherAccountView extends AbstractView {

  private $email;

  function path(): string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . 'SeeOtherAccountTemplate.html';
  }

  function templateValues(): array
  {
    // TODO: Implement templateValues() method.
    return [
//      'USERNAME' => $_SESSION['username'] ?? '',
//      'EMAIL' => $_SESSION['email'] ?? '',
//      'USEROFFERS' => Account::getUserOffers(),
//      'USERBALANCE' => $_SESSION['balance'] ?? 0,
//      'USERBOUGHTOFFERS' => Account::getUserBoughtOffers(),
//      'NAME' => \models\Account::getName(),
      'EMAIL' => $_GET['email'] ?? '',
    ];
  }

  function navbarText(): string
  {
    return 'Compte de ' . ($_GET['email'] ?? '');
  }

}