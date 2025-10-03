<?php

namespace controllers;

use views\LoginFormView;
class Login
{
    const string PATH = '/user/login';
    const string METH = 'GET';

    const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';

    function control(): void
    {
        echo (new LoginFormView())->render("Login - DealTonBUT", self::STYLESHEET);
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }

}