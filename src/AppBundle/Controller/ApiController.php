<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ticket;
use AppBundle\Utils\Filter;
use AppBundle\Utils\PrettyJsonResponse;
//use AppBundle\Utils\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

            $this->saveToDB($tickesFromTojmiasto);

            if ($request->get('format') == 'pretty') {

                return new PrettyJsonResponse($tickesFromTojmiasto,200,  array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'application/json'));
            }
            return new JsonResponse($tickesFromTojmiasto, 200,  array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'application/json'));

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

//    private function showAction($id)
//    {
//        $ticket = $this->findAction($id);
//
//        if (!$ticket) {
//            throw $this->createNotFoundException(
//                'No ticket found for id '.$id
//            );
//        }
//
//        // ... do something, like pass the $product object into a template
//    }

}
