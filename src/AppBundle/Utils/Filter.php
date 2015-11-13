<?php
/**
 * Created by PhpStorm.
 * User: krasai
 * Date: 13.11.15
 * Time: 14:11
 */

namespace AppBundle\Utils;


class Filter
{

    private $keywords = array(
        "gdańsk",
        "gdansk",
        "gdynia",
        "sopot",
        "trójmiasto",
        "trojmiasto",
        "3city"
    );
    private $ticketsFrom3City = [];

    public function __construct()
    {

    }

    public function filterData (Array $tickets){
        foreach( $tickets as $ticket ) {
            $is3CityTicket = 0;
            foreach( $this->keywords as $keyword ){
                if ( stripos($ticket->title, $keyword )
                || stripos($ticket->description, $keyword) ) {
                        $is3CityTicket = 1;
                    }
            }
            if( $is3CityTicket ){ $this->ticketsFrom3City = $ticket; }
        }
    }

}