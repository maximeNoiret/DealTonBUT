<?php

namespace controllers\User\Login;

use core\controllers\Controller;
use dtu\models\TradeDB;
use exceptions\AccountAlreadyExists;
use models\Account;
/* note these are the old use : */
//use views\User\LoginFormView;
//use views\MarketPlaceView;
use models\AccountDB;
use views\User\LoginForm\LoginFormView;
use views\Trade\MarketPlace\MarketPlaceView;

class LoginConfirm implements Controller
{
    const string PATH = '/user/login';
    const string METH = 'POST';

    const array STYLESHEET = [
      '/_assets/styles/loginSingnin.css',
      '/_assets/styles/style.css',
      '/_assets/styles/navbar.css'
    ];

    function control(): void
    {
        /**
        * @var string $email
        */
        $email = $_POST['email'] ?? '';
        /**
        * @var string $password
        */
        $password = $_POST['password'] ?? '';

        $isValid = Account::validateCredentials($email, $password);

        // if logged in
        if ($isValid) {
            // write in $_SESSION if the user is an admin or a user
            if (AccountDB::getInstance()->isAdmin($email))
                $_SESSION['role'] = 'admin';
            else
              $_SESSION['role'] = 'user';

            header('Location: /marketplace');
        } else {
            echo ((new LoginFormView('invalid_credentials'))->render("Login - DealTonBUT", self::STYLESHEET));
        }
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }

}
