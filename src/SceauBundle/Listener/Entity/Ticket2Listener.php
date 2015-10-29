<?php
namespace SceauBundle\Listener\Entity;

use SceauBundle\Listener\Entity\TicketEvent;

class Ticket2Listener
{
    public function ticketReponse(TicketEvent $event)
    {
        $ticket = $event->getTicket();
        $responseData = $event->getReponseData();

    }
}