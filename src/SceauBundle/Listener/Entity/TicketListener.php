<?php
namespace SceauBundle\Listener\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SceauBundle\Entity\Ticket;
use SceauBundle\Entity\TicketHistorique;
use SceauBundle\Entity\TicketHistoriqueEmail;

/**
 * Event listener to update entity Ticket when a entity Ticket is update
 *
 */
class TicketListener
{
    const TICKET_CREATION                   = 1;
    const TICKET_REPONSE                    = 2;
    const TICKET_STATE_CHANGE               = 3;
    const TICKET_NOTE_CREATE                = 4; //note creation action;
    const TICKET_NOTE_UPDATE                = 5; //note update action;
    const TICKET_NOTE_DELETE                = 6; //note delete action;
    const TICKET_REAFECTATION_CATEGORIE     = 7;
    const TICKET_REAFECTATION_MODERATEUR    = 8;


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
                    $this->createTicketHistorique(self::TICKET_NOTE_CREATE, $ticket, $em);
                }
                else
                {
                    $this->createTicketHistorique(self::TICKET_NOTE_UPDATE, $ticket, $em);
                }
            }
            else
            {
                $this->createTicketHistorique(self::TICKET_NOTE_DELETE, $ticket, $em);
            }

        }elseif (isset($changes['etat'])) // if etat has changed
        {
            $this->createTicketHistorique(self::TICKET_STATE_CHANGE, $ticket, $em);
        }
    }

    /**
     * Create a TicketHistorique linked to a Ticket
     *
     * @param $action
     * @param $ticket
     * @param ticketHistoriqueEmail
     */
    private function createTicketHistorique($action, Ticket $ticket, $em, TicketHistoriqueEmail $ticketHistoriqueEmail = null)
    {
        $ticketHistorique = new TicketHistorique();
        
        switch ($action) {
            case TicketEvents::TICKET_CREATION:
                $ticketHistorique->setDescription("Création : envoi au service ".$ticket->getCategorielabel());
                break;
            case TicketEvents::TICKET_REPONSE:
                $ticketHistorique->setDescription("Nouvelle réponse au ticket : traité par ")
                    ->setHistoriqueEmail($ticketHistoriqueEmail);
                break;
            case TicketEvents::TICKET_STATE_CHANGE:
                $ticketHistorique->setDescription("Changement de statut : traité par ")
                    ->setComment($ticket->getEtat());
                break;
            case TicketEvents::TICKET_NOTE_CREATE:
                $ticketHistorique->setDescription("Ajout d'une note par ") //TODO remove fix. Must be replace by real user
                    ->setComment($ticket->getNote());
                break;
            case TicketEvents::TICKET_NOTE_UPDATE:
                $ticketHistorique->setDescription("Modification de la note par ")
                    ->setComment($ticket->getNote());
                break;
            case TicketEvents::TICKET_NOTE_DELETE:
                $ticketHistorique->setDescription("Suppression de la note par ")
                    ->setComment($ticket->getNote());
                break;
            case TicketEvents::TICKET_REAFECTATION_CATEGORIE:
                $ticketHistorique->setDescription("Réafectation catégorie : traité par ")
                    ->setComment($ticket->getCategorielabel());
                break;
            case TicketEvents::TICKET_REAFECTATION_MODERATEUR:
                $ticketHistorique->setDescription("Réafectation modérateur : traité par ")
                    ->setComment('nouveau modérateur');
                break;
        }

        $ticketHistorique
            ->setTicket($ticket)
            ->setUser(1) //TODO remove fix
        ;

        $em->persist($ticketHistorique);
        $em->flush();
    }

    private function createTicketEmailHistorique(Ticket $ticket, $formEmailData)
    {
        $em = $this->em;
        $ticketHistoriqueEmail = new TicketHistoriqueEmail();
        $ticketHistoriqueEmail->setMailFrom($formEmailData['expediteur']);
        $ticketHistoriqueEmail->setMailTo($ticket->getAuteur()->getEmail());
        $ticketHistoriqueEmail->setMailSubject($formEmailData['sujet']);
        $ticketHistoriqueEmail->setMailBody($formEmailData['message']);

        // if ($formEmailData['modeleType']) {
        //     $ticketHistoriqueEmail->setReponseModele();
        // }

        $em->persist($ticketHistoriqueEmail);
        $em->flush();   

        return $ticketHistoriqueEmail;
    }
}
