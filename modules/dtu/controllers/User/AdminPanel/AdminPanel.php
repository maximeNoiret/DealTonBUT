<?php

namespace controllers\User\AdminPanel;

use dtu\views\User\AdminPanel\AccountAdminPanel;
use dtu\views\User\AdminPanel\AdminPanelView;
use models\AccountDB;
use views\User\AccountPage\AccountPageView;
use views\User\LoginForm\LoginFormView;

class AdminPanel
{
  const string PATH = '/admin';

  const string METH = 'GET';

  const array STYLESHEET = [
    '/_assets/styles/Account.css',
    '/_assets/styles/style.css',
    '/_assets/styles/navbar.css',
    '/_assets/styles/offer.css',
    '/_assets/styles/adminPanel.css'
  ];

  function control():void {
    // NOTE : the moment when the role of the user is written is in the LoginConfirm controller
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true || !AccountDB::getInstance()->isAdmin($_SESSION['email'])) {
      echo (new LoginFormView())->render("Login - DealTonBUT", self::STYLESHEET);
    } else {
      echo (new AdminPanelView())->render("Account - DealTonBUT", self::STYLESHEET);
    }
  }

  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }

  /**
   * @description Return the html code to display all the accounts and their associated information.
   * @return string The HTML code to display the email, role and balance of all
   * the accounts, as well as a delete button for each account.
   */
  public function getAllAccountHtml(): string {
    /**
     * @var array<int, array<string, string>> $accounts : an array of accounts,
     * where each account is an associative array with keys 'email', 'username', 'hashedpwd', 'role' and 'balance'.
     */
    $accounts = AccountDB::getInstance()->getAllAccount();
    $html = '<section class ="manage-account-panel">'."\n";
    /*    foreach ($accounts as $account) {
          $html .='<article class="account-manage">'."\n";
          $html .='<p class="account-manage-element"><a href="/account/see?email='.$account['email'].'">'.$account['email'].'</a></p>'."\n";
          $html .='<p class="account-manage-element">'.$account['role'].'</p>'."\n";
          $html .='<p class="account-manage-element">'.$account['balance'].'</p>'."\n";
          $html .=
            '<form method="POST" action="/user/delete-account" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer définitivement votre compte ?\');">'
            .'<input type="hidden" name="remove-account" value="'.$account['email'].'"/>'."\n"
            .'<input type="submit" name="delete-email" class="Admin-button-delete" value="DELETE"/>'."\n"
            .'</form>'."\n";

    //      '<button type="submit" class="button-delete">DELETE</button>'."\n"
    //      ?email='.$account['email'].'
          $html .='</article>'."\n";
        }*/
    foreach ($accounts as $account) {
      $html .= (new AccountAdminPanel($account))->render('article', 'account-manage');
    }
    $html .='</section>'."\n";
    return $html;
  }

}