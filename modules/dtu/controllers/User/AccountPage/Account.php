<?php

namespace controllers\User\AccountPage;

use core\controllers\Controller;
use views\User\AccountPage\AccountPageView;
use views\User\LoginForm\LoginFormView;

class Account implements Controller
{
    const string PATH = '/user/account';

    const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/Account.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css',
        '/_assets/styles/offer.css'
    ];

    /**
     * Control the account page view rendering based on user login status.
     * @return void
     */
    function control(): void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            echo (new LoginFormView())->render("Login - DealTonBUT", self::STYLESHEET);
        } else {
            echo (new AccountPageView())->render("Account - DealTonBUT", self::STYLESHEET);
        }
    }

    /**
     * @description Resolve the path and method to access the Account page
     * @param string $path
     * @param string $meth
     * @return bool
     */
    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }

}
