<?php

namespace core\views;

use core\views\AbstractView;

abstract class AbstractSubView extends AbstractView {
  //create the property "$sectionType", because dynamical property declaration is deprecated
  /**
   * @var string : Type of the section (<article>, <div>...)
   */
  private string $sectionType;

  /**
   * @description Construct a header for the subsection
   * @param string $sectionType : Type of the section (<article>, <div>...)
   * @param string | array<string, string> $sectionClass : .css class
   * @param string $customvalue : Name of the page in the navbar (not used)
   * @return string
   **/
  public function header(string $sectionType, string|array $sectionClass, string $customvalue = ''): string {
    // do this because phpstan does not like concat with an array ( logical lol )
    // TODO: see with maxime if it is possible to just not use AbstractView::header(), but an method specific from AbstractSubView
    $classString = is_array($sectionClass) ? implode(' ', $sectionClass) : $sectionClass;
    return '<' . $sectionType . ' class="' . $classString . '">' . "\n";
  }

  /**
   * @description Build the footer for the subsection
   * @return string
   */
  public function footer(): string {
    return '</' . $this->sectionType . '>';
  }

  /**
   * @description Construct the html of the sub view ( ex : an offer )
   * @param string $sectionType : Type of the section (<article>, <div>...)
   * @param string | array<string, string> $sectionClass : .css class
   * @return string
   **/
  function render(string $sectionType, string|array $sectionClass): string {
    $this->sectionType = $sectionType;
    return $this->header($sectionType, $sectionClass) . $this->body() . $this->footer();
  }

    /**
    * @description Construct the html of the sub view ( ex : an offer ) with a link
    * @param string $sectionType : Type of the section (<article>, <div>...)
    * @param string | array<string, string> $sectionClass : .css class
    * @param string $link : Link to put on the section
    * @return string
    **/
  function renderWithLink(string $sectionType, string|array $sectionClass, string $link): string {
    $this->sectionType = $sectionType;
    return '<a href="' . $link . '">' . $this->header($sectionType, $sectionClass) . $this->body() . $this->footer() . '</a>';
  }

  /**
   * @description Abstract method that contain the path to the corresponding .html
   * @return string
   */
  abstract function path(): string;

  /**
   * @description Abstract methode that define value for each keys in the associated .html file
   * @return array<string,mixed> : The array that contain the real value that are associated by a key
   */
  abstract function templateValues(): array;
}
