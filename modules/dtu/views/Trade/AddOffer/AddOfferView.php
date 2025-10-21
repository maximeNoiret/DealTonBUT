<?php

namespace views\Trade\AddOffer;

use core\views\AbstractView;

class AddOfferView extends AbstractView{
    public function __construct(private string $offresHtml)
    {
    }

    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'AddOffer.html';
    }

    function templateValues(): array {
        return [
            'OFFRES' => $this->offresHtml,
            'ACTION_KEY' => '/offre/confirm',
            'NAME_KEY' => 'title',
            'COUT_KEY' => 'price',
            'DATE_KEY' => 'end_date',
            'DESCRIPTION_KEY' => 'description',
            'TAG_KEY' => 'tag'
        ];
    }

  function navbarText(): string
  {
    return 'Creer une offre';
  }
}