<?php

namespace controllers\Trade\SeeOffer;

use models\DataBase;

class DeleteOffer
{
    const string PATH = '/offre/delete';
    const string METH = 'GET';

    /**
     * Controle si l'utilisateur est connecté et propriétaire de l'offre avant de la supprimer.
     * Redirige vers la page de connexion si l'utilisateur n'est pas connecté.
     * Redirige vers la page du marketplace si l'offre n'existe pas ou si l'utilisateur n'est pas le propriétaire.
     * Supprime l'offre et redirige vers la page du marketplace si toutes les conditions sont remplies.
     */

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