<?php

namespace views\User\AccountPage;

use views\AbstractView;
use models\DataBase;
use views\Offer;

// WARN: maybe find a way to not access a model in a view?

class AccountPageView extends AbstractView
{

    function path(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'AccountPage.html';
    }

  function getUserOffers(): string {
    $offers = DataBase::getInstance()->getUserOffers($_SESSION['email']);
    if ($offers) {
      $ret = '<section class="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        $ret = $ret . (new Offer($offer))->render('article', 'offer-card');
      }
      return $ret . '</section>';
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

  private function getUserBoughtOffers(): string
  {
    $offers = DataBase::getInstance()->getBoughtOffers($_SESSION['email']);
    if ($offers) {
      $ret = '<section class="offer-grid">' . "\n";
      foreach ($offers as $offer) {
        $ret = $ret . (new Offer($offer))->render('article', 'offer-card');
      }
      return $ret . '</section>';
    }
    return '<h1 class="description-text">There are no offers!</h1>';
  }

  function getName(): string
  {
    $email = $_SESSION['email'];
    $name = explode('@', $email)[0];
    $name = str_replace('.', ' ', $name);
    return ucwords($name);
  }

  function templateValues(): array
  {
      $values = [
          'USERNAME' => $_SESSION['username'],
          'EMAIL' => $_SESSION['email'],
          'USEROFFERS' => $this->getUserOffers(),
          'USERBALANCE' => $_SESSION['balance'],
          'USERBOUGHTOFFERS' => $this->getUserBoughtOffers(),
          'NAME' => $this->getName()
      ];
      return $values;
  }
}