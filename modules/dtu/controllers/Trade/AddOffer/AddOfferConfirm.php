<?php

namespace controllers\Trade\AddOffer;

use core\controllers\Controller;
use dtu\models\TradeDB;
use models\AccountDB;

class AddOfferConfirm implements Controller
{
    const string PATH = '/offre/confirm';
    const string METH = 'POST';

    const string STYLESHEET = DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'style.css';


    /** @var array<string, float> Prix en DT₡ à déduire du solde du créateur selon le style choisi */
    const array STYLE_PRICES = [
        'normal'    => 0,
        'cat'       => 0.1,
        'space'     => 0.25,
        'amethyst'  => 0.5,
        'bad-apple' => 1,
    ];

    function control(): void
    {
        $title = $_POST['title'] ?? '';
        $price = $_POST['price'] ?? '';
        $end_date = is_string($_POST['end_date'] ?? null) ? (string)$_POST['end_date'] : '';
        $description = $_POST['description'] ?? '';
        $style = is_string($_POST['style'] ?? null) ? (string)$_POST['style'] : 'normal';
        $quantity = $_POST['quantity'] ?? '';

        // Les teachers ne peuvent poster que des demandes
        $role = $_SESSION['role'] ?? 'student';
        if ($role === 'teacher') {
            $type = 'request';
        } else {
            $type = $_POST['type'] ? 'request' : 'offer';
        }

        /**
         * @var string $tag
         */
        $tag = $_POST['tag'] ?? '';
        $tagsArray = !empty($tag) ? explode(',', $tag) : [];

        // Validation du style
        if (!array_key_exists($style, self::STYLE_PRICES)) {
            $style = is_string($_POST['style'] ?? null) ? $_POST['style'] : 'normal';
        }

        if (empty($title) || empty($price) || empty($end_date)) {
            $_SESSION['flash_error'] = 'Veuillez remplir tous les champs obligatoires (nom, prix, date limite).';
            header('Location: /offre');
            return;
        }
        if (!is_numeric($price) || $price < 0 || $price > 999999) {
            $_SESSION['flash_error'] = 'Le prix est invalide. Il doit être un nombre entre 0 et 999 999.';
            header('Location: /offre');
            return;
        }
        if(!is_numeric($quantity) || $quantity <= 0 || $quantity > 100) {
            $_SESSION['flash_error'] = 'La quantité est invalide. Elle doit être un entier entre 1 et 100.';
            header('Location: /offre');
            return;
        }
        if (strtotime($end_date) === false || strtotime($end_date) <= time()) {
            $_SESSION['flash_error'] = 'La date limite est invalide. Elle doit être dans le futur.';
            header('Location: /offre');
            return;
        }

        $title = is_string($title) ? $title : '';
        $description = is_string($description) ? $description : '';
        $end_date = is_string($_POST['end_date'] ?? null) ? $_POST['end_date'] : '';

        /**
         * @var array<string, string> $_SESSION
         */
        $email = $_SESSION['email'];

        // Vérifier que le créateur a assez de DT₡ pour payer le coût du style
        $styleCost = self::STYLE_PRICES[$style];
        if ($styleCost > 0) {
            $balance = AccountDB::getInstance()->getBalance($email);
            if ($balance === false || $balance === null || (float)$balance < $styleCost) {
                $_SESSION['flash_error'] = 'Solde insuffisant pour ce style d\'offre (coût : ' . $styleCost . ' DT₡).';
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
            (int)$quantity,
            $type,
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