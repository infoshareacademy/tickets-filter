<?php

namespace AppBundle\Controller;

use AppBundle\Utils\Filter;
use AppBundle\Utils\UpdateTable;
use AppBundle\Utils\PrettyJsonResponse;
use AppBundle\Utils\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpProcess;


class ApiController extends Controller
{
    /**
     * @Route("/")
     */
    public function importAction(Request $request)
    {
        $result = $this->callApi('GET', 'http://test.tickets-collector.infoshareaca.nazwa.pl/web/index.php/tickets');
        $dateFromRest = json_decode($result);
        if ($dateFromRest == null) {
            return new JsonResponse(['error' => 'Tickets not found']);
        }
        else {
            $filter = new Filter();
            $tickesFromTojmiasto = $filter->filterData($dateFromRest);

            if ($request->get('format') == 'pretty') {

                return new PrettyJsonResponse($tickesFromTojmiasto);
            }
            return new JsonResponse($tickesFromTojmiasto);
        }
    }


    /**
     * @Route("/update")
     */
    public function updateAction(Request $request)
    {

        $response = new Response(
            'ok',
            Response::HTTP_OK,
            array('content-type' => 'text/html')
        );

        ob_start();
        // do initial processing here
        echo $response; // send the response
        header('Connection: close');
        header('Content-Length: '.ob_get_length());
        ob_end_flush();
        ob_flush();
        flush();


        $result = $this->callApi('GET', 'http://test.tickets-collector.infoshareaca.nazwa.pl/web/index.php/tickets');
        $dateFromRest = json_decode($result);
        if ($dateFromRest == null) {
            return new Response('Error occurred', Response::HTTP_BAD_GATEWAY);
        }
        else {
            $filter = new Filter();
            $tickesFromTojmiasto = $filter->filterData($dateFromRest);

            $logFile = fopen("apilog.txt", "w") or die("Unable to open file!");


            fwrite($logFile, serialize($tickesFromTojmiasto));
            fclose($logFile);
            return new Response();
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
