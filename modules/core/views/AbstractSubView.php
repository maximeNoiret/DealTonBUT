<?php

namespace core\views;

use core\views\AbstractView;

abstract class AbstractSubView extends AbstractView {
  //create the property "$sectionType", because dynamical property declaration is deprecated
  private string $sectionType;

  /**
   * @description construct a header for the subsection
   * @param string $sectionType
   * @param string | array<string, string> $sectionClass
   * @param string $customvalue
   * @return string
   **/
  public function header(string $sectionType, string|array $sectionClass, string $customvalue = ''): string {
    // do this because phpstan does not like concat with an array ( logical lol )
    // TODO: see with maxime if it is possible to just not use AbstractView::header(), but an method specific from AbstractSubView
    $classString = is_array($sectionClass) ? implode(' ', $sectionClass) : $sectionClass;
    return '<' . $sectionType . ' class="' . $classString . '">' . "\n";
  }

  public function footer(): string {
    return '</' . $this->sectionType . '>';
  }

  /**
   * @description construct the html of the sub view ( ex : an offer )
   * @param string $sectionType
   * @param string | array<string, string> $sectionClass
   * @return string
   **/
  function render(string $sectionType, string|array $sectionClass): string {
    $this->sectionType = $sectionType;
    return $this->header($sectionType, $sectionClass) . $this->body() . $this->footer();
  }

  function renderWithLink(string $sectionType, string|array $sectionClass, string $link): string {
    $this->sectionType = $sectionType;
    return '<a href="' . $link . '">' . $this->header($sectionType, $sectionClass) . $this->body() . $this->footer() . '</a>';
  }

  abstract function path(): string;

  /**
   * @description abstract method, of the purpose to replace the palcehoder of an html template by theier true value
   * @return array<string, string>
   **/
  abstract function templateValues(): array;
}


/* Old version of this class */
//abstract class AbstractSubView extends AbstractView {
//  public function header(string $sectionType, string $sectionClass): string {
//    return '<' . $sectionType . ' class="' . $sectionClass . '">' . "\n";
//  }
//
//  public function footer(): string {
//    return '</' . $this->sectionType . '>';
//  }
//
//  function render(string $sectionType, string $sectionClass): string {
//    $this->sectionType = $sectionType;
//    return $this->header($sectionType, $sectionClass) . $this->body() . $this->footer();
//  }
//
//  abstract function path(): string;
//
//  abstract function templateValues(): array;
//}
