<?php

namespace controllers\Trade\AddOffer;

use views\Trade\AddOffer\AddOfferView;

class AddOffer{

    const string PATH = '/offre';
    const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/offre.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css'
    ];

    function control(): void
    {
        echo (new AddOfferView(''))->render("AddOffer - DealTonBUT", self::STYLESHEET);
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}