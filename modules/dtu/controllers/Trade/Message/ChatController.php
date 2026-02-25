<?php

namespace controllers\Trade\Message;
use core\controllers\Controller;
use models\MessageDB;
use views\Trade\Chat\ChatView;
class ChatController implements Controller
{
    public const PATH = '/chat';
    /**
     * @description Store all the different stylesheet used
     * @var array<string> STYLESHEET
     */
    public const STYLESHEET = [
        '/_assets/styles/style.css',
        '/_assets/styles/Chat.css',
        '/_assets/styles/navbar.css',
    ];

    function control(): void
    {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            header('Location: /user/login');
            return;
        }
        $dbMessage = MessageDB::getInstance();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $my_email = $_SESSION['email'] ?? '';
            $id_conv = $_POST['id_conv'] ?? '';
            $content = $_POST['content'] ?? '';

            if (!empty($content) && !empty($id_conv)) {
                $dbMessage->addMessage((int)$id_conv, $my_email, $content);
            }
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }

        $ouid = $_GET['ouid'] ?? '';
        $contact_email = $_GET['email'] ?? '';

        $messages = [];
        $id_conv = null;

        if (!empty($ouid) && !empty($contact_email)) {
            $id_conv = $dbMessage->getConversationId($contact_email, (int)$ouid);
            if ($id_conv !== null) {
                if (!$dbMessage->allowedToChat($_SESSION['email'], $id_conv)) {
                    header('Location: /marketplace');
                    exit();
                }
                $messages = $dbMessage->getMessagesByConversation($id_conv);
            }
        }
        $view = new ChatView();
        $view->setData($messages, $id_conv, $_SESSION['email']);
        echo $view->render("Chat - DealTonBUT", static::STYLESHEET);
    }
    public static function resolve(string $path, string $meth): bool {
        return strtok($path, '?') === static::PATH && ($meth == 'GET' || $meth == 'POST');
    }
}

