<?php
namespace SceauBundle\Listener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
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

    public function prePersist(Ticket $ticket, LifecycleEventArgs $event)
    {
        dump($event); exit;
    }

    public function preUpdate(Ticket $ticket, PreUpdateEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();

        if($ticket instanceof Ticket)
        {
            if($eventArgs->hasChangedField('note')) //if note has changed
            {
                if(!empty($eventArgs->getNewValue('note'))) // if the new value of field note is not empty
                {
                    if(empty($eventArgs->getOldValue('note'))) //if the actual note value before update is empty
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

        }else
        {
            return;
        }

    }

    private function createTicketHistorique($action,$ticket, $em )
    {
        $ticketHistorique = new TicketHistorique();
        $ticketHistorique->setDate(new \DateTime());

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

        $ticketHistorique->setComment("commentaire en dur")
            ->setTicket($ticket)
            ->setUser(1)
        ;
        $em->persist($ticketHistorique);
        $em->flush();
    }

}
