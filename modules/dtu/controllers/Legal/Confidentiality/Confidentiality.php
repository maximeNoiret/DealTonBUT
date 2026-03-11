<?php

namespace controllers\Legal\Confidentiality;

use core\controllers\Controller;
use Exception;
use views\Legal\Confidentiality\ConfidentialityView;

class Confidentiality implements Controller
{
    const string PATH = '/confidentiality';
    const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css',
        '/_assets/styles/loginSingnin.css'
    ];

    /**
     * @throws Exception
     */
    function control(): void
    {
      echo new ConfidentialityView()->render("Confidentialité - DealTonBUT", self::STYLESHEET);
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}