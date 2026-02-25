<?php

namespace controllers\Trade\AddOffer;

use core\controllers\Controller;
use dtu\models\TradeDB;
use models\AccountDB;
use views\AddOfferView;

class AddOfferConfirm implements Controller
{
    const string PATH = '/offre/confirm';
    const string METH = 'POST';

    const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';


    function control(): void
    {
//        throw "Vous avez accédé à AddOfferConfirm\n";


        $title = $_POST['title'] ?? '';
        $price = $_POST['price'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $description = $_POST['description'] ?? '';
        $quantity = $_POST['quantity'] ?? '';
        $type = $_POST['type'] ? 'request' : 'offer';

        /**
         * @var string $tag
         */
        $tag = $_POST['tag'] ?? '';
        $tagsArray = !empty($tag) ? explode(',', $tag) : [];


        if (empty($title) || empty($price) || empty($end_date) || empty($quantity) || empty($description)) {
            echo "Veuillez remplir tous les champs";
            header('Location: /offre');
            /*exit();*/
          return;
        }
        if (!is_numeric($price) || $price < 0 || $price > 999999) {
            header('Location: /offre');
           /* exit();*/
            return;
        }
        if(!is_numeric($quantity) || $quantity <= 0 || $quantity > 100) {
            header('Location: /offre');
            /*exit();*/
            return;
        }
        if (strtotime($end_date) === false || strtotime($end_date) <= time()) {
            header('Location: /offre');
            /*exit();*/
            return;
        }


        $title = is_string($title) ? $title : '';
        $description = is_string($description) ? $description : '';
        $end_date = is_string($end_date) ? $end_date : '';

        /**
         * @var array<string, string> $_SESSION
         */

        $ouid = TradeDB::getInstance()->insertOffre(
            $_SESSION['email'],
            $title,
            (float)$price,
            $description,
            $end_date,
            (int)$quantity,
            $type
        );

        if($ouid && !empty($tagsArray)) {
            foreach ($tagsArray as $tagname) {
                if(!empty($tagname)) {
                    TradeDB::getInstance()->insertTag($tagname, $ouid);
                }
            }
        }

        header('Location: /marketplace');

    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}