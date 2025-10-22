<?php

namespace core\views;

abstract class AbstractView {
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

  public function body(): string {
    $body = file_get_contents($this->path());
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

  function render(string $title, array $stylesheet): string {
    return $this->header($title, $stylesheet, $this->navbarText()) . $this->body() . $this->footer();
  }

  function navbar(string $placeholder = ''): string {
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
              <div class="sidebar-user-name">' . strtoupper($_SESSION['username'] ?? 'NOM DE COMPTE') . '</div>
              <div class="sidebar-user-points">' . number_format($_SESSION['balance'] ?? 0, 2, '.', '') . ' pts</div>
            </div>
            <a href="/user/settings" class="sidebar-settings-icon">⚙</a>
          </div>
          <a class="sidebar-disconnect-btn" href="/user/logout">SE DECONNECTER</a>
        </div>
      </div>
      
      <!-- Overlay -->
      <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>';
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
