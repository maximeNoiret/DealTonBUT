<?php

namespace views\Trade\Chat;

use core\views\AbstractView;

/**
 * @brief
 * The view for the chat page.
 */
class ChatView extends AbstractView
{
    private array $messages = [];
    private ?int $id_conv = null;
    private string $my_email = '';

    // Cette méthode permet au contrôleur de donner les données à la vue

    /**
     * @description Set the data for the view, that will be used to fill the template
     * @param array $messages The messages of the conversation
     * @param int|null $id_conv The id of the conversation
     * @param string $my_email The email of the current user
     * @return void
     */
    public function setData(array $messages, ?int $id_conv, string $my_email): void {
        $this->messages = $messages;
        $this->id_conv = $id_conv;
        $this->my_email = $my_email;
    }

    /**
     * @description Give the path to the .html file associated to this view
     * @return string The path to the .html file associated to this view
     */
    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'ChatTemplate.html';
    }

    /**
     * @description
     * Define the value of the different keys in the .html file.
     * That will be replaced by the corresponding value
     * @return array<string,mixed>
     */
    function templateValues(): array {
        $messagesHtml = '';

        if (empty($this->messages)) {
            $messagesHtml = '<p class="no-msg">Aucun message. Lancez la discussion !</p>';
        } else {
            foreach ($this->messages as $msg) {
                // On vérifie si c'est moi qui ai envoyé le message
                $isMe = ($msg['email'] === $this->my_email);
                $class = $isMe ? 'msg-me' : 'msg-other';

                $messagesHtml .= '
                <div class="message-wrapper ' . $class . '">
                    <div class="message-bubble">
                        <span class="sender-name">' . htmlspecialchars($msg['username']) . '</span>
                        <p class="text">' . htmlspecialchars($msg['content']) . '</p>
                        <span class="date">' . date('H:i', strtotime($msg['date_msg'])) . '</span>
                    </div>
                </div>';
            }
        }
        return [
            'MESSAGES' => $messagesHtml,
            'ID_CONV'  => (string)$this->id_conv,
            'DISPLAY_FORM' => $this->id_conv ? 'flex' : 'none' // Pour cacher le form si pas de conv
        ];
    }

    /**
     * @return string The title of the page, that will be shown on the navbar
     */
    function navbarText(): string {
        return 'Chat';
    }

}