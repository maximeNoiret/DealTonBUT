<?php
namespace views\Trade\SeeOtherAccount;

use core\views\AbstractView;
use dtu\models\TradeDB;
use controllers\User\AccountPage\Account;
use models\AccountDB;
use views\Trade\Offer\Offer;
use views\User\AccountPage\AccountPageView;

class SeeOtherAccountView extends AbstractView {

  /**
   * @description Method that give the path to the corresponding .html
   * @return string the path to the .html file associated to this view
   */
  function path(): string
  {
    /*return __DIR__ . DIRECTORY_SEPARATOR . 'SeeOtherAccountTemplate.html';*/
    return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'User'.DIRECTORY_SEPARATOR.'AccountPage'.DIRECTORY_SEPARATOR.'AccountPage.html';
  }

  /**
   * @return array<mixed>|mixed[] The value of the different keys in the .html file,
   * that will be replaced by the corresponding value
   * @description Method that give the value of the different keys in the .html
   * file. The value of the keys are the information of the observed user, such
   * as his username, his email, his offers, his balance and his bought offers
   */
  function templateValues(): array
  {

    return [
      'USERNAME' =>AccountDB::getInstance()->getUserUsername($_GET['email'] ?? ''),
      'EMAIL' => $_GET['email'] ?? '',
      'USEROFFERS' => Account::getOfferByOtherUser($_GET['email'] ?? ''),
      'USERBALANCE' => AccountDB::getInstance()->getBalance($_GET['email'] ?? ''),
      'USERBOUGHTOFFERS' => Account::getOfferBoughtByOtherUser($_GET['email'] ?? ''),
      'NAME' => \models\Account::getName($_GET['email'] ?? ''),
      'CURRENT_PHOTO' => AccountDB::getInstance()->getUserProfilePicture($_GET['email'] ?? '') ?? 'account_pp.webp'
    ];
  }

  /**
   * @description Method that give the title of the page, that will be shown on the navbar
   * @return string the title of the page
   */
  function navbarText(): string
  {
    return 'Compte de ' . ($_GET['email'] ?? '');
  }

}