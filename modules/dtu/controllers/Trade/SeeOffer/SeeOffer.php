<?php

namespace controllers\Trade\SeeOffer;

use core\controllers\Controller;
use dtu\models\TradeDB;
use dtu\views\Trade\SeeOffer\SeeOfferView;
use models\AccountDB;

class SeeOffer implements Controller
{
    const string PATH = '/offre/voir';
    const string METH = 'GET';

    const array STYLESHEET = [
        '/_assets/styles/offer.css',
        '/_assets/styles/seeOffer.css',
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css'
    ];

    /**
     * @var array<string, mixed> Offer details retrieved from the database.
     */
    static array $offer;
    static int $id;

    public function __construct() {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            /** @var array<string, int> $_GET */
            self::$id = (int)$_GET['id'];

            $result = TradeDB::getInstance()->getOffer(self::$id);
            if (is_array($result)) {
                /** @var array<string, mixed> $result */
                self::$offer = $result;
            } else {
                self::$offer = [];
            }
            return;
        }
        self::$offer = [];
    }

    public function isOwnerOfOffer(): bool {
        return isset($_SESSION['email']) && $_SESSION['email'] === self::$offer['owner'];
    }

    public function buttonOffer(): string {
        if ($this->isOwnerOfOffer()) {
            return '<a class="button-delete" href="/offre/delete?id=' . self::$id . '">Delete</a>';
        }

        $offer = TradeDB::getInstance()->getOffer(self::$id);
        if (!$offer) return '';

        if (TradeDB::getInstance()->isOfferBought(self::$id)) {
            return '';
        }

        /** @var array<string, mixed> $offer */
        $type = is_string($offer['type'] ?? null) ? $offer['type'] : 'offer';
        if ($type === 'request') {
            return '<a class="button-accept" href="/offre/buy?id=' . self::$id . '">Accept</a>';
        } else {
            return '<a class="button-buy" href="/offre/buy?id=' . self::$id . '">Buy</a>';
        }
    }

    function control(): void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            header('Location: /user/login');
        } else {
            echo (new SeeOfferView())->render("Offer - DealTonBUT", self::STYLESHEET);
        }
    }

    static function resolve(string $path, string $meth): bool
    {
        return strtok($path, '?') === static::PATH && $meth === static::METH;
    }
}