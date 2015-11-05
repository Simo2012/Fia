<?php

namespace SceauBundle\Service\Admin;

use Doctrine\ORM\EntityManager;
use SceauBundle\Entity\Ticket;
use SceauBundle\Entity\TicketHistorique;
use SceauBundle\Entity\TicketHistoriqueEmail;

class TicketHistoriqueService
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Create a TicketHistorique linked to a Ticket
     *
     * @param $action
     * @param $ticket
     * @param ticketHistoriqueEmail
     */
    public function createTicketHistorique($action, Ticket $ticket)
    {
        $em = $this->em;
        $ticketHistorique = new TicketHistorique();
        
        switch ($action) {
            case TicketHistorique::TICKET_CREATION:
                $ticketHistorique->setDescription("Création : envoi au service ".$ticket->getCategorielabel());
                break;
            case TicketHistorique::TICKET_STATE_CHANGE:
                $ticketHistorique->setDescription("Changement de statut : traité par ")
                    ->setComment($ticket->getEtat());
                break;
            case TicketHistorique::TICKET_NOTE_CREATE:
                $ticketHistorique->setDescription("Ajout d'une note par ") //TODO remove fix. Must be replace by real user
                    ->setComment($ticket->getNote());
                break;
            case TicketHistorique::TICKET_NOTE_UPDATE:
                $ticketHistorique->setDescription("Modification de la note par ")
                    ->setComment($ticket->getNote());
                break;
            case TicketHistorique::TICKET_NOTE_DELETE:
                $ticketHistorique->setDescription("Suppression de la note par ")
                    ->setComment($ticket->getNote());
                break;
            case TicketHistorique::TICKET_REAFECTATION_CATEGORIE:
                $ticketHistorique->setDescription("Réafectation catégorie : traité par ")
                    ->setComment($ticket->getCategorielabel());
                break;
            case TicketHistorique::TICKET_REAFECTATION_MODERATEUR:
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

    public function createTicketEmailHistorique(Ticket $ticket, TicketHistoriqueEmail $ticketHistoriqueEmail)
    {
        $em = $this->em;
        $ticketHistorique = new TicketHistorique();

        $ticketHistorique->setDescription("Nouvelle réponse au ticket : traité par ")
            ->setTicketHistoriqueEmail($ticketHistoriqueEmail)
            ->setTicket($ticket)
            ->setUser(1);

        $em->persist($ticketHistorique);
        $em->flush();
    }
}
