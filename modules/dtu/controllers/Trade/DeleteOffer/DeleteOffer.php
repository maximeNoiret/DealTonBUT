<?php

namespace controllers\Trade\DeleteOffer;

use core\controllers\Controller;
use dtu\models\TradeDB;
use models\AccountDB;

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
            return;
        }

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Location: /marketplace');
          return;
        }

        $id = (int)$_GET['id'];
        $offer = TradeDB::getInstance()->getOffer($id);

        if (!$offer) {
            header('Location: /marketplace');
          return;
        }

        /**
         * @var array<string, mixed> $offer
         */
        $owner = $offer['owner'];

        if ($owner === $_SESSION['email']) {
            $this->deleteOffer($id);
            header('Location: /marketplace');
            return;
        }

        if (AccountDB::getInstance()->isAdmin($_SESSION['email'])){
          $this->deleteOffer($id);
          header('Location: /admin');
          return;
        }

        header('Location: /marketplace');

        /*
        TradeDB::getInstance()->deleteOffer($id);
        $_SESSION ['flash_success'] = "Offre supprimée avec succès.";
        header('Location: /admin');
        return;
        */
        /*
        if ($owner !== $_SESSION['email']) {
          header('Location: /marketplace');
          return;
        }
        */

        /*
        TradeDB::getInstance()->deleteOffer($id);
        $_SESSION ['flash_success'] = "Offre supprimée avec succès.";
        header('Location: /marketplace');
        */
    }

    function deleteOffer(int $id): void
    {
      TradeDB::getInstance()->deleteOffer($id);
      $_SESSION ['flash_success'] = "Offre supprimée avec succès.";
    }

    static function resolve(string $path, string $meth): bool
    {
        return strtok($path, '?') === static::PATH && $meth === static::METH;
    }
}