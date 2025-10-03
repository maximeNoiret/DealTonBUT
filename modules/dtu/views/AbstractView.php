<?php

namespace views;

abstract class AbstractView {
  public function header(string $title, string $stylesheet): string {
    return '<!DOCTYPE html><html><head><title>' . $title . '</title><link rel="stylesheet" href="' . $stylesheet . '"></head><body>';
  }

  public function body(): string {
    $body = file_get_contents($this->path());
    foreach ($this->templateValues() as $key => $value) {
      $body = str_replace('{' . $key . '}', $value, $body);
    }
    return $body;
  }

  public function footer(): string {
    return '</body></html>';
  }

  function render(string $title, string $stylesheet): string {
    return $this->header($title, $stylesheet) . $this->body() . $this->footer();
  }

  abstract function path(): string;

  abstract function templateValues(): array;
}
