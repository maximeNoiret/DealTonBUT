<?php

namespace controllers\Trade\SeeOffer;

use models\DataBase;

class DeleteOffer
{
    const string PATH = '/offre/delete';
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

        if ($offer['owner'] !== $_SESSION['email']) {
            header('Location: /marketplace');
            exit;
        }

        DataBase::getInstance()->deleteOffer($_GET['id']);
        header('Location: /marketplace');
    }

    static function resolve(string $path, string $meth): bool
    {
        return strtok($path, '?') === static::PATH && $meth === static::METH;
    }
}