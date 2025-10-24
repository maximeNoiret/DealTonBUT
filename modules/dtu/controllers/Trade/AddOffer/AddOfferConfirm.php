<?php

namespace controllers\Trade\AddOffer;

use models\DataBase;
use views\AddOfferView;

class AddOfferConfirm
{
    const string PATH = '/offre/confirm';
    const string METH = 'POST';

    const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';

  /**
   * @description Control if the Offer gived in AddOffer.php is valid and insert it in the DataBase
   * @return void
   */
    function control(): void
    {
      echo "Vous avez accédé à AddOfferConfirm\n";
        // Récupérer les données du formulaire
        $title = $_POST['title'] ?? '';
        $price = $_POST['price'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $description = $_POST['description'] ?? '';
        $tag = $_POST['tag'] ?? '';

        // vérification de l'offre
        if (empty($title) || empty($price) || empty($end_date) || empty($description)) {
            echo "Veuillez remplir tous les champs";
            header('Location: /offre');
            exit();
        }
        if (!is_numeric($price) || $price <= 0 || $price > 999999) {
            header('Location: /offre');
            exit();
        }


        $title = is_string($title) ? $title : '';
        $description = is_string($description) ? $description : '';
        $end_date = is_string($end_date) ? $end_date : '';

        /**
         * @var array<string, string> $_SESSION
         */
        DataBase::getInstance()->insertOffre(
            $_SESSION['email'],
            $title,
            (float)$price,
            $description,
            $end_date
        );
        header('Location: /marketplace');

    }



    /**
     * @description Resolve the path and method to access the Add Offer Confirm page
     * @param string $path
     * @param string $meth
     * @return bool
     */
    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}