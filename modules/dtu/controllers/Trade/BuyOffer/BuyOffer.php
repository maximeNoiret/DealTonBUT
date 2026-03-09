<?php

namespace controllers\Trade\BuyOffer;

use core\controllers\Controller;
use dtu\models\TradeDB;
use models\AccountDB;

/**
 * @brief Class that control the page that allow the user to buy an offer
 */
class BuyOffer implements Controller
{
    const string PATH = '/offre/buy';
    const string METH = 'GET';


    /**
     * @description
     * Check if the user is logged in, if not redirect to the login page
     * Check if the offer id is valid, if not redirect to the marketplace
     * Check if the offer exists, if not redirect to the marketplace
     * Check if the user is not trying to buy his own offer, if yes redirect to
     * the marketplace.
     *
     * @return void
     */
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
            $_SESSION['flash_error'] = "Tu ne peux pas acheter ta propre offre.";
            header('Location: /marketplace');
            exit;
        }

        $balance = AccountDB::getInstance()->getBalance($_SESSION['email']);
        $role = $_SESSION['role'] ?? 'student';

        // Les teachers n'ont pas besoin de dépenser de DTC pour acheter une offre
        if ($role !== 'teacher' && $offer["type"] === "offer") {
            if ((float)$balance < (float)$offer['price']) {
                $_SESSION['flash_error'] = "Solde insuffisant pour acheter cette offre.";
                header('Location: /marketplace');
                exit;
            }
        }

        //tu as déjà acheté cette offre
        if (TradeDB::getInstance()->hasBoughtOffer($ouid, $_SESSION['email'])) {
            $_SESSION['flash_error'] = "Tu as déjà acheté cette offre.";
            header('Location: /marketplace');
            exit;
        }

        $success = TradeDB::getInstance()->buyOffer($_SESSION['email'], $ouid);
        if ($success) {
            $_SESSION['flash_success'] = "Offre achété !";
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