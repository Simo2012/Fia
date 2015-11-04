<?php
namespace SceauBundle\Listener\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SceauBundle\Entity\TicketReponse;
use SceauBundle\Entity\TicketHistorique;
use SceauBundle\Service\Admin\TicketHistoriqueService;

/**
 * Event listener to update entity Ticket when a entity TicketReponse is created
 *
 */
class TicketReponseListener
{
    public $ticketHistoriqueService;

    public function __construct(TicketHistoriqueService $ticketHistoriqueService)
    {
        $this->ticketHistoriqueService = $ticketHistoriqueService;
    }

    public function postPersist(TicketReponse $ticketReponse, LifecycleEventArgs $args)
    {
        $ticket = $ticketReponse->getTicket();
        $this->ticketHistoriqueService->createTicketEmailHistorique(TicketHistorique::TICKET_REPONSE, $ticketReponse);
    }
}
