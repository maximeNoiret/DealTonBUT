<?php

namespace views\User\AccountPage;

use core\views\AbstractView;
use models\DataBase;
use views\Trade\Offer\Offer;

// WARN: maybe find a way to not access a model in a view?

class AccountPageView extends AbstractView
{

    function path(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'AccountPage.html';
    }

  function getUserOffers(): string {
    /**
     * @var string $email
     */
    $email = $_SESSION['email'] ?? '';
    $offers = DataBase::getInstance()->getUserOffers($email);
    if ($offers) {
      $ret = '<section class="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        $ret = $ret . (new Offer((array)$offer))->render('article', 'offer-card');
      }
      return $ret . '</section>';
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

  private function getUserBoughtOffers(): string
  {
    /**
     * @var string $email
     */
    $email = $_SESSION['email'] ?? '';
    $offers = DataBase::getInstance()->getBoughtOffers($email);
    if ($offers) {
      $ret = '<section class="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        $ret = $ret . (new Offer((array)$offer))->render('article', 'offer-card');
      }
      return $ret . '</section>';
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

  function getName(): string
  {
    // Récupère l'email uniquement s'il s'agit bien d'une chaîne
    $email = '';
    if (isset($_SESSION['email']) && is_string($_SESSION['email'])) {
      $email = $_SESSION['email'];
    }
    // Extrait la partie locale avant le @
    $parts = explode('@', $email);
    $name = $parts[0];
    // Remplace les points par des espaces et capitalise
    $name = str_replace('.', ' ', $name);
    return ucwords($name);
  }

  /**
   * @return array<string,mixed>
   */
  function templateValues(): array
  {
    return [
        'USERNAME' => $_SESSION['username'] ?? '',
        'EMAIL' => $_SESSION['email'] ?? '',
        'USEROFFERS' => $this->getUserOffers(),
        'USERBALANCE' => $_SESSION['balance'] ?? 0,
        'USERBOUGHTOFFERS' => $this->getUserBoughtOffers(),
        'NAME' => $this->getName()
    ];
  }

  function navbarText(): string {
    return 'Mon compte';
  }
}