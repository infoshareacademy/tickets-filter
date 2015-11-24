<?php
/**
 * Created by PhpStorm.
 * User: krasai
 * Date: 19.11.15
 * Time: 16:34
 */

namespace AppBundle\Utils;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;

class UpdateTable extends Event
{

    public function __construct(){

      return  $this->getData();

    }

    private function getData(){
        $result = $this->callApi('GET', 'http://test.tickets-collector.infoshareaca.nazwa.pl/web/index.php/tickets');
        $dateFromRest = json_decode($result);
        if ($dateFromRest == null) {
            return new JsonResponse(['error' => 'Tickets not found']);
        } else {
            echo('asd');
            $filter = new Filter();
            $tickesFromTojmiasto = $filter->filterData($dateFromRest);
            return new JsonResponse($tickesFromTojmiasto);
        }
    }


    private function callApi($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}