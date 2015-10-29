<?php
namespace SceauBundle\Listener\Entity;

use SceauBundle\Listener\Entity\TicketEvent;

class Ticket2Listner
{
    public function ticketReponse(TicketEvent $event)
    {
        $test = $event->getTest();
        var_dump('in TicketSubscriber : ', $test);die;
    }
}