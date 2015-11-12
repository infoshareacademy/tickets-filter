<?php

/**
 * Created by PhpStorm.
 * User: katban
 * Date: 12.11.15
 * Time: 16:07
 */

namespace AppBundle\Utils;

class Ticket
{
    public $title;
    public $auctionUrl;
    public $description;
    public $price;
    public $type;

    public function __construct($title, $auctionUrl, $description, $price, $type) {
        $this->title = $title;
        $this->auctionUrl = $auctionUrl;
        $this->description = $description;
        $this->price = $price;
        $this->type = $type;
    }
}