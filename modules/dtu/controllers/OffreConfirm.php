<?php

namespace controllers;

use models\DataBase;
use views\OffreView;

class OffreConfirm
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
            header('Location: /offre?error=missing_fields');
            exit();
        }
        if (!is_numeric($price) || $price <= 0) {
            header('Location: /offre?error=invalid_price');
            exit();
        }

        DataBase::getInstance()->insertOffre(
            $_SESSION['user_email'],
            $title,
            (float)$price,
            $description,
            $end_date
        );
    }




    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}