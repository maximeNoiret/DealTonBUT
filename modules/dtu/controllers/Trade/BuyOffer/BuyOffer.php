<?php

namespace controllers\Trade\BuyOffer;

use core\controllers\Controller;
use dtu\models\TradeDB;
use models\AccountDB;

class BuyOffer implements Controller
{
    const string PATH = '/offre/buy';
    const string METH = 'GET';

    function control(): void
    {
        //vérification si l'utilisateur est connecté
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            $_SESSION['flash_error'] = "Connecte-toi d'abord.";
            header('Location: /user/login');
            exit;
        }

        //vérification de l'id de l'offre
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['flash_error'] = "ID de l'offre invalide.";
            header('Location: /marketplace');
            exit;
        }

        $ouid = (int) $_GET['id'];
        $offer = TradeDB::getInstance()->getOffer($ouid);
        //vérification si l'offre existe
        if (!$offer) {
            $_SESSION['flash_error'] = "L'offre n'existe plus.";
            header('Location: /marketplace');
            exit;
        }

        //vérification si l'utilisateur n'achète pas sa propre offre
        if ($offer['owner'] === $_SESSION['email']) {
            $_SESSION['flash_error'] = "Tu ne peux pas acheter ta propre offre. #BigBrain";
            header('Location: /marketplace');
            exit;
        }

        $balance = AccountDB::getInstance()->getBalance($_SESSION['email']);
        //vérification si l'utilisateur a assez dans son solde
        if ((float)$balance < (float)$offer['price']) {
            $_SESSION['flash_error'] = "Solde insuffisant pour acheter cette offre.";
            header('Location: /marketplace');
            exit;
        }

        //tu as déjà acheté cette offre
        if (TradeDB::getInstance()->hasBoughtOffer($ouid, $_SESSION['email'])) {
            $_SESSION['flash_error'] = "Tu as déjà acheté cette offre.";
            header('Location: /marketplace');
            exit;
        }

        $success = TradeDB::getInstance()->buyOffer($_SESSION['email'], $ouid);
        if ($success) {
            $_SESSION['flash_success'] = "Chat achété !";
        } else {
            $_SESSION['flash_error'] = "Erreur lors de la transaction BDD.";
        }
        header('Location: /marketplace');
        exit;
    }

    static function resolve(string $path, string $meth): bool
    {
        return strtok($path, '?') === static::PATH && $meth === static::METH;
    }

}