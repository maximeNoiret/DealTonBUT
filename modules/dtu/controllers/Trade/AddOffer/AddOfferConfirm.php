<?php

namespace controllers\Trade\AddOffer;

use models\DataBase;
use views\AddOfferView;

class AddOfferConfirm
{
    const string PATH = '/offre/confirm';
    const string METH = 'POST';

    const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';


    function control(): void
    {
        // Récupérer les données du formulaire
        $title = $_POST['title'] ?? '';
        $price = $_POST['price'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $description = $_POST['description'] ?? '';
        $tag = $_POST['tag'] ?? '';

        // vérification de l'offre
        if (empty($title) || empty($price) || empty($end_date) || empty($description)) {
            echo "Veuillez remplir tous les champs";
            header('Location: /offre?error=missing_fields');
            exit();
        }
        if (!is_numeric($price) || $price <= 0) {
            echo "Prix invalide";
            header('Location: /offre?error=invalid_price');
            exit();
        }
        DataBase::getInstance()->insertOffre(
            $_SESSION['email'],
            $title,
            (float)$price,
            $description,
            $end_date
        );
        header('Location: /marketplace');

    }




    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}