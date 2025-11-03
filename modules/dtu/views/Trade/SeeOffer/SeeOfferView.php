<?php

namespace dtu\views\Trade\SeeOffer;

class SeeOfferView
{

    function path(): string {
        return self::PATH;
    }

    /**
     * @return array<string, string>
     */
    function templateValues(): array {
        return $this->offerInfo;
    }


    function navbarText(): string
    {
        return '';
    }
}