<?php

namespace controllers\Trade\AddOffer;

use Couchbase\ViewException;
use views\Trade\AddOffer\AddOfferView;
class AddOffer{
    const string PATH = '/offre';
    const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/addOffer.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css'
    ];

  /**
   * @description Control the access to the Add Offer page
   * @return void
   */
    function control(): void
    {
      if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
        header('Location: /user/login');
      } else {
        echo (new AddOfferView(''))->render("AddOffer - DealTonBUT", self::STYLESHEET);
      }
    }

    /**
     * @description Resolve the path and method to access the Add Offer page
     * @param string $path
     * @param string $meth
     * @return bool
     */
    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}