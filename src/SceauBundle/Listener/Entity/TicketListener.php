<?php
namespace SceauBundle\Listener\Entity;

use Doctrine\ORM\EntityManager;
use SceauBundle\Listener\Entity\TicketEvent;
use SceauBundle\Listener\Entity\TicketEvents;
use SceauBundle\Entity\Ticket;
use SceauBundle\Entity\TicketHistorique;
use SceauBundle\Entity\TicketCategorie;
use SceauBundle\Entity\TicketHistoriqueEmail;

class TicketListener
{
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

    public function ticketCreation(TicketEvent $event)
    {
        $this->createTicketHistorique(TicketEvents::TICKET_CREATION, $event->getTicket());
    }

    public function ticketReponse(TicketEvent $event)
    {
        $ticket = $event->getTicket();
        $data   = $event->getData();

        $ticketHistoriqueEmail = $this->createTicketEmailHistorique($ticket, $data);
        $this->createTicketHistorique(TicketEvents::TICKET_REPONSE, $ticket, $ticketHistoriqueEmail);
        
        if ($ticket->getCategorie()->getId() == TicketCategorie::TYPE_CONTACT) {
            $ticket->setEtat(true);
            $this->em->persist($ticket);
            $this->em->flush();
            $this->createTicketHistorique(TicketEvents::TICKET_STATE_CHANGE, $ticket);
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

    public function ticketReafectationModerateur(TicketEvent $event)
    {
        $this->createTicketHistorique(TicketEvents::TICKET_REAFECTATION_MODERATEUR, $event->getTicket());
    }


    public function createTicketHistorique($action, $ticket, $ticketHistoriqueEmail = null)
    {
    	$ticketHistorique = new TicketHistorique();
    	$em = $this->em;

        switch ($action) {
        	case TicketEvents::TICKET_CREATION:
                $ticketHistorique->setDescription("Création : envoi au service ".$ticket->getCategorie()->getlabel());
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
                	->setComment($ticket->getCategorie()->getLabel());
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

    public function createTicketEmailHistorique(Ticket $ticket, $formEmailData)
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