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
      echo "Vous avez accédé à AddOfferConfirm";
        // Récupérer les données du formulaire
        $title = $_POST['title'] ?? '';
        $price = $_POST['price'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $description = $_POST['description'] ?? '';
        $tag = $_POST['tag'] ?? '';

        echo "Données reçues : ";
        echo "Title: $title, Price: $price, End Date: $end_date, Description: $description, Tag: $tag \n";

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

        echo "Validation des données réussie.\n";
        echo "Insertion de l'offre dans la base de données...\n";
        DataBase::getInstance()->insertOffre(
            $_SESSION['user_email'],
            $title,
            (float)$price,
            $description,
            $end_date
        );
        echo "Offre insérée dans la base de données.\n";

    }




    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}