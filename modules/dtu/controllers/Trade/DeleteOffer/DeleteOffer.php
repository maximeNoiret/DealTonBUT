<?php

namespace controllers\Trade\DeleteOffer;

use core\controllers\Controller;
use models\DataBase;

/**
 * Checks if the user is logged in and is the offer owner before deleting it.
 * Redirects to the login page if the user is not logged in.
 * Redirects to the marketplace page if the offer does not exist or if the user is not the owner.
 * Deletes the offer and redirects to the marketplace page if all conditions are met.
 */
class DeleteOffer implements Controller
{
    const string PATH = '/offre/delete';
    const string METH = 'GET';

    function control(): void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            header('Location: /user/login');
//            exit;
            return;
        }

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Location: /marketplace');
//            exit;
          return;
        }

        $id = (int)$_GET['id'];
        $offer = DataBase::getInstance()->getOffer($id);

        if (!$offer) {
            header('Location: /marketplace');
//            exit;
          return;
        }

        if ($offer['owner'] !== $_SESSION['email']) {
            header('Location: /marketplace');
//            exit;
          return;
        }

        DataBase::getInstance()->deleteOffer($id);
        header('Location: /marketplace');
    }

    static function resolve(string $path, string $meth): bool
    {
        return strtok($path, '?') === static::PATH && $meth === static::METH;
    }
}