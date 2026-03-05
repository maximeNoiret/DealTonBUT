<?php

namespace views\User\AccountPage;

use controllers\User\AccountPage\Account;
use core\views\AbstractView;
use models\AccountDB;
use views\Trade\Offer\Offer;

// WARN: maybe find a way to not access a model in a view?

class AccountPageView extends AbstractView
{

  /**
   * @description Method that give the path tp the corresponding .html
   * @return string
   */
    function path(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'AccountPage.html';
    }

  /**
   * @description Define value for each keys in the associated .html file
   * @return array<string,mixed> : The array that contain the real value that are associated by a key
   */
  function templateValues(): array
  {
      $photo = $_SESSION['profile_picture'] ?? 'account_pp.webp';
      if (empty($photo)) {
          $photo = 'account_pp.webp';
      }
    return [
        'USERNAME' => $_SESSION['username'] ?? '',
        'EMAIL' => $_SESSION['email'] ?? '',
        'USEROFFERS' => Account::getUserOffers(),
        'USERBALANCE' => $_SESSION['balance'] ?? 0,
        'USERBOUGHTOFFERS' => Account::getUserBoughtOffers(),
        'NAME' => \models\Account::getName(),
        'CURRENT_PHOTO' => $photo
        ];
  }

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @return string
   */
  function navbarText(): string {
    return 'Mon compte';
  }
}