<?php

namespace controllers\Legal\TermsOfUse;

use views\Legal\TermsOfUse\TermsOfUseView;

class TermsOfUse
{
    const string PATH = '/termsofuse';
    const string METH = 'POST';

    const array STYLESHEET = [
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css'
    ];

    function control(): void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            header('Location: /user/login');
        } else {
            echo (new TermsOfUseView(''))->render("Condition d'utilisation - DealTonBUT", self::STYLESHEET);
        }
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}