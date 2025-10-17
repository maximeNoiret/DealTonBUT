<?php

namespace controllers;

use controllers\Controller;
use exceptions\AccountAlreadyExists;
use models\Account;
/* note these are the old use : */
//use views\User\LoginFormView;
//use views\MarketPlaceView;
use views\User\LoginForm\LoginFormView;
use views\Trade\MarketPlace\MarketPlaceView;

class LoginConfirm
{
    const string PATH = '/user/login';
    const string METH = 'POST';

    const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';

    function control(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $isValid = Account::validateCredentials($email, $password);

        // if logged in
        if ($isValid) {
            header('Location: /marketplace');
            //echo new MarketPlaceView()->render('Place de Marché - DealTonBUT', self::STYLESHEET);
        } else {
            echo ((new LoginFormView('invalid_credentials'))->render("Login - DealTonBUT", self::STYLESHEET));
        }
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }

}
