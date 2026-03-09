<?php

namespace controllers\User\Login;

use core\controllers\Controller;
use models\Account;
/* note these are the old use : */
use models\AccountDB;
use views\User\LoginForm\LoginFormView;

class LoginConfirm implements Controller
{
    const string PATH = '/user/login';
    const string METH = 'POST';

    const array STYLESHEET = [
        '/_assets/styles/loginSingnin.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css'
    ];

  /**
   * @description Validates user credentials and manages session state for login.
   * This method retrieves the email and password from the POST request,
   * validates them against the database, and if valid, sets the user's role in the session and redirects to the marketplace.
   * If invalid, it renders the login form with an error message.
   * @return void
   */
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

        if (isset($_SESSION['email'])) {
            $_SESSION['theme'] = AccountDB::getInstance()->getTheme($_SESSION['email']) ?? 'normal';
        }

        // if logged in
        if ($isValid) {
            // write the role of the user in a $_SESSION variable
            $_SESSION['role'] = AccountDB::getInstance()->getRole($email);

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
