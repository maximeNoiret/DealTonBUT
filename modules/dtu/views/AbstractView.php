<?php

namespace views;

abstract class AbstractView {
  public function header(string $title, string $stylesheet): string {
    return '<!DOCTYPE html>
<html>
  <head>
    <title>' . $title . '</title>
    <link rel="icon" type="image/x-icon" href="/_assets/images/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/_assets/images/favicon.ico">
    <link rel="stylesheet" href="' . $stylesheet . '">
  </head>
  <body>
    <header>
      <nav>
        <a href="/">Home</a>
        <a href="/marketplace">Place de Marché</a>
        <a href="/user/logout">Deconnexion</a>
        <!-- ajoutez les liens ici les copains -->
      </nav>
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
    return $this->header($title, $stylesheet) . $this->body() . $this->footer();
  }

  static function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
      $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
  }

  abstract function path(): string;

  abstract function templateValues(): array;
}
