<?php

namespace controllers\Trade\BuyOffer;

use models\DataBase;

class BuyOffer
{
 const string PATH = '/offre/buy';
 const string METH = 'GET';

    function control(): void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            header('Location: /user/login');
            exit;
        }

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Location: /marketplace');
            exit;
        }

        $offer = DataBase::getInstance()->getOffer($_GET['id']);

        if (!$offer) {
            header('Location: /marketplace');
            exit;
        }

        if ($offer['owner'] === $_SESSION['email']) {
            header('Location: /marketplace');
            exit;
        }

        DataBase::getInstance()->buyOffer($_SESSION['email'],$_GET['id']);
        header('Location: /marketplace');
    }

    static function resolve(string $path, string $meth): bool
    {
        return strtok($path, '?') === static::PATH && $meth === static::METH;
    }

}