<?php
namespace SceauBundle\Listener\Entity;

use Symfony\Component\EventDispatcher\Event;
use SceauBundle\Entity\Ticket;

class TicketEvent extends Event
{
    /** @var string */
    protected $ticket;
    protected $reponseData;


    public function __construct(Ticket $ticket,  $reponseData = null)
    {
        $this->ticket = $ticket;
        $this->reponseData = $reponseData;
    }


    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }


    public function getTicket()
    {
        return $this->ticket;
    }

    public function setReponseData($reponseData)
    {
        $this->reponseData = $reponseData;
    }


    public function getReponseData()
    {
        return $this->reponseData;
    }
}