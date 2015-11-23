<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DbController extends Controller
{
    public function saveToDB (array $filteredTickets) {

        foreach ($filteredTickets as $filteredTicket) {

//            $ticketObject = $this->jsonToTicketObject($filteredTicket);

            $doubleTicket = $this->findAction($ticketObject);
            if (!$doubleTicket) {
                $this->persistAction();
            }
        };

    }

    private function jsonToTicketObject ($jsonTicket) {
        $ticket = new Ticket();
        $ticket->setTitle($jsonTicket->title);
        $ticket->setPrice($jsonTicket->price);
        $ticket->setauctionUrl($jsonTicket->auctionUrl);
        $ticket->setDescription($jsonTicket->description);
    }

    private function findAction() {
        $ticket = $this->getDoctrine()
            ->getRepository('AppBundle:Ticket')
            ->find($this->auctionUrl);
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

    public function showAction($id)
    {
        $ticket = $this->findAction($id);

        if (!$ticket) {
            throw $this->createNotFoundException(
                'No ticket found for id '.$id
            );
        }

        // ... do something, like pass the $product object into a template
    }
}