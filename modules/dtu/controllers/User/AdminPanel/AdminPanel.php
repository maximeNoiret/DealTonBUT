<?php

namespace controllers\User\AdminPanel;

use controllers\Trade\MarketPlace\MarketPlace;
use dtu\models\TradeDB;
use dtu\views\User\AdminPanel\AccountAdminPanel;
use dtu\views\User\AdminPanel\AdminPanelView;
use models\AccountDB;
use views\Trade\Offer\Offer;
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
    '/_assets/styles/adminPanel.css',
    '/_assets/styles/MarketPlace.css'
  ];

  /**
   * @description Check if the user is logged in, if not redirect to the login page.
   * If yes, check if the user is an admin, if not redirect to the login page,
   * if yes display the admin panel.
   * @Warning The role of the user is written in the session at the moment of the
   * login, in the LoginConfirm controller.
   * @return void
   */
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
   * Use the method getAllAccount() of AccountDB to obtain the account and their information.
   * The HTML is situated in AccountAdminPanel.html, and the CSS in adminPanel.css.
   * @return string The HTML code to display the email, role and balance of all
   * the accounts, as well as a delete button for each account.
   */
  public function getAllAccountHtml(): string {
    /**
     * @var array<int, array<string, string>> $accounts : an array of accounts,
     * where each account is an associative array with keys 'email', 'username', 'hashedpwd', 'role' and 'balance'.
     */
    $accounts = AccountDB::getInstance()->getAllAccount();
    $html = '';
    foreach ($accounts as $account) {
      $html .= (new AccountAdminPanel($account))->render('article', 'account-manage');
    }

    return $html;
  }

  /**
   * @description Generate the HTML code for the delete button of an offer in
   * the admin panel.
   * @param int $offerId The ID of the offer, will be used in the href of the
   * delete button to specify which offer to delete.
   * @return string The HTML code for the delete button of an offer in the
   * admin panel.
   */
  public function geneAdminDeleteOffer(int $offerId): string {
      return '<a class="button-delete" href="/offre/delete?id=' . $offerId . '">Delete</a>';
  }

  /**
   * @description Generate the HTML code to display all the offers in the
   * admin panel, this method is different from MarketPlace::offersHTML()
   * because it adds only a delete button for each offer.
   * @return string HTML code to display all the offers in the admin panel,
   * with a delete button for each offer.
   */
  public function genAdminOffersHtml(): string {
    [$offers, $totalOffers] = TradeDB::getOffers();
//    $offers = TradeDB::getInstance()->getAllOffer();
    $limit = 8;
    $sort = $_GET['sort'] ?? null;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    if ($offers) {
      $ret = '<section class="offer-grid" id="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        /**
         * @var array<string, string> $offer
         */
        // Add button based on ownership
        $offer['button'] = $this->geneAdminDeleteOffer($offer['ouid']);
        $isOwn = AccountDB::ownsOffer($_SESSION['email'], (int)$offer['ouid']);
        $isTeacher = ($offer['role'] ?? '') === 'teacher';
        $teacherClass = $isTeacher ? ' teacher-offer' : '';

        if ($offer['style'] === 'normal')
          $ret = $ret . new Offer($offer)->render('article', 'offer-card' . ($isOwn ? ' own-offer' : '') . $teacherClass). "\n";
        else
          $ret = $ret . new Offer($offer)->render('article', 'offer-card' . ($isOwn ? ' own-offer' : ' offer-card-' . $offer['style'] . '-theme') . $teacherClass). "\n";
      }
      $ret .= '</section>';

      // Add "Load More" button if there are more offers
      if ($totalOffers > $offset + $limit) {
        $nextOffset = $offset + $limit;
        $sortParam = $sort ? '&sort=' . urlencode($sort) : '';
        $searchParam = isset($_GET['search-string']) ? '&search-string=' . urlencode($_GET['search-string']) : '';
        $ret .= '<div class="load-more-container">';
        $ret .= '<button class="load-more-btn" onclick="loadMoreOffersAdmin(' . $nextOffset . ', \'' . htmlspecialchars($sortParam . $searchParam, ENT_QUOTES) . '\')">Plus d\'offres ?</button>';
        $ret .= '</div>';
      }

      return $ret;
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

}