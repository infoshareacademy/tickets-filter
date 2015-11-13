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

    public function filterData (Array $tickets){
        $ticketsFrom3City= [];
        foreach ($tickets as $ticket) {
            $is3CityTicket = 0;
            foreach ($this->keywords as $keyword) {
                if (
                    stripos($ticket->title, $keyword) !== false
                    || stripos($ticket->description, $keyword) !== false
                ) {
                    $is3CityTicket = 1;
                }
            }
            if ($is3CityTicket) {
                array_push($ticketsFrom3City, $ticket);
            }
        }
        return $ticketsFrom3City;
    }

}