<?php

namespace views\Trade\MarketPlace;

use controllers\Trade\MarketPlace\MarketPlace;
use core\views\AbstractView;

class MarketPlaceView extends AbstractView {
  /**
   * @description Method that give the path tp the corresponding .html
   * @return string
   */
  function path(): string {
    return __DIR__ . DIRECTORY_SEPARATOR . 'MarketPlace.html';
  }

  /**
   * @description define value for each keys in the associated .html file
   * @return string[]
   */
    function templateValues(): array {
        $values = [
            'OFFERS' => MarketPlace::getOffers(),
            'POPUP'  => $this->getPopupHtml()
        ];
        return $values;
    }

    private function getPopupHtml(): string {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $html = '';

        // Vérification erreur
        if (isset($_SESSION['flash_error'])) {
            $msg = htmlspecialchars($_SESSION['flash_error']);
            $html .= "<div id='popup-message' class='popup-notification error'>{$msg}</div>";
            unset($_SESSION['flash_error']);
        }
        // Vérification succès
        elseif (isset($_SESSION['flash_success'])) {
            $msg = htmlspecialchars($_SESSION['flash_success']);
            $html .= "<div id='popup-message' class='popup-notification success'>{$msg}</div>";
            unset($_SESSION['flash_success']);
        }
        return $html;
    }

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @return string
   */
  function navbarText(): string {
    return 'Le BUTin';
  }
}
