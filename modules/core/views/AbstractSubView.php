<?php

namespace views;

use views\AbstractView;

abstract class AbstractSubView extends AbstractView {
  public function header(string $sectionType, string $sectionClass): string {
    return '<' . $sectionType . ' class="' . $sectionClass . '">' . "\n";
  }

  public function footer(): string {
    return '</' . $this->sectionType . '>';
  }

  function render(string $sectionType, string $sectionClass): string {
    $this->sectionType = $sectionType;
    return $this->header($sectionType, $sectionClass) . $this->body() . $this->footer();
  }

  abstract function path(): string;

  abstract function templateValues(): array;
}
