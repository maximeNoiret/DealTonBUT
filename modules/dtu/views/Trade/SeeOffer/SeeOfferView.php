<?php

namespace dtu\views\Trade\SeeOffer;

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
    ];
  }

  function navbarText(): string
  {
    return 'Offre';
  }
}