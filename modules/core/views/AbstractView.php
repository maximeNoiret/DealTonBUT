<?php

namespace core\views;

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
          $stylesheetsHtml .= '<link rel="stylesheet" type="text/css" href="' . $stylesheet . '">' . "\n";

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
   * @description Build the html of the <body> section of a page, by taking the html in the corresponding .html file,
   * and use the method templateValue() on the keys values
   * @return string
   */
  public function body(): string {
    $body = file_get_contents($this->path());
//    if (!$body) {
//      throw new ViewException('Unable to load <body>');
//    }
    /**
     * @var string $value
     */
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
      <footer class="site-footer">
        <div class="footer-content">
          <div class="footer-links">
            <a href="/confidentiality" class="footer-link">Politique de confidentialité</a>
            <span class="footer-separator">|</span>
            <a href="/termsofuse" class="footer-link">Conditions d\'utilisation</a>
          </div>
          <div class="footer-copyright">
            &copy; ' . date('Y') . ' DealTonBUT - Tous droits réservés
          </div>
        </div>
      </footer>
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
        $balance = $_SESSION['balance'] ?? 0.00;

        return '
    <nav class="nav">
        <div class="nav-left">
            <img class="overlay-nav" src="/_assets/images/overlayNavbar.webp" alt="Menu" onclick="openSidebar()">
        </div>
        <div class="nav-center">
            <h1 class="page-title">' . htmlspecialchars($placeholder) . '</h1>
        </div>
        <div class="nav-right">
            <a href="/"><img class="logo-nav" src="/_assets/images/navbarLogo.webp" alt="Logo"></a>
        </div>
    </nav>      
    
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="close-btn" onclick="closeSidebar()">X</button>
        </div>

        <div class="sidebar-content">
            <a class="sidebar-link" href="/marketplace">🛒 Place de marché</a>
            <a class="sidebar-link" href="/offre">➕ Ajouter une offre</a>
        </div>

        <div class="sidebar-footer">
            
            <div class="user-info-block">
                <div class="user-text">
                    <span class="username">' . strtoupper(htmlspecialchars($username)) . '</span>
                    <a href="/user/account" class="profile-link">Voir mon profil</a>
                </div>
                <a href="/user/settings" class="settings-icon">
                    <img
                        class="icon_parameter" src="/_assets/images/icon_parameter.webp" alt="Paramètres"
                    >
                </a>
            </div>
            <div class="balance-card">
                <small>Mon solde</small>
                <div class="balance-value">' . number_format($balance, 2, '.', '') . ' DTȻ</div>
                <a href="/trade/points" class="exchange-link">➔ ÉCHANGER MES POINTS</a>
            </div>
            
            <a class="btn-logout" href="/user/logout">SE DÉCONNECTER</a>
        </div>
    </div>
    
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>';
    }

  /**
   * @description Abstract method that contain the path to the corresponding .html
   * @return string
   */
  abstract function path(): string;

  /**
   * @description Abstract methode that define value for each keys in the associated .html file
   * @return array<mixed> : The array that contain the real value that are associated by a key
   */
  abstract function templateValues(): array;

  /**
   * @description Abstract method that contain the title of the page, that will be shown on the navbar
   * @return string
   */
  abstract function navbarText(): string;

  /**
   * @description Toggle that show the navbar
   * @return bool
   */
  public function showNavbar(): bool {
    return true;
  }
}
