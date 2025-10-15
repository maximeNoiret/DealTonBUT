<?php

namespace views;

abstract class AbstractView {
  public function header(string $title, string $stylesheet, string $customvalue = ''): string {
    $navbarHtml = $this->showNavbar() ? $this->navbar($customvalue) : '';
    return '<!DOCTYPE html>
<html>
  <head>
    <title>' . $title . '</title>
    <link rel="icon" type="image/x-icon" href="/_assets/images/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/_assets/images/favicon.ico">
    <link rel="icon" type="image/png" href="/_assets/images/favicon.png">
    <link rel="stylesheet" href="' . $stylesheet . '">
  </head>
  <body>
    <header>
    ' . $navbarHtml . '
    </header>
    <main>';
  }

  public function body(): string {
    $body = file_get_contents($this->path());
    foreach ($this->templateValues() as $key => $value) {
      $body = str_replace('{' . $key . '}', $value, $body);
    }
    return $body;
  }

  public function footer(): string {
    return '</main></body></html>';
  }

  function render(string $title, string $stylesheet): string {
    return $this->header($title, $stylesheet, $this->navbarText()) . $this->body() . $this->footer();
  }

  public function navbar(string $placeholder = ''): string {
    return '
      <nav class="nav">
        <div class="nav-left">
          <p class="placeholder">' . $placeholder . '</p>
        </div>
        <div class="nav-center">
          <a class="nav-link" href="/">Home</a>
          <a class="nav-link" href="/marketplace">Place de Marché</a>
          <a class="nav-link" href="/user/logout">Deconnexion</a>
          <!-- ajoutez les liens ici les copains -->
        </div>
        <div class="nav-right">
          <img class="logo-nav" src="/_assets/images/navbarLogo.webp" alt="Logo">
        </div>
      </nav>';
  }

  static function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
      $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
  }

  abstract function path(): string;

  abstract function templateValues(): array;

  abstract function navbarText(): string;

  public function showNavbar(): bool {
    return true;
  }
}
