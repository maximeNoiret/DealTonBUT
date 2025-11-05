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

        $ouid = (int) $_GET['id'];
        $offer = DataBase::getInstance()->getOffer($ouid);

        if (!$offer) {
            header('Location: /marketplace');
            exit;
        }

        if ($offer['owner'] === $_SESSION['email']) {
            header('Location: /marketplace');
            exit;
        }

        if (!isset($_SESSION['email']) || !is_string($_SESSION['email'])) {
            header('Location: /marketplace');
            exit;
        }
        $email = trim($_SESSION['email']);
        DataBase::getInstance()->buyOffer($email, $ouid);
        header('Location: /marketplace');
    }

    static function resolve(string $path, string $meth): bool
    {
        return strtok($path, '?') === static::PATH && $meth === static::METH;
    }

}