<?php

namespace views;

class OffreView extends AbstractView{
    public function __construct(private string $offresHtml)
    {
    }

    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Offre.html';
    }

    function templateValues(): array {
        return [
            'OFFRES' => $this->offresHtml,
            'ACTION_KEY' => '/offre/confirm',
            'NAME_KEY' => 'title',
            'COUT_KEY' => 'price',
            'DATE_KEY' => 'end_date',
            'DESCRIPTION_KEY' => 'description',
            'TAG_KEY' => 'tag'
        ];
    }
}