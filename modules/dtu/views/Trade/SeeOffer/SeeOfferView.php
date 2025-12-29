<?php

namespace dtu\views\Trade\SeeOffer;

use controllers\Trade\SeeOffer\SeeOffer;
use core\views\AbstractView;

class SeeOfferView extends AbstractView
{

  const string PATH = __DIR__ . DIRECTORY_SEPARATOR . 'SeeOffer.html';

  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'SeeOffer.html';
  }

  function templateValues(): array {
    /**
     * @var array<string, string> $offer
     */
    $offer = SeeOffer::$offer;
    return [
      'username' => $offer['username'],
      'title' => $offer['title'],
      'description' => $offer['description'],
      'price' => $offer['price'],
      'deadline' => $offer['deadline'],
      'button-offer' => new SeeOffer()->buttonOffer()
    ];
  }

  function navbarText(): string
  {
    /**
     * @var string $title
     */
    $title = SeeOffer::$offer['title'] ?? 'Offre';
    return 'Offre - ' . $title;
  }
}