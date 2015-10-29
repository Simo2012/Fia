<?php
namespace SceauBundle\Listener\Entity;

use Symfony\Component\EventDispatcher\Event;
use SceauBundle\Entity\Ticket;

class TicketEvent extends Event
{
    protected $ticket;
    protected $data;

    public function __construct(Ticket $ticket,  $data = null)
    {
        $this->ticket = $ticket;
        $this->data = $data;
    }

    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }


    public function getTicket()
    {
        return $this->ticket;
    }

    public function setData($data)
    {
        $this->data = $data;
    }


    public function getData()
    {
        return $this->data;
    }
}