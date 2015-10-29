<?php
namespace SceauBundle\Listener\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SceauBundle\Entity\Ticket;
use SceauBundle\Entity\TicketHistorique;

/**
 * Event listener to update entity Ticket when a entity Ticket is update
 *
 */
class TicketListener
{
    const NOTE_CREATE  = 1;  //note creation action;
    const NOTE_UPDATE  = 2;  //note update action;
    const NOTE_DELETE  = 3;  //note delete action;
    const STATE_UPDATE = 4;  //etat update action;

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

        }elseif (isset($changes['etat'])) // if etat has changed
        {
            $this->createTicketHistorique(self::STATE_UPDATE, $ticket, $em);
        }
    }

    /**
     * Create a TicketHistorique linked to a Ticket
     *
     * @param $action
     * @param $ticket
     * @param EntityManager $em
     */
    private function createTicketHistorique($action, $ticket, EntityManager $em)
    {
        $ticketHistorique = new TicketHistorique();

        switch ($action) {
            case self::NOTE_CREATE:
                $ticketHistorique->setDescription("Ajout d'une note par ") //TODO remove fix. Must be replace by real user
                    ->setComment($ticket->getNote());
                break;
            case self::NOTE_UPDATE:
                $ticketHistorique->setDescription("Modification de la note par ")
                    ->setComment($ticket->getNote());
                break;
            case self::NOTE_DELETE:
                $ticketHistorique->setDescription("Suppression de la note par ")
                    ->setComment($ticket->getNote());;
                break;
            case self::STATE_UPDATE:
                $ticketHistorique->setDescription("Changement de statut : traité par ");
                break;
        }

        $ticketHistorique
            ->setTicket($ticket)
            ->setUser(1) //TODO remove fix
        ;

        $em->persist($ticketHistorique);
        $em->flush();
    }
}
