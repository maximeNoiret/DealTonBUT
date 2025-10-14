<?php

namespace views;

use views\AbstractView;

class AccountPageView extends AbstractView
{

    function path(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'AccountPage.html';
    }

    function templateValues(): array
    {
        //TODO: Add values (maybe)
        $values = [
        ];
        return $values;
    }
}