<?php

namespace controllers;

use views\User\AccountPageView;
use views\User\LoginFormView;

class Account implements Controller
{
    const string PATH = '/user/account';

    const string METH = 'GET';

    const array STYLESHEET = [
        DIRECTORY_SEPARATOR . '_asset' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'acount.css',
        DIRECTORY_SEPARATOR . '_asset' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'styles.css'
    ];

    function control(): void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            echo (new LoginFormView())->render("Login - DealTonBUT", self::STYLESHEET);
        } else {
            echo (new AccountPageView())->render("Account - DealTonBUT", self::STYLESHEET);
        }
      // TEMPORARY (just for CSS)
      //echo (new AccountPageView())->render('Account - DealTonBUT', self::STYLESHEET);
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }

}
