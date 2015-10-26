<?php
namespace SceauBundle\Listener\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SceauBundle\Entity\Ticket;
use SceauBundle\Entity\TicketHistorique;

/**
 * Event listener to update entity TicketHistorique when a entity Ticket is update
 *
 */
class TicketListener
{
    const NOTE_CREATE = "création de la note";
    const NOTE_UPDATE = "modification de la note";
    const NOTE_DELETE = "suppression de la note";

    public function postUpdate(Ticket $ticket, LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $changes = $uow->getEntityChangeSet($ticket);

        if (isset($changes['note'])) //if note has changed
        {
            if (isset($changes['note'][1]) && $changes['note'][1]) // if the new value of field note is not empty
            {
                if (empty($changes['note'][0])) //if the actual note value before update is empty
                {
                    $this->createTicketHistorique(self::NOTE_CREATE, $ticket, $em);
                }
                else
                {
                    $this->createTicketHistorique(self::NOTE_UPDATE, $ticket, $em);
                }
            }
            else
            {
                $this->createTicketHistorique(self::NOTE_DELETE, $ticket, $em);
            }

        }
    }

    private function createTicketHistorique($action, $ticket, EntityManager $em)
    {
        $ticketHistorique = new TicketHistorique();

        switch ($action) {
            case self::NOTE_CREATE:
                $ticketHistorique->setDescription("description en dur: creation de la note");
                break;
            case self::NOTE_UPDATE:
                $ticketHistorique->setDescription("description en dur: modification de la note");
                break;
            case self::NOTE_DELETE:
                $ticketHistorique->setDescription("description en dur: suppression de la note");
                break;
        }

        $ticketHistorique
            ->setComment("commentaire en dur")
            ->setTicket($ticket)
            ->setUser(1) //TODO remove fix
        ;

        $em->persist($ticketHistorique);
        $em->flush();
    }
}
