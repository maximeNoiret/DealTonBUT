<?php
namespace controllers\Trade\Message;

use core\controllers\Controller;
use Exception;
use models\MessageDB;
use dtu\views\Trade\ListConversations\ListConversationsView;

/**
 * @brief Class that control the page that list all the conversations of the user
 */

class ListConversations implements Controller {

    /**
     * @var string The path to access this page
     */
    public const string PATH = '/messages';

    /**
     * @description Store all the different stylesheet used
     * @var array<string> STYLESHEET
     */

    public const array STYLESHEET = [
        '/_assets/styles/style.css',
        '/_assets/styles/navbar.css',
        '/_assets/styles/ListConv.css',
    ];

    /**
     * @description
     * Check if the user is logged in, if not redirect to the login page
     * Get all the conversations of the user and sho them in the view
     * @return void
     * @throws Exception
     */

    public function control(): void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            header('Location: /user/login');
            return;
        }

        $email = is_string($_SESSION['email'] ?? null) ? $_SESSION['email'] : '';
        $conversations = MessageDB::getInstance()->getUserConversations($email);
        // Affichage de la vue avec les conversations
        $view  = new ListConversationsView();
        $view->setData($conversations, $email);
        echo $view->render("Mes Messages - DealTonBUT", static::STYLESHEET);
    }

    public static function resolve(string $path, string $meth): bool {
        return $path === self::PATH;
    }
}