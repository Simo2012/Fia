<?php
namespace SceauBundle\Listener\Entity;

use SceauBundle\Listener\Entity\TicketEvent;
use SceauBundle\Listener\Entity\TicketEvents;
use Doctrine\ORM\EntityManager;
use SceauBundle\Entity\TicketHistorique;
use SceauBundle\Entity\TicketCategorie;

class TicketListener
{
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

    public function ticketReponse(TicketEvent $event)
    {
        $this->createTicketHistorique(TicketEvents::TICKET_REPONSE, $event->getTicket());

        if ($event->getTicket()->getCategorie()->getId() == TicketCategorie::TYPE_CONTACT) {
            $this->createTicketHistorique(TicketEvents::TICKET_STATE_CHANGE, $event->getTicket());
        }
    }

    public function ticketStateChange(TicketEvent $event)
    {
        $this->createTicketHistorique(TicketEvents::TICKET_STATE_CHANGE, $event->getTicket());
    }

    public function ticketNoteCreate(TicketEvent $event)
    {
    	$this->createTicketHistorique(TicketEvents::TICKET_NOTE_CREATE, $event->getTicket());
    }

    public function ticketNoteUpdate(TicketEvent $event)
    {
    	$this->createTicketHistorique(TicketEvents::TICKET_NOTE_UPDATE, $event->getTicket());
    }

    public function ticketNoteDelete(TicketEvent $event)
    {
    	$this->createTicketHistorique(TicketEvents::TICKET_NOTE_DELETE, $event->getTicket());
    }

    public function ticketReafectationCategorie(TicketEvent $event)
    {
        $this->createTicketHistorique(TicketEvents::TICKET_REAFECTATION_CATEGORIE, $event->getTicket());
    }

    public function ticketReafectationDestinataire(TicketEvent $event)
    {
        $this->createTicketHistorique(TicketEvents::TICKET_REAFECTATION_DESTNATAIRE, $event->getTicket());
    }


    public function createTicketHistorique($action, $ticket)
    {
    	$ticketHistorique = new TicketHistorique();
    	$em = $this->em;

        switch ($action) {
        	case TicketEvents::TICKET_REPONSE:
                $ticketHistorique->setDescription("Nouvelle réponse au ticket : traité par ");
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
                	->setComment($ticket->getCategorie()->getLabel());
                break;
            case TicketEvents::TICKET_REAFECTATION_DESTINATAIRE:
                $ticketHistorique->setDescription("Réafectation destinataire : traité par ")
                	->setComment('nouveau destinataire');
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