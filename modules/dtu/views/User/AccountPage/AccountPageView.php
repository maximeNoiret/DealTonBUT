<?php

namespace views\User\AccountPage;

use core\views\AbstractView;
use models\DataBase;
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
   * @description Show the offer made by the user
   * @return string
   */
  function getUserOffers(): string {
    /**
     * @var string $email : The email of the user
     */
    $email = $_SESSION['email'] ?? '';
    $offers = DataBase::getInstance()->getUserOffers($email);
    if ($offers) {
      $ret = '<section class="offer-grid">' . "\n";
      foreach ($offers as $offer) {
          /**
           * @var array<string, string> $offer
           */
        $ret = $ret . (new Offer($offer))->renderWithLink('article', 'offer-card', '/offre/voir?id=' . $offer['ouid']);
      }
      return $ret . '</section>';
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

  /**
   * @description Show the offer brought by the user
   * @return string
   */
  private function getUserBoughtOffers(): string
  {
    /**
     * @var string $email : The email of the user
     */
    $email = $_SESSION['email'] ?? '';
    $offers = DataBase::getInstance()->getBoughtOffers($email);
    if ($offers) {
      $ret = '<section class="offer-grid">' . "\n";
      foreach ($offers as $offer) {
          /**
           * @var array<string, string> $offer
           */
        $ret = $ret . (new Offer($offer))->renderWithLink('article', 'offer-card', '/offre/voir?id=' . $offer['ouid']);
      }
      return $ret . '</section>';
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

  /**
   * @description Show the name of the user, by using their university email
   * @return string
   */
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
   * @description Define value for each keys in the associated .html file
   * @return array<string,mixed> : The array that contain the real value that are associated by a key
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

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @return string
   */
  function navbarText(): string {
    return 'Mon compte';
  }
}