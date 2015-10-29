<?php
namespace SceauBundle\Listener\Entity;

use SceauBundle\Listener\Entity\TicketEvent;
use SceauBundle\Listener\Entity\TicketEvents;

class Ticket2Listener
{

	//constantes historique



    public function ticketReponse(TicketEvent $event)
    {
        $ticket = $event->getTicket();
        $reponseFormData = $event->data();

        $this->ticketMailer->send($ticket, $reponseFormData);
    }

    public function ticketReafectaction(TicketEvent $event)
    {
    	$ticket = $event->getTicket();
        $reafectactionFormData = $event->data();
    }

    public function ticketNoteCreate(TicketEvent $event)
    {
    	$this->createTicketHistorique(TicketEvents::TICKET_NOTE_CREATE, $ticket);
    }

    public function ticketNoteUpdate(TicketEvent $event)
    {
    	$this->createTicketHistorique(TicketEvents::TICKET_NOTE_UPDATE, $ticket);
    }

    public function ticketNoteDelete(TicketEvent $event)
    {
    	$this->createTicketHistorique(TicketEvents::TICKET_NOTE_DELETE, $ticket);
    }


    public function createTicketHistorique($action, $ticket)
    {
    	$ticketHistorique = new TicketHistorique();

        switch ($action) {
            case TicketEvents::TICKET_NOTE_CREATE:
                $ticketHistorique->setDescription("Ajout d'une note par ") //TODO remove fix. Must be replace by real user
                    ->setComment($ticket->getNote());
                break;
            case TicketEvents::TICKET_NOTE_UPDATE:
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

        $this->em->persist($ticketHistorique);
        $this->em->flush();
    }


}