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


    /** @var array<string, int> Prix en DT₡ à déduire du solde du créateur selon le style choisi */
    const array STYLE_PRICES = [
        'normal'    => 0,
        'cat'       => 2,
        'space'     => 4,
        'amethyst'  => 6,
        'bad-apple' => 10,
    ];

    function control(): void
    {
        $title = $_POST['title'] ?? '';
        $price = $_POST['price'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $description = $_POST['description'] ?? '';
        $style = $_POST['style'] ?? 'normal';
        /**
         * @var string $tag
         */
        $tag = $_POST['tag'] ?? '';
        $tagsArray = !empty($tag) ? explode(',', $tag) : [];

        // Validation du style
        if (!array_key_exists($style, self::STYLE_PRICES)) {
            $style = 'normal';
        }

        if (empty($title) || empty($price) || empty($end_date)) {
            header('Location: /offre');
            return;
        }
        if (!is_numeric($price) || $price <= 0 || $price > 999999) {
            header('Location: /offre');
            return;
        }
        if (strtotime($end_date) === false || strtotime($end_date) <= time()) {
            header('Location: /offre');
            return;
        }

        $title = is_string($title) ? $title : '';
        $description = is_string($description) ? $description : '';
        $end_date = is_string($end_date) ? $end_date : '';

        /**
         * @var array<string, string> $_SESSION
         */
        $email = $_SESSION['email'];

        // Vérifier que le créateur a assez de DT₡ pour payer le coût du style
        $styleCost = self::STYLE_PRICES[$style];
        if ($styleCost > 0) {
            $balance = AccountDB::getInstance()->getBalance($email);
            if ($balance === false || $balance === null || (float)$balance < $styleCost) {
                header('Location: /offre');
                return;
            }
        }

        // Déduire le coût du style du solde du créateur
        if ($styleCost > 0) {
            $currentBalance = (float) AccountDB::getInstance()->getBalance($email);
            AccountDB::getInstance()->setBalance($email, $currentBalance - $styleCost);
            AccountDB::getInstance()->updateBalance($email);
        }

        $ouid = TradeDB::getInstance()->insertOffer(
            $email,
            $title,
            (float)$price,
            $description,
            $end_date,
            $style
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