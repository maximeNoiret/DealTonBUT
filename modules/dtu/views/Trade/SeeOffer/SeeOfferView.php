<?php

namespace dtu\views\Trade\SeeOffer;

use controllers\Trade\SeeOffer\SeeOffer;
use core\views\AbstractView;
use models\DataBase;

class SeeOfferView extends AbstractView
{

  const string PATH = __DIR__ . DIRECTORY_SEPARATOR . 'SeeOffer.html';

  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'SeeOffer.html';
  }

  function templateValues(): array {
    return [
      'username' => SeeOffer::$offer['owner'],
      'title' => SeeOffer::$offer['title'],
      'description' => SeeOffer::$offer['description'],
      'price' => SeeOffer::$offer['price'],
      'deadline' => SeeOffer::$offer['deadline'],
      'button-offer' => SeeOffer::class->buttonOffer()
    ];
  }

  function navbarText(): string
  {
    return 'Offre - ' . SeeOffer::$offer['title'];
  }
}