<?php

namespace views\Legal\Confidentiality;

use core\views\AbstractView;

class ConfidentialityView extends AbstractView
{
    /**
     * @description Method that give the path tp the corresponding .html
     * @return string
     */
    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Confidentiality.html';
    }

    /**
     * @description Replace keys value by their real value in the associated .html file
     * @return string[]
     */
    function templateValues(): array {
        return [
        ];
    }

    /**
     * @description Contain the title of the page, that will be shown on the navbar
     * @return string
     */
    function navbarText(): string {
        return 'Confidentialité';
    }

  public function showNavbar(): bool {
    return false;
  }
}