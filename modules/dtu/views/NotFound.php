<?php

namespace views;

use core\views\AbstractView;

class NotFound extends AbstractView
{

  /**
   * @inheritDoc
   */
  function path(): string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . 'NotFound.html';
  }

  /**
   * @inheritDoc
   */
  function templateValues(): array
  {
  return [];}

  /**
   * @inheritDoc
   */
  function navbarText(): string
  {
    return 'Not Found';
  }

  public function showNavbar(): bool {
    return false;
  }
}