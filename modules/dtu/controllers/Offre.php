<?php

namespace controllers;

use views\OffreView;

class Offre{

    const string PATH = '/offre';
    const string METH = 'GET';

    const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'offre.css';

    function control(): void
    {
        echo (new OffreView(''))->render("Offre - DealTonBUT", self::STYLESHEET);
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}