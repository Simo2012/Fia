<?php
namespace SceauBundle\Listener\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SceauBundle\Entity\Ticket;
use SceauBundle\Entity\TicketHistorique;
use SceauBundle\Service\Admin\TicketHistoriqueService;

/**
 * Event listener to update entity Ticket when a entity Ticket is updated or created
 *
 */
class TicketListener
{
    public $ticketHistoriqueService;

    public function __construct(TicketHistoriqueService $ticketHistoriqueService)
    {
        $this->ticketHistoriqueService = $ticketHistoriqueService;
    }
    
    public function postPersist(Ticket $ticket, LifecycleEventArgs $args)
    {
        $this->ticketHistoriqueService->createTicketHistorique(TicketHistorique::TICKET_CREATION, $ticket);
    }

    public function postUpdate(Ticket $ticket, LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        $changes = $uow->getEntityChangeSet($ticket);

        //if note has changed
        if (isset($changes['note'])) {
            // if the new value of field note is not empty
            if (isset($changes['note'][1]) && $changes['note'][1]) {
                //if the actual note value before update is empty
                if (empty($changes['note'][0])) {
                    $this->ticketHistoriqueService->createTicketHistorique(TicketHistorique::TICKET_NOTE_CREATE, $ticket);
                } else {
                    $this->ticketHistoriqueService->createTicketHistorique(TicketHistorique::TICKET_NOTE_UPDATE, $ticket);
                }
            } else {
                $this->ticketHistoriqueService->createTicketHistorique(TicketHistorique::TICKET_NOTE_DELETE, $ticket);
            }
        }

        // if etat has changed
        if (isset($changes['etat'])) {
            $this->ticketHistoriqueService->createTicketHistorique(TicketHistorique::TICKET_STATE_CHANGE, $ticket);
        }

        // if categorie has changed
        if (isset($changes['categorie'])) {
            $this->ticketHistoriqueService->createTicketHistorique(TicketHistorique::TICKET_REAFECTATION_CATEGORIE, $ticket);
        }
    }
}
