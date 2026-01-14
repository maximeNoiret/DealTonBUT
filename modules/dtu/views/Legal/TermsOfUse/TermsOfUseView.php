<?php

namespace views\Legal\TermsOfUse;

use core\views\AbstractView;

class TermsOfUseView extends AbstractView
{
    /**
     * @description Constructor of the class
     */
    public function __construct()
    {
    }

    /**
     * @description Method that give the path tp the corresponding .html
     * @return string
     */
    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'TermsOfUse.html';
    }

    /**
     * @description Replace keys value by their real value in the associated .html file
     * @return array|mixed[]
     */
    function templateValues(): array {
        return [
        ];
    }

    /**
     * @description Contain the title of the page, that will be shown on the navbar
     * @return string
     */
    function navbarText(): string
    {
        return "Condition d'utilisation";
    }

  public function showNavbar(): bool {
    return false;
  }
}