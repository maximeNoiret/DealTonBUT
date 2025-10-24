<?php

namespace core\views;

use Couchbase\ViewException;

abstract class AbstractView {
  /**
   * @description Build a .html header for the pages
   * @param string $title : Title of the page
   * @param array<string> $stylesheets : Array representing all the .css file used for the page
   * @param string $customvalue : Name of the page in the navbar
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
    ' . $stylesheetsHtml . '
  </head>
  <body>
    <header>
    ' . $navbarHtml . '
    </header>
    <main>';
  }

  /**
   * @description Build the html of the <body> section of a page, by taking the html in the corresponding .html file,
   * and use the method templateValue() on the keys values
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

  /**
   * @description Build the footer of the pages and return the small javascript used for the navbar
   * @return string
   */
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
   * @description Construct the html of the pages ( ex : the login page ), by using the methods header(),body() and footer()
   * @param string $title : Title of the page
   * @param array<string> $stylesheet : Array representing all the .css file used for the page
   * @return string
   *
   * @throws ViewException
   */
  function render(string $title, array $stylesheet): string {
    return $this->header($title, $stylesheet, $this->navbarText()) . $this->body() . $this->footer();
  }

  /**
   * @description Build the .html of the navbar
   * @param string $placeholder : Name of the page in the navbar
   * @return string
   */
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

  /**
   * @description Abstract method that contain the path to the corresponding .html
   * @return string
   */
  abstract function path(): string;

  /**
   * @description Abstract methode tha replace keys value by their real value in the associated .html file
   * @return array<string,mixed>
   */
  abstract function templateValues(): array;

  /**
   * @description Abstract method that contain the title of the page, that will be shown on the navbar
   * @return string
   */
  abstract function navbarText(): string;

  /**
   * @description
   * @return bool
   */
  public function showNavbar(): bool {
    return true;
  }
}
