<?php

namespace controllers;

class Login
{
    const string PATH = '/user/login';
    const string METH = 'GET';

    function control(): void
    {
        echo new \views\LoginFormView()->render();
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }

}