<?php

namespace dtu\views\Trade\ListConversations;

use core\views\AbstractView;

/**
 * @brief Class that represent the view of the page that list all the conversations of the user
 */
class ListConversationsView extends AbstractView {

    private array $conversations = [];
    private string $my_email = '';

    /**
     * @description
     * Method that set the conversations and the email of the user.
     * That will be used to show the conversations in the page
     * @param array $conversations
     * @param string $email
     * @return void
     */
    public function setData(array $conversations, string $email): void{
        $this->conversations = $conversations;
        $this->my_email = $email;
    }

    /**
     * @description
     * Define the value of the different keys in the .html file.
     * That will be replaced by the corresponding value.
     * @return array<string,mixed>
     */
    public function templateValues(): array
    {
        $html = '';
        if(empty($this->conversations)){
            $html = '<p class="description-text"> Vous n\'avez aucune discussion en cours.</p>';
        }
        else {
            foreach ($this->conversations as $c){
                $otherName =  ($c['buyer_email'] === $this->my_email) ? $c['seller_name'] : $c['buyer_name'];
                $url = "/chat?ouid={$c['ouid']}&email=" . urlencode($c['buyer_email']);

                $html .= "
                <a href='{$url}' class='conv-item'>
                    <div class='conv-info'>
                        <span class='conv-title'>{$c['offer_title']}</span>
                        <span class='conv-user'>Discussion avec : {$otherName}</span>
                    </div>
                    <span class='arrow'>→</span>
                </a>";
            }
        }

        return ['CONVERSATIONS' => $html];
    }

    /**
     * @return string The path to the .html file associated to this view
     * @description Method that give the path to the .html file associated to this view
     */
    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'ListConversationsTemplate.html';
    }

    /**
     * @description Method that give the title of the page.
     * @return string The title of the page, that will be shown on the navbar.
     */
    function navbarText(): string {
        return 'Mes conversations';
    }

}