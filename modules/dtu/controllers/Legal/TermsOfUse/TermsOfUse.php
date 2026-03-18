<?php

namespace controllers\Legal\TermsOfUse;

use core\controllers\Controller;
use Exception;
use views\Legal\TermsOfUse\TermsOfUseView;

class TermsOfUse implements Controller
{
    const string PATH = '/termsofuse';
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
      echo new TermsOfUseView()->render("Condition d'utilisation - DealTonBUT", self::STYLESHEET);
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}