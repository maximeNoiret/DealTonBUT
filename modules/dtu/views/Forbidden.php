<?php

namespace views;

use core\views\AbstractView;

class Forbidden extends AbstractView
{

  /**
   * @inheritDoc
   */
  function path(): string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . 'Forbidden.html';
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
    return 'Forbidden';
  }

  public function showNavbar(): bool {
    return false;
  }
}