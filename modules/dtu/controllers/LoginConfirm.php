<?php

namespace controllers;

use controllers\Controller;
use exceptions\AccountAlreadyExists;
use models\Account;
use views\LoginFormView;

class LoginConfirm
{
    const string PATH = '/user/login';
    const string METH = 'POST';

    const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';

    function control(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $isValid = \models\Account::validateCredentials($email, $password);

        if ($isValid) {
            // Credentials are valid, proceed with login
            // For example, set session variables or redirect to a dashboard
            echo "Login successful!";
        } else {
            // Credentials are invalid, show the login form with an error message
            echo ((new \views\LoginFormView('invalid_credentials'))->render("Login - DealTonBUT", self::STYLESHEET));
        }
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }

}