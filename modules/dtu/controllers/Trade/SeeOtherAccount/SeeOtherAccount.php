<?php
namespace controllers\Trade\SeeOtherAccount;
use core\controllers\Controller;
use views\Trade\SeeOtherAccount\SeeOtherAccountView;
use views\User\AccountPage\AccountPageView;

class SeeOtherAccount implements Controller
{

  /**
   * @var string PATH The path to access to the page
   */
  Const string PATH = '/account/see';
  /**
   * @var string METH The method to access to the page
   */
  Const string METH = 'GET';
  /**
   * @var array<string> STYLESHEET The different stylesheet used for the page
   */
  const array STYLESHEET = [
    '/_assets/styles/Account.css',
    '/_assets/styles/style.css',
    '/_assets/styles/navbar.css',
    '/_assets/styles/offer.css',
  ];

  /**
   * Check if the path and the method correspond to the one of the page
   * @param string $path
   * @param string $meth
   * @return bool true if the path and the method correspond to the one of the page, false otherwise
   */
  static function resolve(string $path, string $meth): bool{
    return strtok($path, '?') === static::PATH && $meth === static::METH;
  }

  /**
   * @description Check if the user is logged in, if not redirect to the login
   * page, if yes check if the email in the url correspond to the email of the
   * logged-in user, if yes show the account page of the logged in user, if not
   * show the account page of the observed user
   * @return void
   */
  function control(): void
  {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      //echo (new LoginFormView())->render("Login - DealTonBUT", self::STYLESHEET);
      header('Location: /user/login');
    } else {
      if ($_GET['email'] == $_SESSION['email']) {
        echo (new AccountPageView())->render("Account - DealTonBUT", self::STYLESHEET);
        return;
      }
      echo (new SeeOtherAccountView())->render("Account - DealTonBUT", self::STYLESHEET);
    }
  }
}