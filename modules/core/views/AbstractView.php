<?php

namespace core\views;

use Couchbase\ViewException;

abstract class AbstractView {
  /**
   * @description construct a header for the pages
   * @param string $title
   * @param array<string> $stylesheets
   * @param string $customvalue
   * @return string
   **/
  public function header(string $title, array $stylesheets, string $customvalue = ''): string {
    $navbarHtml = $this->showNavbar() ? $this->navbar($customvalue) : '';

      $stylesheetsHtml = '';
      foreach ($stylesheets as $stylesheet) {
          $stylesheetsHtml .= '<link rel="stylesheet" href="' . $stylesheet . '">' . "\n";

      }
    return '<!DOCTYPE html>
<html>
  <head>
    <title>' . $title . '</title>
    <link rel="icon" type="image/x-icon" href="/_assets/images/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/_assets/images/favicon.ico">
    <link rel="icon" type="image/png" href="/_assets/images/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    ' . $stylesheetsHtml . '
  </head>
  <body>
    <header>
    ' . $navbarHtml . '
    </header>
    <main>';
  }

  /**
   * @description construct the html of the <body> section of a page
   * @return string
   *
   * @throws ViewException
   */
  public function body(): string {
    $body = file_get_contents($this->path());
    if (!$body) {
      throw new ViewException('Unable to load <body>');
    }
    foreach ($this->templateValues() as $key => $value) {
      $body = str_replace('{' . $key . '}', $value, $body);
    }
    return $body;
  }

  public function footer(): string {
    return '</main>
      <script>
        function openSidebar() {
          document.getElementById("sidebar").classList.add("active");
          document.getElementById("sidebar-overlay").classList.add("active");
        }
        
        function closeSidebar() {
          document.getElementById("sidebar").classList.remove("active");
          document.getElementById("sidebar-overlay").classList.remove("active");
        }
      </script>
    </body>
    </html>';
  }

  /**
   * @description construct the html of the pages ( ex : the login page )
   * @param string $title
   * @param array<string> $stylesheet
   * @return string
   *
   * @throws ViewException
   */
  function render(string $title, array $stylesheet): string {
    return $this->header($title, $stylesheet, $this->navbarText()) . $this->body() . $this->footer();
  }

  function navbar(string $placeholder = ''): string {
    $username = $_SESSION['username'] ?? 'NOM DE COMPTE';
    settype($username, 'string');
    $balance = $_SESSION['balance'] ?? 0.00;
    settype($balance, 'float');
    return '
      <nav class="nav">
        <div class="nav-left">
          <img class="overlay-nav" src="/_assets/images/overlayNavbar.webp" alt="Menu" onclick="openSidebar()">
        </div>
        <div class="nav-center">
          <h1 class="page-title">' . $placeholder . '</h1>
        </div>
        <div class="nav-right">
          <a href="/">
            <img class="logo-nav" src="/_assets/images/navbarLogo.webp" alt="Logo">
          </a>
        </div>
      </nav>    
      
      <!-- Pour la Sidebar -->
      <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
          <button class="close-btn" id="close-btn" onclick="closeSidebar()">
            <span></span>
            <span></span>
          </button>
        </div>
        <div class="sidebar-content">
          <a class="sidebar-link" href="/">Échanger Points</a>
          <a class="sidebar-link" href="/marketplace">Place de marché</a>
          <a class="sidebar-link" href="/offre">Ajouter une offre</a>
        </div>
        <div class="sidebar-footer">
          <div class="sidebar-user-card" onclick=\'window.location.href="/user/account"\' style="cursor:pointer;">
            <div class="sidebar-user-info">
              <div class="sidebar-user-name">' . strtoupper($username) . '</div>
              <div class="sidebar-user-points">' . number_format($balance, 2, '.', '') . ' pts</div>
            </div>
            <a href="/user/settings" class="sidebar-settings-icon">⚙</a>
          </div>
          <a class="sidebar-disconnect-btn" href="/user/logout">SE DECONNECTER</a>
        </div>
      </div>
      
      <!-- Overlay -->
      <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>';
  }

  /* clipboard :
  strtoupper(usernameTest($_SESSION['username']))
  strtoupper($_SESSION['username'] ?? 'NOM DE COMPTE')
  number_format($_SESSION['balance'] ?? 0, 2, '.', '')
  number_format($this->balanceTest($_SESSION['balance']), 2, '.', '')
  */

  /**
   * @description Print the debug objects
   * @param string|array<string> $data
   * @return void
   **/
  static function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
      $output = implode(',', $output);
    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
  }
  abstract function path(): string;

  /**
   * @description abstract method, of the purpose to contain the value that will replace the template in the html file
   * @return array<string, string>
   **/
  abstract function templateValues(): array;

  abstract function navbarText(): string;

  public function showNavbar(): bool {
    return true;
  }
}
