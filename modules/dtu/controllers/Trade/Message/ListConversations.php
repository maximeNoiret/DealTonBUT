<?php
namespace controllers\Trade\Message;

use core\controllers\Controller;
use models\MessageDB;
use dtu\views\Trade\ListConversations\ListConversationsView;

class ListConversations implements Controller {

    public const PATH = '/messages';

    public const STYLESHEET = [
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css',
        '/_assets/styles/ListConv.css',
    ];

    public function control(): void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            header('Location: /user/login');
            return;
        }

        $conversations = MessageDB::getInstance()->getUserConversations($_SESSION['email']);

        $view  = new ListConversationsView();
        $view->setData($conversations, $_SESSION['email']);
        echo $view->render("Mes Messages - DealTonBUT", static::STYLESHEET);
    }

    public static function resolve(string $path, string $meth): bool {
        return $path === self::PATH;
    }
}