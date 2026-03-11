<?php

namespace controllers\Trade\Message;
use core\controllers\Controller;
use models\MessageDB;
use views\Trade\Chat\ChatView;

/**
 * @class ChatController
 * @brief Controller responsible for managing the chat between two users.
 */

class ChatController implements Controller
{
    /**
     * @var string PATH : The path to access the chat page
     */
    public const string PATH = '/chat';
    public const string API_PATH = '/chat/updates';

    /**
     * @description Store all the different stylesheet used
     * @var array<string> STYLESHEET
     */
    public const STYLESHEET = [
        '/_assets/styles/style.css',
        '/_assets/styles/Chat.css',
        '/_assets/styles/navbar.css',
    ];

    /**
     * @description Main controller method
     * Handles the whole chat logic:
     * - Checks if the user is logged in
     * - Processes message sending (POST request)
     * - Retrieves the conversation and its messages
     * - Displays the chat interface
     * @return void
     */
    function control(): void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            header('Location: /user/login');
            return;
        }
        $dbMessage = MessageDB::getInstance();

        $session_email = is_string($_SESSION['email'] ?? null) ? $_SESSION['email'] : '';

        $requestUri = is_string($_SERVER['REQUEST_URI'] ?? null) ? $_SERVER['REQUEST_URI'] : '/';
        $currentPath = strtok($requestUri, '?');

        if ($currentPath === self::API_PATH) {
            header('Content-Type: application/json');
            $id_conv = is_numeric($_GET['id_conv'] ?? null) ? (int)$_GET['id_conv'] : 0;
            $dbMessage = MessageDB::getInstance();
            if($id_conv > 0 && $dbMessage->allowedToChat($session_email, $id_conv)) {
                $messages = $dbMessage->getMessagesByConversation($id_conv);

                $view = new ChatView();
                $view->setData($messages, $id_conv, $session_email);

                $htmlMessages = $view->templateValues()['MESSAGES'];

                echo json_encode(['html' => $htmlMessages]);
            } else {
                echo json_encode(['html' => '<p>Erreur de chargement...</p>']);
            }
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_conv = is_numeric($_POST['id_conv'] ?? null) ? (int)$_POST['id_conv'] : 0;
            /**
             * @var string $content The content of the message to send, defaulting to an empty string if not provided or not a string.
             */
            $content = is_string($_POST['content'] ?? null) ? $_POST['content'] : '';

            if (!empty($content) && !empty($id_conv) && strlen($content) <= 500 ) {
                $dbMessage->addMessage($id_conv, $session_email, $content);
            }
            $uri = is_string($_SERVER['REQUEST_URI'] ?? null) ? $_SERVER['REQUEST_URI'] : '/';
            header('Location: ' . $uri);
            exit();
        }

        $ouid = is_numeric($_GET['ouid'] ?? null) ? (int)$_GET['ouid'] : 0;
        $contact_email = is_string($_GET['email'] ?? null) ? $_GET['email'] : '';

        $messages = [];
        $id_conv = null;

        if (!empty($ouid) && !empty($contact_email)) {
            $id_conv = $dbMessage->getConversationId($contact_email, $ouid);

            if ($id_conv !== null) {
                if (!$dbMessage->allowedToChat($session_email, $id_conv)) {
                    header('Location: /marketplace');
                    exit();
                }
                $messages = $dbMessage->getMessagesByConversation($id_conv);
            }
        }

        $view = new ChatView();
        $view->setData($messages, $id_conv, $session_email);
        echo $view->render("Chat - DealTonBUT", static::STYLESHEET);
    }

    public static function resolve(string $path, string $meth): bool {
        $currentPath = strtok($path, '?');
        return ($currentPath === static::PATH || $currentPath === static::API_PATH)
            && ($meth == 'GET' || $meth == 'POST');
    }
}

