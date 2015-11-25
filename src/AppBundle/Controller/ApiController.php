<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ticket;
use AppBundle\Utils\Filter;
use AppBundle\Utils\UpdateTable;
use AppBundle\Utils\PrettyJsonResponse;
//use AppBundle\Utils\Ticket;
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
        $ticketsFromDatabase = $this->getData();

        if ($request->get('format') == 'pretty') {
            return new PrettyJsonResponse($ticketsFromDatabase,200,  array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'application/json'));
        }
        return new JsonResponse($ticketsFromDatabase, 200,  array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'application/json'));
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
            $tickesFromTrojmiasto = $filter->filterData($dateFromRest);

            $this->resetData();
            $this->saveToDB($tickesFromTrojmiasto);

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

    private function saveToDB (array $filteredTickets) {

        foreach ($filteredTickets as $filteredTicket) {

            $ticketObject = $this->jsonToTicketObject($filteredTicket);

            $doubleTicket = $this->findAction($ticketObject);
            if (!$doubleTicket) {
                $this->persistAction($ticketObject);
            }
        };

    }

    private function jsonToTicketObject ($jsonTicket) {
        $ticket = new Ticket();
        $ticket->setTitle($jsonTicket->title);
        $ticket->setPrice($jsonTicket->price);
        $ticket->setauctionUrl($jsonTicket->auctionUrl);
        $ticket->setDescription($jsonTicket->description);

        return $ticket;
    }

    private function findAction($ticketObject) {
        $ticket = $this->getDoctrine()
            ->getRepository('AppBundle:Ticket')
            ->find($ticketObject->auctionUrl);
        if ($ticket) {
            return true;
        } else {
            return false;
        }
    }

    private function persistAction($jsonTicket)
    {
        $ticket = new Ticket();
        $ticket->setTitle($jsonTicket->title);
        $ticket->setPrice($jsonTicket->price);
        $ticket->setauctionUrl($jsonTicket->auctionUrl);
        $ticket->setDescription($jsonTicket->description);

        $em = $this->getDoctrine()->getManager();

        $em->persist($ticket);
        $em->flush();

        return new Response('DB was updated');
    }

    private function getData() {
        return $this->getDoctrine()
            ->getRepository('AppBundle:Ticket')
            ->findAll();
    }

    /**
     * removing all data in database
     */
    private function resetData() {
        $tickets = $this->getData();
        $em = $this->getDoctrine()->getManager();

        foreach ($tickets as $ticket) {
            $em->remove($ticket);
        }
        $em->flush();
    }

}
