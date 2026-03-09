<?php

namespace dtu\views\User\AdminPanel;

use controllers\User\AccountPage\Account;
use core\views\AbstractView;
use models\AccountDB;

class AdminPanelView extends AbstractView
{
  /**
   * @description Method that give the path tp the corresponding .html
   * @return string
   */
  function path(): string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . 'AdminPanel.html';
  }

  /**
   * @description Define value for each keys in the associated .html file
   * @return array<string,mixed> : The array that contain the real value that are associated by a key
   */
  function templateValues(): array
  {
    $values = [
      /*'ACCOUNTS' => new AccountAdminPanelView()->render('article','account-manage')*/
      'ACCOUNTS' => AccountDB::getInstance()->getAllAccountHtml(AccountDB::getInstance()->getAllAccount())
    ];
    return $values;
  }

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @return string
   */
  function navbarText(): string {
    return 'Panneau d\'administration';
  }
}