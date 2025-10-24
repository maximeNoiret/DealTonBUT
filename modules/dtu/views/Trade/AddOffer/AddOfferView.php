<?php

namespace views\Trade\AddOffer;

use core\views\AbstractView;

class AddOfferView extends AbstractView{
  /**
   * @description Constructor of the class
   * @param string $offresHtml :
   */
    public function __construct(private string $offresHtml)
    {
    }

  /**
   * @description Method that give the path tp the corresponding .html
   * @return string
   */
    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'AddOffer.html';
    }

  /**
   * @description Replace keys value by their real value in the associated .html file
   * @return array|mixed[]
   */
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

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @return string
   */
  function navbarText(): string
  {
    return 'Creer une offre';
  }
}