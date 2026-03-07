<?php

namespace controllers\User\Settings;

use core\controllers\Controller;
use models\AccountDB;

class SaveTheme implements Controller
{
    const string PATH = '/user/settings';
    const string METH = 'POST';

    function control():void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            header('Location: /user/login');
            return;
        }

        $allowed = ['normal', 'rgb', 'cat', 'space'];
        $theme = isset($_POST['theme']) && in_array($_POST['theme'], $allowed)
            ? $_POST['theme']
            : 'normal';

        $email = $_SESSION['email'] ?? '';
        AccountDB::getInstance()->setTheme($email, $theme);
        $_SESSION['theme'] = $theme;

        header('Location: /user/settings');
    }

    static function resolve(string $path, string $meth): bool {
        return $path === self::PATH && $meth === 'POST';
    }

}