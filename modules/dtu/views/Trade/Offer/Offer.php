<?php

namespace views\Trade\Offer;

use controllers\Trade\Offer\OfferController;
use core\views\AbstractSubView;

class Offer extends AbstractSubView {

  /**
   * @var string : The path to the .html file associated
   */
  const string PATH = __DIR__ . DIRECTORY_SEPARATOR . 'OfferTemplate.html';

  /**
   * @description Constructor of the Offer
   * @param array<string, string> $offerInfo : the information of the offer
   */
  function __construct(private readonly array $offerInfo) {
  }

  /**
   * @description Give the path to the .html file associated
   * @return string
   */
  function path(): string {
    return self::PATH;
  }

  /**
   * @description Define value for each keys in the associated .html file
   * @return array<string, string>
   */
  function templateValues(): array {
    return $this->offerInfo;
  }

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @return string
   */
  function navbarText(): string
  {
    return '';
  }
}
