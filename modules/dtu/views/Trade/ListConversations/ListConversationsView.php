<?php

namespace dtu\views\Trade\ListConversations;

use core\views\AbstractView;

class ListConversationsView extends AbstractView {

    private array $conversations = [];
    private string $my_email = '';

    public function setData(array $conversations, string $email): void{
        $this->conversations = $conversations;
        $this->my_email = $email;
    }

    public function templateValues(): array
    {
        $html = '';
        if(empty($this->conversations)){
            $html = '<p> Vous n\'avez aucune discussion en cours.</p>';
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

    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'ListConversationsTemplate.html';
    }

    function navbarText(): string {
        return 'Mes conversations';
    }

}