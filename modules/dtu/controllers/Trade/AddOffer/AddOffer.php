<?php

namespace controllers\Trade\AddOffer;

use core\controllers\Controller;
use views\Trade\AddOffer\AddOfferView;

class AddOffer implements Controller {

    const string PATH = '/offre';
    const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/addOffer.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css'
    ];

    function control(): void
    {
      if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
        header('Location: /user/login');
      } else {
        echo (new AddOfferView(''))->render("AddOffer - DealTonBUT", self::STYLESHEET);
      }
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}